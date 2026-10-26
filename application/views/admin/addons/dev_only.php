<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Addons - Development Purpose Only</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #0f172a;
            padding: 24px;
        }
        .dev-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 18px 50px rgba(0, 0, 0, .35);
            max-width: 440px;
            width: 100%;
            padding: 42px 34px;
            text-align: center;
        }
        .dev-icon {
            width: 74px;
            height: 74px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: #eef2ff;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .dev-icon svg { width: 34px; height: 34px; stroke: #7367f0; }
        .dev-card h1 { font-size: 22px; color: #0f172a; margin-bottom: 8px; }
        .dev-card p { font-size: 15px; color: #64748b; line-height: 1.6; margin-bottom: 26px; }
        .dev-badge {
            display: inline-block;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .5px;
            text-transform: uppercase;
            color: #7367f0;
            background: #eef2ff;
            border: 1px solid #d6dcfd;
            border-radius: 999px;
            padding: 6px 14px;
            margin-bottom: 18px;
        }
        .dev-btn {
            display: inline-block;
            background: #7367f0;
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            padding: 11px 26px;
            transition: background .15s ease;
        }
        .dev-btn:hover { background: #5f54e0; color: #fff; }
    </style>
</head>
<body>
    <div class="dev-card">
        <div class="dev-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 9v4"/><path d="M12 16h.01"/><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
            </svg>
        </div>
        <span class="dev-badge">Development Purpose Only</span>
        <h1>Addons Section</h1>
        <p>This section is only for development purpose. It is not available for general use.</p>
        <a class="dev-btn" href="<?php echo site_url('admin/admin/dashboard'); ?>">Back to Dashboard</a>
    </div>
</body>
</html>
