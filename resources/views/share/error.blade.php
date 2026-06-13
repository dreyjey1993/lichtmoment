<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fehler – Lichtmoment</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400&family=Inter:wght@300;400&display=swap" rel="stylesheet">
</head>
<body class="bg-offwhite flex items-center justify-center min-h-screen px-6">
    <div class="text-center max-w-md">
        <p class="text-6xl text-gray-200 mb-4">
            @if(str_contains($message, '⏰'))⏰@else🔒@endif
        </p>
        <p class="text-gray-500 text-lg">{{ $message }}</p>
    </div>
</body>
</html>
