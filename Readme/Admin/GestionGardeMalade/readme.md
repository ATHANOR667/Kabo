
---

### ROUTES DE L'ADMIN

LIEES A LA GESTION DES GARDES MALADES

---
---
---

---
---

### Route `admin\sickguard-list`

Cette route permet à l'admin de récupérer la liste des gardes malades.

- **Méthode HTTP**: `Get`
- **Endpoint**: `api/admin/sickguard-list`

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
        "accepted": [
            {
                "id": 2,
                "nom": "John",
                "prenom": "Doe",
                "sexe": "M",
                "dateNaissance": "27-05-2005",
                "lieuNaissance": "Yaoundé",
                "telephone": "680694321",
                "pays": "Cameroun",
                "ville": "Youndé",
                "quartier": "titi garage",
                "photoProfil": null,
                "pieceIdentite": null,
                "email": "john@doe.c",
                "deleted_at": null,
                "admin_id": null,
                "active": null,
                "status": "accepted"
            }
        ],
        "rejected": [],
        "pending": [],
        "banned": []
    }
}
```
- self-rejected : compte supprimé par le garde malade lui meme 
- pending : Dossier du garde malade en attente de traitement
- rejected : Dossier du garde malade rejeté
- accepted : Dossier du garde malade accepté
- banned : Garde malade banni
- `500`: Erreur interne

---
---
---

---
---



### Route `admin\sickguard-accept`

Cette route permet à l'admin d'accepter la candidature d'un garde malade

- **Méthode HTTP**: `PATCH`
- **Endpoint**: `api/admin/sickguard-accept`

#### Paramètres de la requête:
````json
{
    "id": 1
}
````

#### Réponses :
- `200`:
```json
{
    "status": 200,
    "message": "Le profil de John Doe a été accepté."
}
```
- `400`
- `500`: Erreur interne

---
---
---

---
---



### Route `admin\sickguard-reject`

Cette route permet à l'admin de rejeter une candidature.

- **Méthode HTTP**: `PATCH`
- **Endpoint**: `api/admin/sickguard-reject`

#### Paramètres de la requête:
````json
{
    "id": 1
}
````

#### Réponses :
- `200`:
```json
{
    "status": 200,
    "message": "Le profil de John Doe a été rejeté."
}
```
- `400`
- `500`: Erreur interne

---
---
---

---
---



### Route `admin\sickguard-ban`

Cette route permet à l'admin de bannir un garde malade en precisant ou pas le motif.

- **Méthode HTTP**: `PATCH`
- **Endpoint**: `api/admin/sickguard-ban`

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

Cette route permet à l'admin d'annuler le bannisement d'un guarde malade.

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


### Route `admin\admin/sickguard-ban-list`

Cette route permet à l'admin de récupérer l'historique de tous les bans d'un guarde malade.

- **Méthode HTTP**: `POST`
- **Endpoint**: `api/admin/admin/sickguard-ban-list`

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
    "historique des bans du garde malade": [
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

