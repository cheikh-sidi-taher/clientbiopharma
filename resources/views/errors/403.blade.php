<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès refusé — Biopharma CRM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0d1b2a 0%, #1a3a5c 50%, #1a6fbf 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: #fff;
            border-radius: 16px;
            padding: 40px 36px;
            max-width: 420px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0,0,0,.25);
        }
        .icon {
            width: 72px; height: 72px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 32px;
            color: #dc2626;
        }
        h1 { font-size: 22px; font-weight: 800; color: #1e293b; margin-bottom: 10px; }
        p { font-size: 14px; color: #64748b; line-height: 1.6; margin-bottom: 24px; }
        a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 22px;
            background: linear-gradient(135deg, #1a6fbf, #2980d9);
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            border-radius: 10px;
        }
        a:hover { opacity: .95; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon"><i class="bi bi-shield-lock-fill"></i></div>
        <h1>Accès refusé</h1>
        <p>Vous n’avez pas les droits nécessaires pour accéder à cette page. Contactez un administrateur si vous pensez qu’il s’agit d’une erreur.</p>
        @auth
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        @else
            <a href="{{ route('login') }}">
                <i class="bi bi-box-arrow-in-right"></i> Connexion
            </a>
        @endauth
    </div>
</body>
</html>
