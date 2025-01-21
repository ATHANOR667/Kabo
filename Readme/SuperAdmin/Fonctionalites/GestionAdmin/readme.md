
---

### ROUTES DU SUPERADMIN

LIEES AUX FONCTIONNALITES DE GESTION DES ADMINS





---
---
---

---
---



### Route `superadmin/admin-create`

Cette route permet de créer un admin.

- **Méthode HTTP**: `POST`
- **Endpoint**: `api/superadmin/admin-create`

#### Header:

- Authorization : le token de l'utilisateur connecté

#### Paramètres de la requête:

````json
{
  "nom": "Doe",
  "prenom": "John",
  "telephone": "1234567890",
  "pays": "Cameroun",
  "ville": "Yaoundé",
  "photoProfil": "null||fichier" ,
  "pieceIdentite": "null||fichier"
}
````

#### Réponses :
- `200`:
```json
{
    "status":"200",
    "admins":
        [
            {
                "nom":"John",
                "prenom":"Doe",
                "email": null,
                "telephone":"677342134",
                "pays":"Cameroun",
                "ville":"Yaound\u00e9",
                "photoProfil":"profiles\/GtPRkHWohBHClx4smbYZLf2tx8kcpmsT81v5nV9r.jpg",
                "pieceIdentite":null,
                "deleted_at":null
            }
        ]
}
```
- `422`: Erreur de validation
- `500`: Erreur interne



---
---
---

---
---


### Route `superadmin/admin-list`

Cette route permet de récupérer la liste des admins.

- **Méthode HTTP**: `GET`
- **Endpoint**: `api/superadmin/admin-list`

#### Header:

- Authorization : le token de l'utilisateur connecté

#### Paramètres de la requête:

- aucun

#### Réponses :
- `200`:  
```json
{
    "status": "200",
    "admins": [
        {
            "id": 1,
            "matricule": "01b9c005-bc27-490b-bcf0-02944f01c436",
            "nom": "John",
            "prenom": "Doe",
            "email": null,
            "telephone": "677342134",
            "pays": "Cameroun",
            "ville": "Yaoundé",
            "photoProfil": "profiles/l3mp3Ab10OmhItNnzrj5DALK06GVWLlJaT3Gq53G.jpg",
            "pieceIdentite": null,
            "deleted_at": null
        },
        {
            "id": 2,
            "matricule": "170f2634-beb2-4c3b-bb93-725688594a04",
            "nom": "John",
            "prenom": "Doe",
            "email": null,
            "telephone": "677342134",
            "pays": "Cameroun",
            "ville": "Yaoundé",
            "photoProfil": "profiles/PRakhI5iTmrDSyWjCYw1NgcP4lyq14vgwO0WvO8Z.jpg",
            "pieceIdentite": null,
            "deleted_at": null
        }
    ],
    "admins_deleted": [
        {
            "id": 3,
            "matricule": "96a2538b-0db9-4776-8f4d-9f006d3323fc",
            "nom": "John",
            "prenom": "Doe",
            "email": null,
            "telephone": "677342134",
            "pays": "Cameroun",
            "ville": "Yaoundé",
            "photoProfil": "profiles/dCAQSx2paiNlUPVrNedZg2tOJZ44UHNBApdxvPW1.jpg",
            "pieceIdentite": null,
            "deleted_at": "2025-01-17T14:36:35.000000Z"
        }
    ]
}
```
- `500`: Erreur interne

---
---
---

---
---



### Route `superadmin/admin-edit`

Cette route permet d'éditer un admin.

- **Méthode HTTP**: `PATCH`
- **Endpoint**: `api/superadmin/admin-edit`

#### Header:

- Authorization : le token de l'utilisateur connecté

#### Paramètres de la requête:

````json
{
    "id" : 3 ,
    "nom" : "azerty"

}
````

#### Réponses :
- `200`:
```json
{
    "status": 201,
    "message ": "Admin mis a jour avec succes.",
    "admin": {
        "id": 3,
        "nom": "azerty",
        "prenom": "Doe",
        "email": null,
        "telephone": "677342134",
        "pays": "Cameroun",
        "ville": "Yaoundé",
        "photoProfil": "profiles/GtPRkHWohBHClx4smbYZLf2tx8kcpmsT81v5nV9r.jpg",
        "pieceIdentite": null,
        "deleted_at": null
    }
}
```
- `422`: Erreur de velidation
````json
{
    "status": 422,
    "message": "Échec de la validation des données.",
    "errors": {
        "password": [
            "vous ne pouvez pas éditer mot de passe"
        ]
    }
}
````
- `500`: Erreur interne

---
---
---

---
---



### Route `superadmin/admin-delete`

Cette route permet de supprimer un admin.

- **Méthode HTTP**: `DELETE`
- **Endpoint**: `api/superadmin/admin-delete`

#### Header:

- Authorization : le token de l'utilisateur connecté

#### Paramètres de la requête:

````json
{
    "id" : 3 
}
````

#### Réponses :
- `200`:
```json
{
    "status": 200,
    "message": "Suppression réussie."
}
```
- `422`: Erreur interne
````json
{
    "status": 422,
    "message": "Échec de la validation des données.",
    "errors": {
        "id": [
            " l'id renseigné ne correspond a aucun admin "
        ]
    }
}
````
---
---
---

---
---




