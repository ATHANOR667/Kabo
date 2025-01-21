
---

### ROUTES DE L'ADMIN

LIEES A LA GESTION DES CLIENTS

---
---
---

---
---

### Route `admin\client-list`

Cette route permet à l'admin de récupérer la liste des clients.

- **Méthode HTTP**: `Get`
- **Endpoint**: `api/admin/client-list`

#### Entete :
- Authorization : token de l'admin

#### Paramètres de la requête:
- aucun

#### Réponses :
- `200`:
```json
{
    "status": 200,
    "data": {
        "selfRejected": [],
        "client": {
            "1": {
                "id": 2,
                "email": "john@doe.c",
                "nom": "John",
                "prenom": "Doe",
                "dateNaissance": "27-05-2005",
                "lieuNaissance": "Yaoundé",
                "telephone": "680694321",
                "sexe": "M",
                "photoProfil": null,
                "pieceIdentite": null,
                "deleted_at": null
            }
        },
        "banned": [
            {
                "id": 1,
                "email": "john@doe.com",
                "nom": "Doe",
                "prenom": "John",
                "dateNaissance": "27-12-2000",
                "lieuNaissance": "Cameroun,Yaoundé",
                "telephone": "1234567890",
                "sexe": "M",
                "photoProfil": null,
                "pieceIdentite": null,
                "deleted_at": null,
                "motif": "Plaintes répetées"
            }
        ]
    }
}
```
- self-rejected : compte supprimé par le client lui meme
- client : Client non banni avec compte actif
- banned : client banni
- `500`: Erreur interne

---
---
---

---
---


### Route `admin\client-ban`

Cette route permet à l'admin de bannir un client en precisant ou pas le motif.

- **Méthode HTTP**: `PATCH`
- **Endpoint**: `api/admin/client-ban`

#### Paramètres de la requête:
````json
{
    "id": 2 ,
    "motif" : "Plaintes répetées"  
}
````
le motif est nullable
#### Réponses :
- `200`:
```json
{
    "status": 200,
    "message": "John Doe a été banni."
}
```
-`400`
````json

````
- `500`: Erreur interne

---
---
---

---
---




### Route `admin\unban`

Cette route permet à l'admin d'annuler le bannisement d'un client.

- **Méthode HTTP**: `POST`
- **Endpoint**: `api/admin/unban`

#### Paramètres de la requête:
````json
{
    "id": 2 
}
````

#### Réponses :
- `200`:
```json
{
    "status": 200,
    "message": "John Doe a été retiré de la liste des bannis."
}
```
- `400`
- `500`: Erreur interne

---
---
---

---
---


### Route `admin\admin/client-ban-list`

Cette route permet à l'admin de récupérer l'historique de tous les bans d'un client.

- **Méthode HTTP**: `POST`
- **Endpoint**: `api/admin/admin/client-ban-list`

#### Paramètres de la requête:
````json
{
    "id": 2 
}
````

#### Réponses :
- `200`:
```json
{
    "status": 200,
    "historique des bans du client": [
        {
            "motif": "Plaintes répetées",
            "debut": "20-01-2025",
            "fin": "20-01-2025"
        },
        {
            "motif": "Plaintes répetées",
            "debut": "20-01-2025",
            "fin": null
        }
    ]
}
```
- `400`
- `500`: Erreur interne

---
---
---

---
---

