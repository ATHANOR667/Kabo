---

##   

---

---

#     KABO : api de l'app de digitalisation du processus de recherche de garde malade

---

---

##     Installation des dépendances et démarrage du projet

---

### Integration des routes : 

Un guide complet de l'api est disponible dans le dossier readme

### Installation des dépendances :

1. Accédez au répertoire du projet via le terminal.
2. Exécutez la commande `composer install` pour installer les dépendances PHP.

### Configuration de l'environnement :

1. Dupliquez le fichier `.env.example` et renommez-le en `.env`.
2. Configurez la base de données dans le fichier `.env`.
3. Configurez également le SMTP pour l'envoi des mails dans le fichier `.env`.

### Génération de la clé d'application :
Exécutez la commande suivante pour générer une nouvelle clé d'application :

- **php artisan key:generate**
- les parametres de la bd si necessaire (sqlite par defaut)
- faites **php artisan migrate** pour migrer la BD
- faites **php artisan db:seed** pour initier les parametres par defaut
- faites **php artisan schedule:work** pour executer les taches chron  necessaires au bon fonctionnement du projet 





