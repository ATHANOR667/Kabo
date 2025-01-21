
---

### ROUTES DE L'ADMIN

LIEES A L'AUTHENTIFIATION ET A LA MODIFICATION DES IDENTIFIANTS

---
---
---

---
---

### Route `admin\signin-init`

Cette route permet à l'admin de récupérer son otp d'inscription en donnant son email et son matricule.

- **Méthode HTTP**: `POST`
- **Endpoint**: `api/admin/signin-init`

#### Paramètres de la requête:
````json
{
    "matricule" : "01b9c005-bc27-490b-bcf0-02944f01c436" ,
    "email" : "mdjiepmo@gmail.com"
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
    "message": "Vous êtes déjà inscrit."
}
````
- `500`: Erreur interne

---
---
---

---
---


### Route `admin\signin-process`

Cette route permet à l'admin de poursuivre son inscription en donnant
       l'otp précédemment envoyé à son adresse en son password.

- **Méthode HTTP**: `PATCH`
- **Endpoint**: `api/admin/signin-process`

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
    "message": "Mot de passe et email mis à jour."
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



### Route `admin\login`

Cette route permet à l'admin de se connecter (recupérer son token).
- **Méthode HTTP**: `POST`
- **Endpoint**: `api/admin/login`

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
        "admin": {
            "id": 1,
            "matricule": "01b9c005-bc27-490b-bcf0-02944f01c436",
            "nom": "John",
            "prenom": "Doe",
            "email": "mdjiepmo@gmail.com",
            "telephone": "677342134",
            "pays": "Cameroun",
            "ville": "Yaoundé",
            "photoProfil": "profiles/l3mp3Ab10OmhItNnzrj5DALK06GVWLlJaT3Gq53G.jpg",
            "pieceIdentite": null,
            "deleted_at": null
        }
    }
}
```
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



### Route `admin\logout`

Cette route permet à l'admin de se déconnecter.

- **Méthode HTTP**: `DELETE`
- **Endpoint**: `api/admin/logout`

#### Header:

- Authorization : le token de l'admin 

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



### Route `admin\password-reset-while-dissconnected-init`

Cette route permet à l'admin de changer son mot de passe sans etre au préalable connecté.
Il fournira son adresse et si elle est reconnue, on lui envoie un otp.

- **Méthode HTTP**: `POST`
- **Endpoint**: `api/admin/password-reset-while-dissconnected-init`


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
- `500`: Erreur interne

-`404`
````json
{
    "status": 404,
    "message": "Adresse inconnue."
}
````
---
---
---

---
---

### Route `admin\password-reset-while-dissconnected-process`

Cette route permet à l'admin de donner son nouveau mot de passe sous condition de validation de son Otp.

- **Méthode HTTP**: `PATCH`
- **Endpoint**: `api/admin/password-reset-while-dissconnected-process`


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
### Route `admin\password-reset-while-connected-init`

Cette route permet à l'admin de se déconnecter.

- **Méthode HTTP**: `POST`
- **Endpoint**: `api/admin/password-reset-while-connected-init`

#### Header:

- Authorization : le token de l'admin

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

### Route `admin\password-reset-while-connected-process`

Cette route permet à l'admin de se déconnecter.

- **Méthode HTTP**: `PATCH`
- **Endpoint**: `api/admin/password-reset-while-connected-process`

#### Header:

- Authorization : le token de l'admin

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

### Route `admin\email-reset-init`

Cette route permet à l'admin d'initier le changement d'adresse.
Il doit etre connecté et donner sa nouvelle adresse et son actuel mot de passe.
On y enverra alors un otp.

- **Méthode HTTP**: `POST`
- **Endpoint**: `api/admin/email-reset-init`

#### Header:

- Authorization : le token de l'admin

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

### Route `admin\email-reset-process`

Cette route permet à l'admin de se déconnecter.

- **Méthode HTTP**: `PATCH`
- **Endpoint**: `api/admin/email-reset-process`

#### Header:

- Authorization : le token de l'admin

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
