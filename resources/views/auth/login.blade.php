<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Biopharma CRM</title>
    <meta name="description" content="Connexion au CRM Terrain Biopharma Mauritanie">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #f0f4f9;
        }

        .login-left {
            flex: 1;
            background: linear-gradient(135deg, #0d1b2a 0%, #1a3a5c 60%, #1a6fbf 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 48px;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(0,184,148,.15) 0%, transparent 70%);
            bottom: -100px; right: -100px;
            border-radius: 50%;
        }
        .login-left::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(26,111,191,.2) 0%, transparent 70%);
            top: -50px; left: -50px;
            border-radius: 50%;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 48px;
            position: relative;
            z-index: 1;
        }

        .brand-logo .icon {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, #1a6fbf, #00b894);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px; color: #fff;
            box-shadow: 0 8px 24px rgba(0,184,148,.3);
        }

        .brand-logo .text h1 {
            font-size: 20px; font-weight: 800; color: #fff; line-height:1.2;
        }
        .brand-logo .text p {
            font-size: 12px; color: rgba(255,255,255,.5);
        }

        .left-content {
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .left-content h2 {
            font-size: 32px; font-weight: 800; color: #fff; margin-bottom: 16px;
            line-height: 1.3;
        }

        .left-content p {
            font-size: 15px; color: rgba(255,255,255,.65); max-width: 340px; line-height: 1.7;
            margin: 0 auto 40px;
        }

        .features {
            display: flex; flex-direction: column; gap: 14px; text-align: left; max-width: 300px;
        }

        .feature-item {
            display: flex; align-items: center; gap: 12px;
        }

        .feature-item .feat-icon {
            width: 36px; height: 36px;
            background: rgba(255,255,255,.1);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; color: #00b894;
            flex-shrink: 0;
        }

        .feature-item span {
            font-size: 13px; color: rgba(255,255,255,.75); font-weight: 500;
        }

        /* RIGHT PANEL */
        .login-right {
            width: 480px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
            background: #fff;
        }

        .login-form-wrapper {
            width: 100%;
            max-width: 380px;
        }

        .form-title {
            font-size: 26px; font-weight: 800; color: #1e293b; margin-bottom: 6px;
        }
        .form-subtitle {
            font-size: 14px; color: #64748b; margin-bottom: 36px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px 12px 44px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px; font-family: inherit;
            color: #1e293b;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
            background: #f8fafc;
        }

        .form-control:focus {
            border-color: #1a6fbf;
            box-shadow: 0 0 0 3px rgba(26,111,191,.12);
            background: #fff;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px; top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: #94a3b8;
        }

        .form-check {
            display: flex; align-items: center; gap: 8px;
        }
        .form-check input[type="checkbox"] {
            width: 16px; height: 16px; accent-color: #1a6fbf; cursor: pointer;
        }
        .form-check label {
            font-size: 13px; color: #64748b; cursor: pointer;
        }

        .form-actions {
            display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;
        }

        .forgot-link {
            font-size: 13px; color: #1a6fbf; text-decoration: none; font-weight: 500;
        }
        .forgot-link:hover { text-decoration: underline; }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #1a6fbf, #2980d9);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px; font-weight: 700; font-family: inherit;
            cursor: pointer;
            transition: all .2s;
            box-shadow: 0 4px 14px rgba(26,111,191,.35);
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #145a9e, #1a6fbf);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(26,111,191,.4);
        }

        .error-message {
            background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px;
            padding: 12px 14px; font-size: 13px; color: #dc2626;
            display: flex; align-items: center; gap: 8px; margin-bottom: 20px;
        }

        .divider {
            text-align: center; margin: 24px 0;
            position: relative;
        }
        .divider::before {
            content: '';
            position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: #e2e8f0;
        }
        .divider span {
            background: #fff; padding: 0 12px; font-size: 12px; color: #94a3b8; position: relative;
        }

        .test-accounts {
            background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 10px; padding: 16px;
        }
        .test-accounts h4 {
            font-size: 12px; font-weight: 700; color: #0369a1; margin-bottom: 10px;
            text-transform: uppercase; letter-spacing: .05em;
        }
        .test-account-item {
            display: flex; justify-content: space-between; padding: 4px 0;
            font-size: 12px; color: #475569; border-bottom: 1px solid #e0f2fe;
        }
        .test-account-item:last-child { border: none; }
        .test-account-item .role { color: #0284c7; font-weight: 600; }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            .login-left { display: none; }
            .login-right { width: 100%; min-height: 100vh; padding: 32px 24px; }
        }
    </style>
</head>
<body>

{{-- Left panel --}}
<div class="login-left">
    <div class="brand-logo">
        <div class="icon"><i class="bi bi-capsule-pill"></i></div>
        <div class="text">
            <h1>Biopharma</h1>
            <p>Mauritanie — CRM Terrain</p>
        </div>
    </div>
    <div class="left-content">
        <h2>Gérez votre terrain avec précision</h2>
        <p>Suivez les pharmacies, planifiez les visites et transformez vos prospects en clients actifs.</p>
        <div class="features">
            <div class="feature-item">
                <div class="feat-icon"><i class="bi bi-map-fill"></i></div>
                <span>7 zones de couverture à Nouakchott</span>
            </div>
            <div class="feature-item">
                <div class="feat-icon"><i class="bi bi-people-fill"></i></div>
                <span>Gestion des rôles (Admin, Agent, Commercial)</span>
            </div>
            <div class="feature-item">
                <div class="feat-icon"><i class="bi bi-bar-chart-fill"></i></div>
                <span>Tableaux de bord et rapports en temps réel</span>
            </div>
            <div class="feature-item">
                <div class="feat-icon"><i class="bi bi-phone-fill"></i></div>
                <span>Interface mobile-first pour les agents terrain</span>
            </div>
        </div>
    </div>
</div>

{{-- Right panel --}}
<div class="login-right">
    <div class="login-form-wrapper">
        <h2 class="form-title">Connexion</h2>
        <p class="form-subtitle">Entrez vos identifiants pour accéder à votre espace.</p>

        @if ($errors->any())
            <div class="error-message">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        @if (session('status'))
            <div style="background:#ecfdf5;border:1px solid #a7f3d0;border-radius:8px;padding:12px 14px;font-size:13px;color:#047857;margin-bottom:20px;display:flex;align-items:flex-start;gap:8px;">
                <i class="bi bi-check-circle-fill" style="flex-shrink:0;margin-top:2px;"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Adresse email</label>
                <div class="input-wrapper">
                    <i class="bi bi-envelope-fill input-icon"></i>
                    <input type="email" id="email" name="email" class="form-control"
                           value="{{ old('email') }}" placeholder="admin@biopharma.mr" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Mot de passe</label>
                <div class="input-wrapper">
                    <i class="bi bi-lock-fill input-icon"></i>
                    <input type="password" id="password" name="password" class="form-control"
                           placeholder="••••••••" required>
                </div>
            </div>

            <div class="form-actions">
                <div class="form-check">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Se souvenir de moi</label>
                </div>
                @if (Route::has('password.request'))
                    <a class="forgot-link" href="{{ route('password.request') }}">Mot de passe oublié ?</a>
                @endif
            </div>

            <button type="submit" class="btn-login" id="loginBtn">
                <i class="bi bi-box-arrow-in-right"></i>
                Connexion
            </button>
        </form>

        <div class="divider"><span>Comptes de démonstration</span></div>

        <div class="test-accounts">
            <h4>🔐 Comptes de test (mdp: password)</h4>
            <div class="test-account-item">
                <span>admin@biopharma.mr</span>
                <span class="role">Admin</span>
            </div>
            <div class="test-account-item">
                <span>superviseur@biopharma.mr</span>
                <span class="role">Superviseur</span>
            </div>
            <div class="test-account-item">
                <span>agent@biopharma.mr</span>
                <span class="role">Agent terrain</span>
            </div>
            <div class="test-account-item">
                <span>commercial@biopharma.mr</span>
                <span class="role">Commercial</span>
            </div>
        </div>
    </div>
</div>

</body>
</html>
