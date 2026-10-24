import urllib.request
import urllib.error
import ssl
import re

directories = [
    {"name": "DirectoryWorld", "url": "https://www.directoryworld.net/submit.php"},
    {"name": "FreePRProject", "url": "https://www.freeprproject.com/submit.php"},
    {"name": "GainWeb", "url": "https://www.gainweb.org/submit.php"},
    {"name": "AliveDirectory", "url": "https://www.alivedirectory.com/submit.php"},
    {"name": "PegasusDirectory", "url": "https://www.pegasusdirectory.com/submit.php"},
    {"name": "A1WebDirectory", "url": "https://www.a1webdirectory.org/submit.php"},
    {"name": "HighRankDirectory", "url": "https://www.highrankdirectory.com/submit.php"},
    {"name": "ProLinkDirectory", "url": "https://www.prolinkdirectory.com/submit.php"},
    {"name": "AddURLDirectory", "url": "https://www.addurldirectory.com/submit.php"},
    {"name": "MarketingInternetDirectory", "url": "https://www.marketinginternetdirectory.com/submit.php"},
    {"name": "Bedigan", "url": "https://www.bedigan.com/submit.php"},
    {"name": "OneWayTextLink", "url": "https://www.onewaytextlink.com/submit.php"},
    {"name": "SitePromotionDirectory", "url": "https://www.sitepromotiondirectory.com/submit.php"},
    {"name": "WebWorldDirectory", "url": "https://www.webworlddirectory.com/submit.php"},
    {"name": "FreeDirectoryCity", "url": "https://www.freedirectorycity.com/submit.php"},
    {"name": "Entireweb", "url": "https://www.entireweb.com/free_submission/"},
    {"name": "ExactSeek", "url": "https://www.exactseek.com/add.html"},
    {"name": "Jayde B2B", "url": "https://www.jayde.com/submit.html"}
]

ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

headers = {
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
}

print(f"Testing {len(directories)} directories...")
results = []
for d in directories:
    name = d["name"]
    url = d["url"]
    try:
        req = urllib.request.Request(url, headers=headers)
        with urllib.request.urlopen(req, timeout=10, context=ctx) as resp:
            status = resp.status
            html = resp.read().decode('utf-8', errors='ignore')
            has_form = '<form' in html.lower()
            has_reciprocal = 'reciprocal' in html.lower()
            has_captcha = 'captcha' in html.lower() or 'g-recaptcha' in html.lower()
            results.append({
                "name": name,
                "url": url,
                "status_code": status,
                "active": True,
                "has_form": has_form,
                "has_reciprocal": has_reciprocal,
                "has_captcha": has_captcha
            })
            print(f"[{status}] {name}: Form={has_form}, Reciprocal={has_reciprocal}, Captcha={has_captcha}")
    except urllib.error.HTTPError as e:
        print(f"[{e.code}] {name}: HTTPError")
        results.append({"name": name, "url": url, "status_code": e.code, "active": False, "error": str(e)})
    except Exception as e:
        print(f"[ERR] {name}: {str(e)[:50]}")
        results.append({"name": name, "url": url, "status_code": 0, "active": False, "error": str(e)[:50]})

print("\nSummary complete.")
