const https = require('https');

function check(url) {
  return new Promise((resolve) => {
    https.get(url, (res) => {
      let body = '';
      res.on('data', chunk => body += chunk);
      res.on('end', () => {
        const canonicalMatch = body.match(/<link rel=["']canonical["'] href=["']([^"']+)["']/i);
        const titleMatch = body.match(/<title>([^<]+)<\/title>/i);
        const textOnly = body.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, ' ')
                             .replace(/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/gi, ' ')
                             .replace(/<[^>]+>/g, ' ');
        const words = textOnly.split(/\s+/).filter(Boolean);

        resolve({
          url: url,
          statusCode: res.statusCode,
          location: res.headers.location || null,
          title: titleMatch ? titleMatch[1] : null,
          canonical: canonicalMatch ? canonicalMatch[1] : null,
          hasTrustBlock: body.includes('buyora-marketplace-trust'),
          wordCount: words.length
        });
      });
    }).on('error', err => resolve({ url, error: err.message }));
  });
}

async function test() {
  const urls = [
    'https://buyoramarketplace.com/',
    'https://buyoramarketplace.com/shop',
    'https://buyoramarketplace.com/contacts',
    'https://buyoramarketplace.com/blog',
    'https://buyoramarketplace.com/login',
    'https://buyoramarketplace.com/register',
    'https://buyoramarketplace.com/glowhouse/?category=1',
    'https://buyoramarketplace.com/glowhouse/makeup_kit',
    'https://buyoramarketplace.com/vendor/view'
  ];
  for (const u of urls) {
    const res = await check(u);
    console.log(JSON.stringify(res, null, 2));
  }
}
test();
