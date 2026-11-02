<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connecting to Bank Alfalah Payment Gateway...</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .redirect-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 40px;
            max-width: 480px;
            width: 100%;
            text-align: center;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        }
        .brand-logos {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-bottom: 24px;
        }
        .brand-logos img {
            height: 42px;
            object-fit: contain;
        }
        .spinner {
            width: 48px;
            height: 48px;
            border: 4px solid #e2e8f0;
            border-top-color: #c8460a;
            border-radius: 50%;
            animation: spin 0.9s linear infinite;
            margin: 0 auto 20px auto;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        h2 {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 10px 0;
            color: #0f172a;
        }
        p {
            font-size: 14px;
            color: #64748b;
            margin: 0 0 24px 0;
            line-height: 1.5;
        }
        .btn-manual {
            display: inline-block;
            background: #c8460a;
            color: #ffffff;
            padding: 10px 22px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        .btn-manual:hover {
            background: #ad3b08;
        }
    </style>
</head>
<body>
    <div class="redirect-box">
        <div class="brand-logos">
            <img src="<?= base_url('assets/buyora/buyora-logo.png') ?>" alt="BUYORA">
        </div>
        <div class="spinner"></div>
        <h2>Transferring to Bank Alfalah</h2>
        <p>Please wait while we transfer you securely to Bank Alfalah Alfa Payment Gateway to complete your order payment.</p>

        <form id="alfalah-form" method="POST" action="<?= htmlspecialchars($gateway_url, ENT_QUOTES, 'UTF-8') ?>">
            <?php foreach ($payload as $key => $val) { ?>
                <input type="hidden" name="<?= htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') ?>">
            <?php } ?>
            <button type="submit" class="btn-manual">Continue to Bank Alfalah</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var form = document.getElementById('alfalah-form');
                if (form) {
                    form.submit();
                }
            }, 600);
        });
    </script>
</body>
</html>

