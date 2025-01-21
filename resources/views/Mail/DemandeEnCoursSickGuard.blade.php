<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande d'inscription en cours</title>
    <!-- Inclure Bootstrap via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="container" style="max-width: 600px; margin-top: 50px;">
    <div class="card shadow">
        <div class="card-header text-center bg-warning text-white">
            <h3>Demande d'inscription en cours</h3>
        </div>
        <div class="card-body">
            <p>Bonjour cher(e) {{$nom}} {{$prenom}}</p>
            <p>Nous avons bien reçu votre demande d'inscription et nous vous informons que le traitement est en cours.</p>
            <p>Votre demande sera examinée dans les plus brefs délais. Nous vous tiendrons informé de l'avancement du processus.</p>
            <h4 class="text-center text-warning" style="font-size: 30px; font-weight: bold;">
                Merci pour votre patience !
            </h4>
            <p class="text-center" style="font-size: 18px;">Nous vous contacterons dès que possible pour vous informer de la suite.</p>
            <hr>
            <p>Cordialement,</p>
            <p>L'équipe de Kabo</p>
        </div>
    </div>
</div>
</body>

</html>
