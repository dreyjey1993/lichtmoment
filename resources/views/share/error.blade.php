<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fehler – Lichtmoment</title>
    <meta name="robots" content="noindex, nofollow">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #FAFAF8;
            color: #374151;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .error-card {
            text-align: center;
            max-width: 420px;
            width: 100%;
        }
        .error-icon {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            display: block;
        }
        .error-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.75rem;
            font-weight: 400;
            color: #1F2937;
            margin-bottom: 0.75rem;
        }
        .error-message {
            color: #6B7280;
            line-height: 1.6;
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }
        .error-actions {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            align-items: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-primary {
            background: #D4A843;
            color: white;
        }
        .btn-primary:hover {
            background: #C19A4E;
        }
        .btn-ghost {
            background: transparent;
            color: #6B7280;
            border: 1px solid #E5E7EB;
        }
        .btn-ghost:hover {
            border-color: #D4A843;
            color: #D4A843;
        }
        .brand {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.25rem;
            color: #D4A843;
            margin-bottom: 2rem;
            letter-spacing: 0.05em;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <p class="brand">Lichtmoment</p>

        @if(isset($expired) && $expired)
            <span class="error-icon">⏰</span>
            <h1 class="error-title">Link abgelaufen</h1>
            <p class="error-message">
                Dieser Galerie-Link ist leider nicht mehr gültig.
                @if(isset($expiredAt))
                    Er ist am {{ $expiredAt }} abgelaufen.
                @endif
            </p>
        @else
            <span class="error-icon">🔍</span>
            <h1 class="error-title">Seite nicht gefunden</h1>
            <p class="error-message">
                {{ $message ?? 'Dieser Link existiert nicht oder wurde entfernt.' }}
            </p>
        @endif

        <div class="error-actions">
            <a href="/" class="btn btn-primary">Zur Startseite</a>
            <a href="/" class="btn btn-ghost">Lichtmoment – Hochzeitsfotografie</a>
        </div>
    </div>
</body>
</html>