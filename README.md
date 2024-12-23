# README


### Explications :
- **URL des projets déployés** : Liens vers les différentes parties du projet déployées sur le serveur web.
- **Dépôt Git** : Le lien vers le dépôt Git où se trouve le code source de votre projet.
- **Investissement de chaque membre** : Un résumé de ce que chaque membre a contribué dans les projets.
- **Indications supplémentaires pour lancer les applications** : Instructions supplémentaires pour démarrer les applications localement avec un script spécifique.
- **Peuplement de la base de données** : Une explication indiquant que la base de données est déjà peuplée et comment la peupler si nécessaire.

## URL des projets déployés

- **Annuaire** : [https://webinfo.iutmontp.univ-montp2.fr/~garroc/annuaire/](https://webinfo.iutmontp.univ-montp2.fr/~garroc/annuaire/)
- **Front** : [https://webinfo.iutmontp.univ-montp2.fr/~garroc/front](https://webinfo.iutmontp.univ-montp2.fr/~garroc/front)
- **API** : [https://webinfo.iutmontp.univ-montp2.fr/~garroc/trail/apiTrail/public/api](https://webinfo.iutmontp.univ-montp2.fr/~garroc/trail/apiTrail/public/api)

### Information sur l'Annuaire

Suite à des problèmes avec Webpack, le CSS ne fonctionnait plus correctement. Nous avons donc dû laisser le site tel quel. Cependant, le site fonctionne très bien.


## Dépôt Git

Le code source des projets est hébergé sur GitHub :  
[https://github.com/3Guys1Project/Rendu](https://github.com/3Guys1Project/Rendu)

## Récapitulatif de l'investissement des membres

- **Marius** : Principalement responsable du développement du front-end et a contribué partiellement à l'API.
- **Daniil** : Principalement responsable du développement du front-end et a contribué partiellement à l'API.
- **Clément** : Principalement responsable de l'API, mais a également contribué au front-end.

## Indications supplémentaires pour tester l'application en local

Pour lancer et tester l'application en local, voici les étapes spécifiques à suivre, outre les classiques `composer install` et `npm install` pour les projets, ainsi que la configuration et génération de la base de données :

1. Clonez le dépôt Git :
   ```bash
   git clone https://github.com/3Guys1Project/Rendu
   cd Rendu
    ```
2. Exécutez le script run.sh situé à la racine du projet pour démarrer les services nécessaires :
    ```bash
    ./run.sh
    ```
   
## Peuplement de la base de données

La base de données est déjà peuplée, donc il n'est pas nécessaire de relancer les scripts de peuplement. Toutefois, si vous devez peupler la base de données manuellement, exécutez les deux scripts SQL situés à la racine du projet sur GitHub.


## API

### Endpoints de l'API

Ci-dessous, vous trouverez une liste des routes disponibles dans l'API. Elle prend en charge les méthodes HTTP standards (GET, POST, PATCH, DELETE) pour faciliter les opérations CRUD sur les ressources.

### Authentification

- **Enregistrer un nouvel utilisateur**
    - `POST /api/auth/register`
- **Connexion**
    - `POST /api/auth/login`
- **Déconnexion**
    - `POST /api/token/invalidate`

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
    - `DELETE /api/participations/{id}`
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


## Répartition des contributions

Chaque membre a contribué de manière équitable au développement du projet :

- **Clément Garro** - Implémentation des routes d’utilisateur et d’authentification. Développement de la gestion des
  événements et des participations.
- **Marius Brouty** - Développement de la gestion des événements et des participations, gestion des JWT.
- **Daniil Hirchyts** - Configuration des fonctionnalités de sécurité, gestion des JWT, gestion des événements.