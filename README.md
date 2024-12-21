# Documentation de l'API

## Dépôt Git

Le code source complet du projet est disponible sur GitHub :
[3Guys1Project/apiTrail](https://github.com/3Guys1Project/apiTrail)

## Endpoints de l'API

Ci-dessous, vous trouverez une liste des routes disponibles dans l'API. Elle prend en charge les méthodes HTTP standards (GET, POST, PATCH, DELETE) pour faciliter les opérations CRUD sur les ressources.

### Authentification

- **Enregistrer un nouvel utilisateur**
    - `POST /api/auth/register`
- **Connexion**
    - `POST /api/auth/login`

### Utilisateurs

- **Récupérer tous les utilisateurs**
    - `GET /api/utilisateurs`
- **Récupérer un utilisateur par ID**
    - `GET /api/utilisateurs/{id}`
- **Mettre à jour un utilisateur par ID**
    - `PATCH /api/utilisateurs/{id}`
- **Supprimer un utilisateur par ID**
    - `DELETE /api/utilisateurs/{id}`
- **Changer le rôle d’un utilisateur**
    - `PATCH /api/utilisateurs/{id}/changeRole`

### Événements

- **Récupérer tous les événements**
    - `GET /api/events`
- **Récupérer un événement par ID**
    - `GET /api/events/{id}`
- **Créer un nouvel événement**
    - `POST /api/events`
- **Mettre à jour un événement par ID**
    - `PATCH /api/events/{id}`
- **Supprimer un événement par ID**
    - `DELETE /api/events/{id}`
- **Récupérer les événements d'un utilisateur**
    - `GET /api/utilisateurs/{idOrga}/events`

### Participations

- **Récupérer toutes les participations**
    - `GET /api/participations`
- **Récupérer une participation par ID**
    - `GET /api/participations/{id}`
- **Créer une nouvelle participation**
    - `POST /api/participations`
- **Récupérer les participations d'un utilisateurs**
    - `GET /api/utilisateurs/{idUser}/participations`
- **Supprimer une participation spécifique d'un utilisateur**
    - `DELETE /api/utilisateurs/{idUser}/participations/{idParticipation}`
- **Récupérer les participations d'un événement**
    - `GET /api/events/{idEvent}/participations`

## Sécurité et Authentification

L'API utilise des jetons JWT pour une authentification sécurisée des utilisateurs, lesquels sont retournés sous forme de cookies pour assurer une gestion sécurisée des sessions. Le contrôle d'accès est mis en place pour restreindre l'accès aux opérations sensibles en fonction des rôles des utilisateurs.

## Lancement de l’API

Le projet utilise Docker pour une configuration simplifiée en environnement de développement et de production. Voici comment démarrer l’API en utilisant Docker Compose et les différents fichiers de configuration.

### Prérequis

Assurez-vous d'avoir installé Docker et Docker Compose sur votre machine.

### Instructions de Lancement

1. **Choix de l'environnement** :
    - **Développement** : Ce mode utilise des volumes partagés pour un développement interactif.
    - **Production** : Ce mode configure l’API pour un environnement de production avec des variables d'environnement adaptées.

2. **Démarrage de l'API** :
    - Pour lancer en mode **développement** :
      ```bash
      ./run.sh dev
      ```
      Ce mode utilise `compose.yaml` pour monter les volumes de développement et expose des ports supplémentaires.

    - Pour lancer en mode **production** :
      ```bash
      ./run.sh prod
      ```
      Ce mode utilise à la fois `compose.yaml` et `compose.prod.yaml` pour une configuration adaptée en production, sans volumes partagés et avec les ports standards.

3. **Configuration des JWT** :
   Pour que l'authentification avec JWT fonctionne correctement, exécutez la commande suivante pour générer les clés de chiffrement JWT :
   ```bash
   php bin/console lexik:jwt:generate-keypair
   
4. **Accès à l'API** :
    - En mode **développement** : Accès à `http://localhost:80` (ou port spécifié).
    - En mode **production** : Accès à `https://localhost:443` avec un certificat SSL auto-signé.


## Répartition des contributions

Chaque membre a contribué de manière équitable au développement du projet :

- **Clément Garro** - Implémentation des routes d’utilisateur et d’authentification. Développement de la gestion des 
  événements et des participations.
- **Marius Brouty** - Développement de la gestion des événements et des participations, gestion des JWT.
- **Daniil Hirchyts** - Configuration des fonctionnalités de sécurité, gestion des JWT, gestion des événements.

