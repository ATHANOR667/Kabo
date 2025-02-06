
---

### ROUTES DU GARDE MALADE

LIEES A LA GESTION DES EXPERIENCES DU SICK-GUARD PAR CELUI CI


Ces routes seront accessible pour un garde malade qu'il soit connecté ou pas,
que sa demande d'inscription soit en attente ou confirmée
de cette façon l'admin pourra la consulter en guise de cv
et les clients pourront aussi.

Il peut lister, créer et supprimer, mais pas éditer.
Ceci afin d'avoir une visibilité sur ce qui est ajouté ou supprimé
(l'admin voyant les softDelete.)


---
---
---

---
---

### Route `sickguard\qualification\list`

Cette route permet au sickguard de consulter la liste de ses qualifications.

- **Méthode HTTP**: `GET`
- **Endpoint**: `api/sickguard/qualification/list`

#### Paramètres de la requête:
````json
{
    "email" : "john@doe.c" ,
    "password" : "azerty"
}
````

#### Réponses :
- `200`: 
```json
{
    "status": 200,
    "data": {
        "qualifications": []
    }
}
```
-`400`
````json
````
---
````json
````
- `500`: Erreur interne

---
---
---

---
---



### Route `sickguard\qualification\create`

Cette route permet au sickguard de créer une qualification.

- **Méthode HTTP**: `POST`
- **Endpoint**: `api/sickguard/qualification/create`

#### Paramètres de la requête:
````json
{
    "titre": "Master en Informatique",
    "annee": 2023,
    "mention": "Très Bien",
    "institutionReference": "Université de Paris",
    "fichier": null, 
    "sick_guard_id": 1 , 
    "email" : "john@doe.c" ,
    "password" : "azerty"
}

````

#### Réponses :
- `200`:
```json
{
    "status": 200,
    "data": {
        "qualification": {
            "titre": "Master en Informatique",
            "annee": 2023,
            "mention": "Très Bien",
            "institutionReference": "Université de Paris",
            "fichier": null,
            "sick_guard_id": 1,
            "updated_at": "2025-02-02T12:51:42.000000Z",
            "created_at": "2025-02-02T12:51:42.000000Z",
            "id": 1
        }
    }
}
```
-`400`
````json
````
---
````json
````
- `500`: Erreur interne

---
---
---

---
---


### Route `sickguard\qualification\delete`

Cette route permet au sickguard de supprimer une qualification.

- **Méthode HTTP**: `DELETE`
- **Endpoint**: `api/sickguard/qualification/delete`

#### Paramètres de la requête:
````json
{
    "email" : "john@doe.c" ,
    "password" : "azerty",
    "id" : 1

}
````

#### Réponses :
- `200`:
```json
{
    "status": 200,
    "message": "qualification supprimée avec succèss"
}
```
-`404`
````json
{
    "status": 404,
    "message": "Qualification introuvable"
}
````
-`422`
````json
{
    "status": 422,
    "message": "Échec de la validation des données.",
    "errors": {
        "id": [
            "L'experience n'existe pas"
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
