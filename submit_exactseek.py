import urllib.request
import urllib.parse
import ssl

ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

url = "https://www.exactseek.com/cgi-bin/eadd-t-new.cgi"
data = urllib.parse.urlencode({
    "ID": "eseeksubmit",
    "url": "https://buyoramarketplace.com/",
    "name": "BUYORA Marketplace",
    "email": "support@buyoramarketplace.com",
    "agree": "Yes"
}).encode('utf-8')

headers = {
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    'Referer': 'https://www.exactseek.com/add.html',
    'Content-Type': 'application/x-www-form-urlencoded'
}

req = urllib.request.Request(url, data=data, headers=headers)
try:
    with urllib.request.urlopen(req, timeout=15, context=ctx) as resp:
        content = resp.read().decode('utf-8', errors='ignore')
        print(f"Status: {resp.status}")
        print("Response snippet:")
        print(content[:1000])
except Exception as e:
    print("Error:", e)
