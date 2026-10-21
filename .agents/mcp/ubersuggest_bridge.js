#!/usr/bin/env node
/**
 * Ubersuggest MCP Stdio Bridge
 * Bridges standard MCP stdio (Antigravity, Cursor, Claude) to Ubersuggest's Streamable HTTP MCP endpoint.
 */

const https = require('https');
const readline = require('readline');
const fs = require('fs');
const path = require('path');
const querystring = require('querystring');

const TOKEN_FILE = path.join(__dirname, 'ubersuggest_token.json');
const MCP_ENDPOINT = 'https://ubersuggest-mcp.neilpatelapi.com/mcp';
const TOKEN_ENDPOINT = 'https://ubersuggest-mcp.neilpatelapi.com/token';
const CLIENT_ID = 'ubersuggest-mcp';

let tokenData = loadToken();

function loadToken() {
  try {
    if (fs.existsSync(TOKEN_FILE)) {
      return JSON.parse(fs.readFileSync(TOKEN_FILE, 'utf8'));
    }
  } catch (err) {
    process.stderr.write(`[bridge] Error loading token: ${err.message}\n`);
  }
  return null;
}

function saveToken(data) {
  try {
    tokenData = { ...tokenData, ...data, saved_at: Date.now() };
    fs.writeFileSync(TOKEN_FILE, JSON.stringify(tokenData, null, 2), 'utf8');
  } catch (err) {
    process.stderr.write(`[bridge] Error saving token: ${err.message}\n`);
  }
}

function refreshToken() {
  return new Promise((resolve, reject) => {
    if (!tokenData || !tokenData.refresh_token) {
      return reject(new Error('No refresh token available'));
    }

    const postData = querystring.stringify({
      grant_type: 'refresh_token',
      client_id: CLIENT_ID,
      refresh_token: tokenData.refresh_token
    });

    const req = https.request(TOKEN_ENDPOINT, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'Content-Length': Buffer.byteLength(postData)
      }
    }, (res) => {
      let body = '';
      res.on('data', chunk => body += chunk);
      res.on('end', () => {
        try {
          const json = JSON.parse(body);
          if (json.access_token) {
            saveToken(json);
            resolve(json.access_token);
          } else {
            reject(new Error(`Refresh failed: ${body}`));
          }
        } catch (e) {
          reject(e);
        }
      });
    });

    req.on('error', reject);
    req.write(postData);
    req.end();
  });
}

function forwardRequest(payload, retry = true) {
  return new Promise((resolve, reject) => {
    if (!tokenData || !tokenData.access_token) {
      return reject(new Error('No access token available. Please authenticate first.'));
    }

    const data = JSON.stringify(payload);
    const req = https.request(MCP_ENDPOINT, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json, text/event-stream',
        'Authorization': `Bearer ${tokenData.access_token}`,
        'Content-Length': Buffer.byteLength(data)
      }
    }, (res) => {
      let body = '';
      res.on('data', chunk => body += chunk);
      res.on('end', async () => {
        if (res.statusCode === 401 && retry) {
          process.stderr.write('[bridge] Access token expired, refreshing...\n');
          try {
            await refreshToken();
            const retryRes = await forwardRequest(payload, false);
            return resolve(retryRes);
          } catch (refreshErr) {
            process.stderr.write(`[bridge] Token refresh failed: ${refreshErr.message}\n`);
          }
        }

        // Parse response (either SSE "data: {...}" or direct JSON)
        let responseJson = null;
        const lines = body.split('\n');
        for (const line of lines) {
          if (line.startsWith('data: ')) {
            try {
              responseJson = JSON.parse(line.slice(6).trim());
              break;
            } catch (_) {}
          }
        }

        if (!responseJson) {
          try {
            responseJson = JSON.parse(body.trim());
          } catch (_) {}
        }

        if (responseJson) {
          resolve(responseJson);
        } else {
          // If body is empty or non-JSON (e.g. notifications)
          resolve(null);
        }
      });
    });

    req.on('error', reject);
    req.write(data);
    req.end();
  });
}

const rl = readline.createInterface({
  input: process.stdin,
  output: process.stdout,
  terminal: false
});

rl.on('line', async (line) => {
  const trimmed = line.trim();
  if (!trimmed) return;

  let request;
  try {
    request = JSON.parse(trimmed);
  } catch (err) {
    process.stderr.write(`[bridge] Invalid JSON: ${err.message}\n`);
    return;
  }

  try {
    const response = await forwardRequest(request);
    if (response && request.id !== undefined) {
      process.stdout.write(JSON.stringify(response) + '\n');
    }
  } catch (err) {
    process.stderr.write(`[bridge] Request error: ${err.message}\n`);
    if (request.id !== undefined) {
      const errResponse = {
        jsonrpc: '2.0',
        id: request.id,
        error: {
          code: -32603,
          message: err.message
        }
      };
      process.stdout.write(JSON.stringify(errResponse) + '\n');
    }
  }
});

process.stderr.write('[bridge] Ubersuggest MCP Bridge initialized and ready.\n');
