<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Universal API - Bastivan Consulting</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e293b, #0f172a);
            color: #e2e8f0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
            background: rgba(30, 41, 59, 0.7);
            padding: 3rem;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            border: 1px solid #334155;
            backdrop-filter: blur(10px);
        }
        h1 {
            font-size: 3rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(to right, #38bdf8, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        p {
            font-size: 1.2rem;
            color: #94a3b8;
        }
        .badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #3b82f6;
            color: white;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: bold;
            margin-top: 1.5rem;
        }
        .footer {
            margin-top: 2rem;
            font-size: 0.8rem;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Universal API</h1>
        <p>A High-Performance PHP 8.4 Foundation</p>
        <div class="badge">Developed by Bastivan Consulting</div>

        <div style="margin-top: 2rem;">
            <code>GET /status</code> to see the API in action.
        </div>
    </div>
    <div class="footer">
        &copy; <?php echo date('Y'); ?> Bastivan Consulting. All rights reserved.
    </div>
</body>
</html>
