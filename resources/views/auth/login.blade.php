<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Connexion - Molka Moden</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --primary: #f53003;
            --background: #fdfdfc;
            --surface: #ffffff;
            --text: #1b1b18;
            --text-muted: #706f6c;
            --border: #e3e3e0;
        }

        body {
            margin: 0;
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, -apple-system, sans-serif;
            background-color: var(--background);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            min-height: 100dvh;
            padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);
            box-sizing: border-box;
        }

        .login-card {
            background: var(--surface);
            padding: 1.5rem;
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 400px;
            margin: 1rem;
        }
        @media (min-width: 640px) {
            .login-card { padding: 2.5rem; }
        }

        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo h1 {
            color: var(--primary);
            font-size: 2rem;
            margin: 0;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-muted);
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid var(--border);
            font-size: 1rem;
            box-sizing: border-box;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(245, 48, 3, 0.1);
        }

        .btn-login {
            width: 100%;
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.875rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.2s;
            margin-top: 1rem;
        }

        .btn-login:hover {
            opacity: 0.9;
        }

        .error-message {
            color: #ef4444;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        .footer {
            margin-top: 2rem;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.875rem;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="logo">
            <h1>molkamoden</h1>
            <p style="color: var(--text-muted); margin-top: 0.5rem;">Entrez le mot de passe pour accéder</p>
        </div>

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required autofocus placeholder="••••••••">
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-login">Se Connecter</button>
        </form>

        <div class="footer">
            &copy; {{ date('Y') }} Molka Moden. Tous droits réservés.
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>