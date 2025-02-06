
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

### Route `sickguard\experience\list`

Cette route permet au sickguard de consulter la liste de ses experiences.

- **Méthode HTTP**: `GET`
- **Endpoint**: `api/sickguard/experience/list`

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
        "experiences": []
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



### Route `sickguard\experience\create`

Cette route permet au sickguard de créer une experience.

- **Méthode HTTP**: `POST`
- **Endpoint**: `api/sickguard/experience/create`

#### Paramètres de la requête:
````json
{
  "nomEntreprise": "Tech Innovators Inc.",
  "typeEntreprise": "Technologie",
  "nomReferent": "Jean Dupont",
  "numeroReferent": "0123456789",
  "posteReferent": "Responsable RH",
  "dateDebut": "2022-06-01",
  "dateFin": "2024-01-15",
  "poste": "Développeur Senior",
  "description": "Développement d'applications web et mobiles, gestion d'une équipe de 5 développeurs.",
  "sick_guard_id": 1 , 
  "email" : "john@doe.c",
  "password" : "azerty"
}

````

#### Réponses :
- `200`:
```json
{
    "status": 200,
    "data": {
        "experience": {
            "nomEntreprise": "Tech Innovators Inc.",
            "typeEntreprise": "Technologie",
            "nomReferent": "Jean Dupont",
            "numeroReferent": "0123456789",
            "posteReferent": "Responsable RH",
            "dateDebut": "2022-06-01",
            "dateFin": "2024-01-15",
            "poste": "Développeur Senior",
            "description": "Développement d'applications web et mobiles, gestion d'une équipe de 5 développeurs.",
            "sick_guard_id": 1,
            "updated_at": "2025-02-05T09:54:12.000000Z",
            "created_at": "2025-02-05T09:54:12.000000Z",
            "id": 245
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


### Route `sickguard\experience\delete`

Cette route permet au sickguard de supprimer une experience.

- **Méthode HTTP**: `DELETE`
- **Endpoint**: `api/sickguard/experience/delete`

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
    "message": "experience supprimée avec succèss"
}
```
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
-`404`
````json
{
    "status": 404,
    "message": "Experience introuvable"
}
````
- `500`: Erreur interne

---
---
---

---
---
