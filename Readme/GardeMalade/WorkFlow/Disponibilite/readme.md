
---

### ROUTES DU GARDE MALADE

LIÉES A LA GESTION DES DISPONIBILITÉS DU SICK-GUARD PAR CELUI CI



---
---
---

---
---

### Route `sickguard\disponibilite\list`

Cette route permet au sick guard de consulter la liste de ses disponibilités.

- **Méthode HTTP**: `GET`
- **Endpoint**: `api/sickguard/workflow/disponibilite/list`

#### Header 
     Token du sickguard
#### Paramètres de la requête:
````json

````

#### Réponses :
- `200`:
```json
{
    "status": 200,
    "data": {
        "disponibilites": {
            "annee": {
                "2025": {
                    "february": {
                        "05": [
                            {
                                "id": 1,
                                "date": "05-02-2025",
                                "debut": "18",
                                "fin": "19",
                                "deleted_at": null,
                                "created_at": "2025-02-05T14:01:37.000000Z",
                                "updated_at": "2025-02-05T14:01:37.000000Z",
                                "sick_guard_id": 1,
                                "jour": "wednesday"
                            }
                        ],
                        "06": [
                            {
                                "id": 6,
                                "date": "06-02-2025",
                                "debut": "13",
                                "fin": "14",
                                "deleted_at": null,
                                "created_at": "2025-02-06T11:27:20.000000Z",
                                "updated_at": "2025-02-06T11:27:20.000000Z",
                                "sick_guard_id": 1,
                                "jour": "thursday"
                            },
                            {
                                "id": 3,
                                "date": "06-02-2025",
                                "debut": "14",
                                "fin": "15",
                                "deleted_at": null,
                                "created_at": "2025-02-06T11:26:17.000000Z",
                                "updated_at": "2025-02-06T11:26:17.000000Z",
                                "sick_guard_id": 1,
                                "jour": "thursday"
                            },
                            {
                                "id": 4,
                                "date": "06-02-2025",
                                "debut": "15",
                                "fin": "16",
                                "deleted_at": null,
                                "created_at": "2025-02-06T11:26:25.000000Z",
                                "updated_at": "2025-02-06T11:26:25.000000Z",
                                "sick_guard_id": 1,
                                "jour": "thursday"
                            },
                            {
                                "id": 5,
                                "date": "06-02-2025",
                                "debut": "16",
                                "fin": "17",
                                "deleted_at": null,
                                "created_at": "2025-02-06T11:26:31.000000Z",
                                "updated_at": "2025-02-06T11:26:31.000000Z",
                                "sick_guard_id": 1,
                                "jour": "thursday"
                            }
                        ]
                    }
                }
            }
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



### Route `sickguard\disponibilite\create`

Cette route permet au sickguard de créer une disponibilite.

- **Méthode HTTP**: `POST`
- **Endpoint**: `api/sickguard/workflow/disponibilite/create`


#### Header
     Token du sickguard
#### Paramètres de la requête:
````json
{
    "date": "06-02-2025",
    "debut": 13
}
````

#### Réponses :
- `200`:
```json
{
    "status": 200,
    "message": "Disponibilité créée avec succès."
}
```
-`422`
````json
{
    "status": 422,
    "message": "Échec de la validation des données.",
    "errors": {
        "date": [
            "La date doit être aujourd'hui ou une date future."
        ]
    }
}
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


### Route `sickguard\disponibilite\delete`

Cette route permet au sickguard de supprimer une disponibilite.

- **Méthode HTTP**: `DELETE`
- **Endpoint**: `api/sickguard/workflow/disponibilite/delete`


#### Header
     Token du sickguard
#### Paramètres de la requête:
````json
{
    "date": "05-02-2025",
    "debut": 16
}
````

#### Réponses :
- `200`:
```json
{
    "status": 200,
    "message": "disponibilite supprimée avec succèss"
}
```
-`404`
````json
{
    "status": 404,
    "message": "La disponibilité spécifiée n'existe pas."
}
````
-`422`
````json
{
    "status": 422,
    "message": "Échec de la validation des données.",
    "errors": {
        "debut": [
            "L'heure de début ne peut pas être dans le passé."
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
