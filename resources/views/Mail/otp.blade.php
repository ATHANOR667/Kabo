<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Mail</title>
    <!-- Inclure Bootstrap via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container" style="max-width: 600px; margin-top: 50px;">
    <div class="card">
        <div class="card-header text-center bg-primary text-white">
            <h3>Votre Code OTP</h3>
        </div>
        <div class="card-body">
            <p>Bonjour,</p>
            <p>Voici votre code OTP pour vérifier votre identité :</p>
            <h1 class="text-center text-primary" style="font-size: 50px; font-weight: bold;">
                {{ $otp }}
            </h1>
            <p class="text-center">Entrez ce code pour continuer votre processus de vérification.</p>
            <p>Cordialement,</p>
            <p>L'équipe de Kabo</p>
        </div>
    </div>
</div>
</body>
</html>
