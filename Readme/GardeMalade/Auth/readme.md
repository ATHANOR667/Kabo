
---

### ROUTES DU GARDE MALADE

LIEES A LA DEMANDE D'INSCRIPTION L'AUTHENTIFIATION ET A LA MODIFICATION DES IDENTIFIANTS

---
---
---

---
---

### Route `sickguard\signin-init`

Cette route permet au sickguard de récupérer son otp d'inscription en donnant son email et son matricule.

- **Méthode HTTP**: `POST`
- **Endpoint**: `api/sickguard/signin-init`

#### Paramètres de la requête:
````json
{
    "email": "john@doe.com",
    "sexe": "M",
    "nom": "Doe",
    "prenom": "John",
    "telephone": "1234567890",
    "dateNaissance": "27-12-2000",
    "lieuNaissance": "Cameroun,Yaoundé",
    "pays": "Cameroun",
    "ville": "Yaoundé", 
    "quartier": "titi garage",
    "photoProfil": "null||fichier" ,
    "pieceIdentite": "null||fichier"
}
````

#### Réponses :
- `200`: matricule valide , otp envoyé.
```json
{
    "status": 200,
    "message": "E-mail envoyé avec succès."
}
```
-`400`
````json
{
    "status": 400,
    "message": "Votre demande a été rejetée."
}
````
---
````json
{
    "status": 400,
    "message": "Votre demande est encore en cours de traitement. Nous vous répondrons dans les plus bref délais."
}
````
- `500`: Erreur interne

---
---
---

---
---


### Route `sickguard\signin-process`

Cette route permet au sickguard de poursuivre sa demande d'inscription en donnant
l'otp précédemment envoyé à son adresse en son password.

- **Méthode HTTP**: `PATCH`
- **Endpoint**: `api/sickguard/signin-process`

#### Paramètres de la requête:
````json
{
    "otp" : 5102 ,
    "password" : "azerty"
}
````

#### Réponses :
- `200`: matricule valide , otp envoyé.
```json
{
    "status": 200,
    "message": "Votre demande d'inscription a bien été prise en compte."
}
```
-`401`
````json
{
    "status": 401,
    "message": "OTP incorrect ou expiré."
}
````
- `500`: Erreur interne

---
---
---

---
---



### Route `sickguard\login`

Cette route permet au sickguard de se connecter (recupérer son token).
- **Méthode HTTP**: `POST`
- **Endpoint**: `api/sickguard/login`

#### Paramètres de la requête:
````json
{
    "email" : "mdjiepmo@gmail.com",
    "password" : "azerty"
}
````

#### Réponses :
- `200`: matricule valide , otp envoyé.
```json
{
    "status": 200,
    "message": "Connexion réussie.",
    "data": {
        "token": "8|nINtdv9FwrRuQspI5SO2PT3Yxgp0yoyJTlAtSncte9d499ba",
        "sickguard": {
            "id": 1,
            "matricule": "01b9c005-bc27-490b-bcf0-02944f01c436",
            "nom": "John",
            "prenom": "Doe",
            "email": "mdjiepmo@gmail.com",
            "telephone": "677342134",
            "pays": "Cameroun",
            "ville": "Yaoundé",
            "quartier" : "mokolo",
            "photoProfil": "profiles/l3mp3Ab10OmhItNnzrj5DALK06GVWLlJaT3Gq53G.jpg",
            "pieceIdentite": null,
            "deleted_at": null
        }
    }
}
```
-`403`
````json
{
    "status": 403,
    "message": "Vos accèss ont été révoqués."
}
````
-`401`
````json
{
    "status": 401,
    "message": "Mot de passe incorrect."
}
````


-`404`
````json
{
    "status": 404,
    "message": "Adresse inconnue."
}
````

- `500`: Erreur interne

---
---
---

---
---



### Route `sickguard\logout`

Cette route permet au sickguard de se déconnecter.

- **Méthode HTTP**: `DELETE`
- **Endpoint**: `api/sickguard/logout`

#### Header:

- Authorization : le token du sickguard

#### Paramètres de la requête:

- aucun

#### Réponses :
- `200`: matricule valide , otp envoyé.
```json
{
    "status": 200,
    "message": "Déconnexion réussie."
}
```
- `500`: Erreur interne

---
---
---

---
---



### Route `sickguard\password-reset-while-dissconnected-init`

Cette route permet au sickguard de changer son mot de passe sans etre au préalable connecté.
Il fournira son adresse et si elle est reconnue, on lui envoie un otp.

- **Méthode HTTP**: `POST`
- **Endpoint**: `api/sickguard/password-reset-while-dissconnected-init`


#### Paramètres de la requête:

````json
{
    "email" : "mdjiepmo@gmail.com"
}
````

#### Réponses :
- `200`: email reconnu, otp envoyé.
```json
{
    "status": 200,
    "message": "Otp envoyé"
}
```

-`404`
````json
{
    "status": 404,
    "message": "Adresse inconnue."
}
````


- `500`: Erreur interne
````json
{
    "status": 500,
    "message": "Operation imposssible , compte sans adresse",
    "error": "Attempt to read property \"email\" on null"
}
````
---
---
---

---
---

### Route `sickguard\password-reset-while-dissconnected-process`

Cette route permet au sickguard de donner son nouveau mot de passe sous condition de validation de son Otp.

- **Méthode HTTP**: `PATCH`
- **Endpoint**: `api/sickguard/password-reset-while-dissconnected-process`


#### Paramètres de la requête:

````json
{
    "otp": 8534 ,
    "password": "azerty"
}
````

#### Réponses :
- `200`: otp valide , password changé.
```json
{
    "status": 200,
    "message": "Mot de passe mis a jour avec succes. Veuillez vous reconnecter."
}
```
- `500`: Erreur interne
- `404`: Erreur interne
````json
{
    "status": 404,
    "message": "Otp expiré ou incorrect."
}
````
---
---
---

---
---
### Route `sickguard\password-reset-while-connected-init`

Cette route permet au sickguard de se déconnecter.

- **Méthode HTTP**: `POST`
- **Endpoint**: `api/sickguard/password-reset-while-connected-init`

#### Header:

- Authorization : le token du sickguard

#### Paramètres de la requête:

- aucun

#### Réponses :
- `200`:
```json
{
    "status": 200,
    "message": "Otp envoyé"
}
```
- `500`: Erreur interne

---
---
---

---
---

### Route `sickguard\password-reset-while-connected-process`

Cette route permet au sickguard de se déconnecter.

- **Méthode HTTP**: `PATCH`
- **Endpoint**: `api/sickguard/password-reset-while-connected-process`

#### Header:

- Authorization : le token du sickguard

#### Paramètres de la requête:

````json
{
    "otp": 7643,
    "password" : "azerty"
}
````

#### Réponses :
- `200`:
```json
{
    "status": 200,
    "message": "Mot de passe mis a jour avec succes. Veuillez vous reconnecter."
}
```
-`401`
````json
{
    "status": 401,
    "message": "Otp incorrect."
}
````
- `500`: Erreur interne

---
---
---

---
---

### Route `sickguard\email-reset-init`

Cette route permet au sickguard d'initier le changement d'adresse.
Il doit etre connecté et donner sa nouvelle adresse et son actuel mot de passe.
On y enverra alors un otp.

- **Méthode HTTP**: `POST`
- **Endpoint**: `api/sickguard/email-reset-init`

#### Header:

- Authorization : le token du sickguard

#### Paramètres de la requête:

````json
{
    "email": "mdjiepmo@gmail.co",
    "password" : "azerty"
}
````

#### Réponses :
- `200`:
```json
{
    "status": 200,
    "message": "Otp envoyé avec succès."
}
```
- `500`: Erreur interne

---
---
---

---
---

### Route `sickguard\email-reset-process`

Cette route permet au sickguard de changer l'adresse associée au compte..

- **Méthode HTTP**: `PATCH`
- **Endpoint**: `api/sickguard/email-reset-process`

#### Header:

- Authorization : le token du sickguard

#### Paramètres de la requête:

````json
{
    "otp" : 9857
}
````

#### Réponses :
- `200`:
```json
{
    "status": 200,
    "message": "Email du compte modifié avec succes."
}
```
- `500`: Erreur interne

---
---
---

---
---
