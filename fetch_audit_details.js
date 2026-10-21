const https = require('https');
const fs = require('fs');
const path = require('path');

const tokenData = JSON.parse(fs.readFileSync(path.join(__dirname, '.agents/mcp/ubersuggest_token.json'), 'utf8'));

function callMcp(toolName, args) {
  return new Promise((resolve, reject) => {
    const postData = JSON.stringify({
      jsonrpc: '2.0',
      id: Date.now(),
      method: 'tools/call',
      params: {
        name: toolName,
        arguments: args
      }
    });

    const req = https.request('https://ubersuggest-mcp.neilpatelapi.com/mcp', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json, text/event-stream',
        'Authorization': `Bearer ${tokenData.access_token}`,
        'Content-Length': Buffer.byteLength(postData)
      }
    }, (res) => {
      let body = '';
      res.on('data', chunk => body += chunk);
      res.on('end', () => {
        try {
          const lines = body.split('\n');
          for (const line of lines) {
            if (line.startsWith('data: ')) {
              return resolve(JSON.parse(line.slice(6).trim()));
            }
          }
          resolve(JSON.parse(body.trim()));
        } catch (e) {
          resolve({ raw: body });
        }
      });
    });

    req.on('error', reject);
    req.write(postData);
    req.end();
  });
}

async function main() {
  const issues = [
    'response_code_4xx',
    'have_broken_links_anchor',
    'duplicate_meta_descriptions',
    'have_title_duplicates',
    'seo_non_friendly_url',
    'title_short',
    'meta_description_empty'
  ];

  const results = {};
  for (const issue of issues) {
    console.log(`Fetching issue: ${issue}...`);
    try {
      const res = await callMcp('site_audit_results', {
        domain: 'buyoramarketplace.com',
        issue: issue
      });
      results[issue] = res;
    } catch (err) {
      results[issue] = { error: err.message };
    }
  }

  fs.writeFileSync('audit_issues_breakdown.json', JSON.stringify(results, null, 2), 'utf8');
  console.log('Finished writing audit_issues_breakdown.json');
}

main();
