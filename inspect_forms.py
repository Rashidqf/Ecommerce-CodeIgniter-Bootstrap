import urllib.request
import urllib.error
import ssl
from bs4 import BeautifulSoup
import re

ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

headers = {
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
}

for target in ["https://www.gainweb.org/submit.php", "https://www.alivedirectory.com/submit.php", "https://www.exactseek.com/add.html"]:
    print(f"\n====================\nInspecting {target}...")
    try:
        req = urllib.request.Request(target, headers=headers)
        with urllib.request.urlopen(req, timeout=10, context=ctx) as resp:
            html = resp.read().decode('utf-8', errors='ignore')
            # Extract form
            forms = re.findall(r'<form[\s\S]*?</form>', html, re.IGNORECASE)
            print(f"Found {len(forms)} forms.")
            for i, f in enumerate(forms):
                print(f"--- Form {i+1} ---")
                action_match = re.search(r'action=[\'"](.*?)[\'"]', f, re.IGNORECASE)
                method_match = re.search(r'method=[\'"](.*?)[\'"]', f, re.IGNORECASE)
                print("Action:", action_match.group(1) if action_match else "N/A")
                print("Method:", method_match.group(1) if method_match else "GET")
                inputs = re.findall(r'<(?:input|textarea|select)[\s\S]*?>', f, re.IGNORECASE)
                for inp in inputs:
                    name_match = re.search(r'name=[\'"](.*?)[\'"]', inp, re.IGNORECASE)
                    type_match = re.search(r'type=[\'"](.*?)[\'"]', inp, re.IGNORECASE)
                    val_match = re.search(r'value=[\'"](.*?)[\'"]', inp, re.IGNORECASE)
                    print(f"  Field: name={name_match.group(1) if name_match else 'N/A'}, type={type_match.group(1) if type_match else 'text'}, val={val_match.group(1) if val_match else ''}")
    except Exception as e:
        print("Error:", e)
