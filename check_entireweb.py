import urllib.request
import ssl
import re

ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

req = urllib.request.Request("https://www.entireweb.com/free_submission/", headers={'User-Agent': 'Mozilla/5.0'})
try:
    with urllib.request.urlopen(req, timeout=10, context=ctx) as r:
        html = r.read().decode('utf-8', errors='ignore')
        print("HTML length:", len(html))
        # Find iframes or forms
        iframes = re.findall(r'<iframe[\s\S]*?>', html, re.IGNORECASE)
        print("Iframes:", iframes)
        forms = re.findall(r'<form[\s\S]*?</form>', html, re.IGNORECASE)
        print("Forms count:", len(forms))
        for f in forms:
            print(f[:300])
except Exception as e:
    print(e)
