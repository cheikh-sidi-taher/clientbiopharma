<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Biopharma CRM')</title>
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
            font-size: 28px; font-weight: 800; color: #fff; margin-bottom: 16px;
            line-height: 1.3;
        }

        .left-content p {
            font-size: 15px; color: rgba(255,255,255,.65); max-width: 340px; line-height: 1.7;
            margin: 0 auto;
        }

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
            font-size: 14px; color: #64748b; margin-bottom: 28px; line-height: 1.5;
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

        .success-message {
            background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px;
            padding: 12px 14px; font-size: 13px; color: #047857;
            display: flex; align-items: flex-start; gap: 8px; margin-bottom: 20px;
        }

        .auth-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #1a6fbf;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .auth-back:hover { text-decoration: underline; }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            .login-left { display: none; }
            .login-right { width: 100%; min-height: 100vh; padding: 32px 24px; }
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="login-left">
    <div class="brand-logo">
        <div class="icon"><i class="bi bi-capsule-pill"></i></div>
        <div class="text">
            <h1>Biopharma</h1>
            <p>Mauritanie — CRM Terrain</p>
        </div>
    </div>
    <div class="left-content">
        <h2>@yield('auth_heading', 'Sécurité du compte')</h2>
        <p>@yield('auth_lead', 'Accès réservé aux équipes terrain et à l\'administration.')</p>
    </div>
</div>

<div class="login-right">
    <div class="login-form-wrapper">
        @yield('content')
    </div>
</div>

</body>
</html>
