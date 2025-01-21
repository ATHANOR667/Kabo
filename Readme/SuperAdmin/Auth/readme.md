
---

### ROUTES DU SUPERADMIN

LIEES A L'AUTHENTIFIATION ET A LA MODIFICATION DES IDENTIFIANTS

---
---
---

---
---

### Route `superadmin\login`

Cette route permet de récupérer le token de l'utilisateur.

- **Méthode HTTP**: `POST`
- **Endpoint**: `api/superadmin/login`

#### Paramètres de la requête:
- `password`: Le mot de passe de l'utilisateur.

#### Réponses :
- `200`: connexion réussie.
```json
{
    "status": 200,
    "message": "Connexion réussie.",
    "data": {
        "token": "6|2W0p21KnAP3pU2IN7hAfusWazpUukqy2Qbz0YqBB85a89e05"
    }
}
```
- `401`: Mot de passe incorrect
- `404`: User non trouvé
- `500`: Erreur interne

---
---
---

---
---

### Route `superadmin\logout`

Cette route permet de déconnecter l'utilisateur.

- **Méthode HTTP**: `DELETE`
- **Endpoint**: `api/superadmin/logout`

#### Paramètres de la requête:
- `token`: Le token de l'utilisateur.

#### Réponses :
- `200`: connexion réussie.
```json
{
    "status": 200,
    "message": "déconnexion réussie."
}
```
- `404`: User déja déconnecté

---
---
---

---
---

### Route `superadmin\default`

Cette route permet de savoir si l'admin a toujours le mot de passe par défaut.
Ceci conditionnera les options de gestion de compte une fois connecté 
Et est également une condition à l'update de mot de passe tout en étant déconnecté.

Default = true signifie oui (on propose de modifier les identifiants par défaut 
            c'est-à-dire donner un email et mettre son propre password)

Default = false signifie non (il peut mettre a jour son mot de passe ou l'email associé au compte)

- **Méthode HTTP**: `GET`
- **Endpoint**: `api/superadmin/default`

#### Paramètres de la requête:
- aucun

#### Réponses :
- `200`: succès.
```json
{
"status": 200,
"default": true
}
```
- `500`: Erreur interne

---
---
---

---
---

### Route `superadmin\otp-request`

Cette route permet de demander l'envoi d'un OTP (One-Time Password) à un email donné.

- **Méthode HTTP**: `POST`
- **Endpoint**: `api/superadmin/otp-request`


#### Header:

- Authorization : le token de l'utilisateur connecté

#### Paramètres de la requête:
- `email`: L'adresse e-mail de l'utilisateur.

#### Réponses :
- `200`: OTP envoyé avec succès.
````json
{
    "status": 200,
    "message": "E-mail envoyé avec succès."
}
````
- `500`: Erreur interne
- `400`: Erreur d'envoi ou problème d'adresse

---
---
---

---
---

### Route `superadmin\default-erase`

Cette route permet de remplacer les identifiants par défaut (email null et mot de passe par défaut).

- **Méthode HTTP**: `PATCH`
- **Endpoint**: `api/superadmin/default-erase`

#### Header:

- Authorization : le token de l'utilisateur connecté

#### Paramètres de la requête:
- `email`: L'adresse e-mail de l'utilisateur.
- `password`: Mot de passe.
- `otp`:otp a 4 chiffre précédement envoyé à l'email de l'user.

#### Réponses :
- `200`: OTP envoyé avec succès.
````json
{
    "status":200,
    "message":"Mot de passe et email mis à jour."}
````
- `500`: Erreur interne
- `400`: Erreur d'envoi ou problème d'adresse

---
---
---

---
---

### Route `superadmin\password-reset-while-dissconnected-init`

Cette route permet modifier son mot de passe sans etre au préalable connecté
(elle fonctionnera uniquement si default = false)
Car l'admin étant non connecté, on se base sur l'envoi d'Otp à l'adresse connue.
Si default = true, on ne peut pas envoyer d'Otp car il n'y a pas d'adresse connue.



- **Méthode HTTP**: `POST`
- **Endpoint**: `api/superadmin/password-reset-while-dissconnected-init`

#### Paramètres de la requête:
- Aucun

#### Réponses :
- `200`: OTP envoyé avec succès.
````json
{
    "status": 200,
    "message": "E-mail envoyé avec succès."
}
````
- `500`: Erreur interne
- `400`: Erreur d'envoi ou problème d'adresse

---
---
---

---
---

### Route `superadmin\password-reset-while-dissconnected-process`

Cette route donne suite a  **"superadmin\password-reset-while-dissconnected-init"**
- **Méthode HTTP**: `PATCH`
- **Endpoint**: `api/superadmin/password-reset-while-dissconnected-process`

#### Paramètres de la requête:
- `otp` : `précédemment envoyé a l'adresse connue`
- `password` : `nouveau mot de passe`

#### Réponses :
- `200`
````json
{
    "status":200,
    "message":"Mot de passe mis a jour avec succes. Veuillez vous reconnecter."
}
````
- `500`: Erreur interne

---
---
---

---
---

### Route `superadmin\password-reset-while-connected-init`

Cette route permet modifier son mot de passe sans etre au préalable connecté
(elle fonctionnera uniquement si default = false)
Si default = true, on ne peut pas envoyer d'Otp car il n'y a pas d'adresse connue.

- **Méthode HTTP**: `POST`
- **Endpoint**: `api/superadmin/password-reset-while-connected-init`

#### Header:

- Authorization : le token de l'utilisateur connecté


#### Paramètres de la requête:
- Aucun

#### Réponses :
- `200`: OTP envoyé avec succès.
````json
{
    "status": 200,
    "message": "E-mail envoyé avec succès."
}
````
- `500`: Erreur interne
- `400`: Erreur d'envoi ou problème d'adresse

---
---
---

---
---

### Route `superadmin\password-reset-while-connected-process`

Cette route doone suite a  **"superadmin\password-reset-while-connected-init"**
- **Méthode HTTP**: `PATCH`
- **Endpoint**: `api/superadmin/password-reset-while-connected-process`

#### Header:

- Authorization : le token de l'utilisateur connecté

#### Paramètres de la requête:
- `otp` : `précédemment envoyé a l'adresse connue`
- `password` : `nouveau mot de passe`

#### Réponses :
- `200`
````json
{
    "status":200,
    "message":"Mot de passe mis a jour avec succes. Veuillez vous reconnecter."
}
````
- `500`: Erreur interne


---
---
---

---
---

### Route `superadmin\email-reset-init`

Cette route doone suite a  **"superadmin\password-reset-while-connected-init"**
- **Méthode HTTP**: `POST`
- **Endpoint**: `api/superadmin/email-reset-init`

#### Header:

- Authorization : le token de l'utilisateur connecté

#### Paramètres de la requête:
- `email` : `la nouvelle adresse a attribuer au compte`
- `password` : `le mot de passe est nécessaire piur une telle opération`

#### Réponses :
- `200`
````json
{
    "status":200,
    "message":"Otp envoyé"
}
````
- `500`: Erreur interne

---
---
---

---
---
### Route `superadmin\email-reset-process`

Cette route doone suite a **"superadmin/email-reset-init"**
- **Méthode HTTP**: `PATCH`
- **Endpoint**: `api/superadmin/email-reset-process`


#### Header:

- Authorization : le token de l'utilisateur connecté

#### Paramètres de la requête:
- `otp` : `précédemment envoyé a l'adresse donnée par le user`

#### Réponses :
- `200`
````json
{
    "status":200,
    "message":"Email mis à jour avec succes. Veuillez vous reconnecter."
}
````
- `500`: Erreur interne

---
---

---

---
---

