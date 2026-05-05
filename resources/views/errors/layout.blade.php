<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{config('app.name')}}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafb;
            overflow: hidden;
            position: relative;
        }
        .bg-pattern {
            position: fixed; inset: 0; z-index: 0;
            background:
                radial-gradient(circle at 20% 50%, rgba(26,154,90,0.06) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(22,163,74,0.04) 0%, transparent 50%),
                radial-gradient(circle at 50% 80%, rgba(16,185,129,0.05) 0%, transparent 50%);
        }
        .floating-shape {
            position: fixed; border-radius: 50%; opacity: 0.07;
            animation: floatShape 20s ease-in-out infinite;
        }
        .shape-1 { width: 300px; height: 300px; background: #1a9a5a; top: -80px; left: -80px; }
        .shape-2 { width: 200px; height: 200px; background: #38BDF8; bottom: -60px; right: -40px; animation-delay: -7s; }
        .shape-3 { width: 150px; height: 150px; background: #22c55e; top: 40%; right: 10%; animation-delay: -14s; }
        @keyframes floatShape {
            0%, 100% { transform: translate(0,0) rotate(0deg); }
            25% { transform: translate(20px,-30px) rotate(5deg); }
            50% { transform: translate(-10px,20px) rotate(-3deg); }
            75% { transform: translate(15px,10px) rotate(2deg); }
        }
        .error-container {
            position: relative; z-index: 1; text-align: center;
            padding: 40px 24px; max-width: 520px; width: 100%;
        }
        .error-code {
            font-size: 140px; font-weight: 800; line-height: 1;
            background: linear-gradient(135deg, #1a9a5a 0%, #38BDF8 50%, #6ee7b7 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
            margin-bottom: 8px;
            animation: codeEntry 0.8s cubic-bezier(0.34,1.56,0.64,1) forwards;
            opacity: 0; transform: scale(0.5);
            filter: drop-shadow(0 4px 20px rgba(26,154,90,0.15));
        }
        @keyframes codeEntry { to { opacity:1; transform:scale(1); } }
        .error-icon {
            width: 80px; height: 80px; margin: 0 auto 20px;
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border-radius: 24px; display: flex; align-items: center; justify-content: center;
            font-size: 36px;
            animation: iconBounce 0.6s 0.3s cubic-bezier(0.34,1.56,0.64,1) forwards;
            opacity: 0; transform: translateY(20px);
            box-shadow: 0 8px 32px rgba(26,154,90,0.1);
        }
        @keyframes iconBounce { to { opacity:1; transform:translateY(0); } }
        .error-title {
            font-size: 24px; font-weight: 700; color: #1a2332; margin-bottom: 12px;
            animation: fadeUp 0.6s 0.4s ease forwards; opacity: 0; transform: translateY(15px);
        }
        .error-desc {
            font-size: 15px; color: #64748b; line-height: 1.7; margin-bottom: 32px;
            animation: fadeUp 0.6s 0.5s ease forwards; opacity: 0; transform: translateY(15px);
        }
        @keyframes fadeUp { to { opacity:1; transform:translateY(0); } }
        .error-actions {
            display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;
            animation: fadeUp 0.6s 0.6s ease forwards; opacity: 0; transform: translateY(15px);
        }
        .btn-primary-err {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 14px 28px; background: linear-gradient(135deg, #1a9a5a, #16a34a);
            color: white; border-radius: 12px; text-decoration: none;
            font-weight: 600; font-size: 15px; border: none; cursor: pointer;
            box-shadow: 0 4px 14px rgba(26,154,90,0.3); transition: all 0.3s ease;
        }
        .btn-primary-err:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(26,154,90,0.4); color: white; text-decoration: none; }
        .btn-secondary-err {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 14px 28px; background: white; color: #374151;
            border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 15px;
            border: 1.5px solid #e5e7eb; cursor: pointer; transition: all 0.3s ease;
        }
        .btn-secondary-err:hover { transform: translateY(-2px); border-color: #1a9a5a; color: #1a9a5a; text-decoration: none; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }

        /* Redirect countdown */
        .redirect-bar {
            margin-top: 28px;
            animation: fadeUp 0.6s 0.8s ease forwards;
            opacity: 0; transform: translateY(15px);
        }
        .redirect-text {
            font-size: 13px; color: #94a3b8; margin-bottom: 8px;
        }
        .redirect-text span { color: #1a9a5a; font-weight: 700; font-size: 15px; }
        .progress-track {
            width: 200px; height: 4px; background: #e5e7eb;
            border-radius: 4px; margin: 0 auto; overflow: hidden;
        }
        .progress-fill {
            height: 100%; width: 100%; border-radius: 4px;
            background: linear-gradient(90deg, #1a9a5a, #38BDF8);
            transform-origin: left;
            animation: progressShrink var(--duration) linear forwards;
        }
        @keyframes progressShrink { from { transform: scaleX(1); } to { transform: scaleX(0); } }

        .error-footer {
            margin-top: 48px; font-size: 13px; color: #94a3b8;
            animation: fadeUp 0.6s 0.7s ease forwards; opacity: 0;
        }
        .error-footer a { color: #1a9a5a; text-decoration: none; font-weight: 500; }

        .glitch { position: relative; }
        .glitch::before, .glitch::after {
            content: attr(data-text); position: absolute; left: 0; right: 0; background: #f8fafb; overflow: hidden;
        }
        .glitch::before { animation: g1 2s infinite linear alternate-reverse; clip-path: polygon(0 0,100% 0,100% 35%,0 35%); -webkit-text-fill-color: #ef4444; }
        .glitch::after { animation: g2 3s infinite linear alternate-reverse; clip-path: polygon(0 65%,100% 65%,100% 100%,0 100%); -webkit-text-fill-color: #3b82f6; }
        @keyframes g1 { 0%{transform:translate(0)}20%{transform:translate(-3px,3px)}40%{transform:translate(3px,-2px)}60%{transform:translate(-2px,1px)}80%{transform:translate(1px,-3px)}100%{transform:translate(0)} }
        @keyframes g2 { 0%{transform:translate(0)}20%{transform:translate(3px,-1px)}40%{transform:translate(-2px,3px)}60%{transform:translate(1px,-2px)}80%{transform:translate(-3px,2px)}100%{transform:translate(0)} }

        @media (max-width: 480px) {
            .error-code { font-size: 100px; }
            .error-title { font-size: 20px; }
            .error-actions { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="bg-pattern"></div>
    <div class="floating-shape shape-1"></div>
    <div class="floating-shape shape-2"></div>
    <div class="floating-shape shape-3"></div>

    <div class="error-container">
        @yield('content')

        <div class="error-footer">
            {{config('app.name')}} &copy; {{ date('Y') }} &mdash;
            <a href="mailto:support@replai.id">Butuh bantuan?</a>
        </div>
    </div>

    <script>
    // Auto-redirect with countdown
    (function() {
        var el = document.getElementById('redirectCountdown');
        if (!el) return;
        var seconds = parseInt(el.getAttribute('data-seconds'));
        var url = el.getAttribute('data-url');
        var counter = document.getElementById('countNumber');
        var interval = setInterval(function() {
            seconds--;
            if (counter) counter.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(interval);
                window.location.href = url;
            }
        }, 1000);
    })();
    </script>
</body>
</html>
