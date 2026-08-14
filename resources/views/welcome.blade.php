<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="0;url={{ auth()->check() ? route('dashboard') : route('login') }}">
    <title>CAEI Mailing - Redirection</title>
    <script>
        window.location.href = "{{ auth()->check() ? route('dashboard') : route('login') }}";
    </script>
</head>
<body style="font-family: sans-serif; display: flex; height: 100vh; align-items: center; justify-content: center; background-color: #0f172a; color: #ffffff; margin: 0;">
    <div style="text-align: center;">
        <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 8px;">CAEI Mailing</h2>
        <p style="color: #94a3b8; font-size: 14px;">Redirection en cours vers l'application...</p>
        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" style="color: #38bdf8; text-decoration: underline; font-size: 13px; margin-top: 12px; display: inline-block;">Cliquez ici si la redirection ne s'effectue pas automatiquement</a>
    </div>
</body>
</html>
