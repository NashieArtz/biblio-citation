# Bibliothèque de citations

Application web permettant de constituer et de gérer une bibliothèque de citations : ajout, consultation, modification et suppression, avec contrôle des saisies et retours utilisateur explicites.

## Stack technique

| Composant | Version |
|---|---|
| PHP | 8.2 |
| Symfony | 7.4 |
| Doctrine ORM | 3.x |
| MySQL | 8.2 |
| Serveur web | Symfony CLI (`symfony server:start`) |
| Conteneurisation | Docker / Docker Compose |

## Fonctionnalités

### Gestion des citations

- **Liste** : affichage de toutes les citations sous forme de cartes distinctes, avec date d'ajout et actions accessibles
- **Consultation** : page dédiée présentant l'intégralité des informations d'une citation
- **Ajout** : formulaire de création
- **Modification** : formulaire de mise à jour d'une citation existante
- **Suppression** : protégée par un jeton CSRF et une demande de confirmation
- **Bibliothèque vide** : message dédié lorsqu'aucune citation n'est enregistrée
- **Navigation** : chaque page donne accès à la liste, à l'ajout, à la consultation et à la modification

### Contrôle des saisies

La validation est assurée côté serveur par le composant Validator (contraintes déclarées sur l'entité `Citation`), en complément des attributs HTML5 du formulaire. Les messages d'erreur sont rédigés en français et affichés sous le champ concerné.

| Champ | Règles |
|---|---|
| Texte | obligatoire, de 5 à 5000 caractères |
| Auteur | obligatoire, de 2 à 100 caractères |
| Date d'ajout | obligatoire, renseignée automatiquement |
| Année de publication | facultative, format numérique (`1943`, `-350`) |
| Source | facultative, 255 caractères maximum |
| Catégorie | facultative, 30 caractères maximum |
| Univers | facultatif, 100 caractères maximum |
| Tags | 10 tags maximum, 50 caractères par tag |

### Messages flash

Chaque opération (ajout, modification, suppression) produit un message de confirmation ou d'erreur, affiché en haut de page et consommé à la première lecture.

### Champs à choix assistés

Les champs **catégorie**, **univers** et **tags** proposent des listes construites dynamiquement à partir des valeurs déjà présentes en base, afin d'éviter la création de doublons et les fautes de frappe. Un champ texte complémentaire permet malgré tout d'introduire une valeur inédite, qui alimentera à son tour la liste des propositions.

## Types de données

Entité `Citation`, table `citation` :

| Propriété | Type PHP | Colonne SQL | Nullable | Description |
|---|---|---|---|---|
| `id` | `int` | `INT AUTO_INCREMENT` | non | Identifiant, clé primaire |
| `text` | `string` | `LONGTEXT` | non | Texte de la citation |
| `auteur` | `string` | `VARCHAR(100)` | non | Auteur de la citation |
| `datetime` | `\DateTime` | `DATETIME` | non | Date et heure d'ajout, positionnées à l'instanciation |
| `source` | `?string` | `VARCHAR(255)` | oui | Source ou origine (œuvre, discours, ouvrage) |
| `publishing_year` | `?string` | `VARCHAR(15)` | oui | Année de publication de la source |
| `category` | `?string` | `VARCHAR(30)` | oui | Catégorie ou thème (philosophie, humour, informatique) |
| `universe` | `?string` | `VARCHAR(100)` | oui | Univers d'origine (monde réel, Runeterra, Grand Line) |
| `tags` | `array` | `JSON` | non | Liste de mots-clés libres |

## Pré-requis

- Git
- Docker et Docker Compose

Aucune installation locale de PHP, Composer ou MySQL n'est nécessaire : tout est fourni par les conteneurs.

## Installation

### 1. Récupérer le projet

```bash
git clone git@github.com:NashieArtz/biblio-citation.git
cd biblio-citation
```

En HTTPS, si vous n'avez pas de clé SSH configurée :

```bash
git clone https://github.com/NashieArtz/biblio-citation.git
cd biblio-citation
```

### 2. Construire et démarrer les conteneurs

```bash
docker compose up -d --build
```

Trois conteneurs sont lancés : l'application, la base MySQL et phpMyAdmin. Le conteneur applicatif installe les dépendances Composer à son premier démarrage, puis lance le serveur web.

Suivre la progression :

```bash
docker compose logs -f app
```

L'application est prête lorsque le message `Web server listening` apparaît.

### 3. Créer le schéma de base de données

La base `citations_db` est créée automatiquement par le conteneur MySQL ; il reste à appliquer les migrations :

```bash
docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction
```

La bibliothèque est alors vide : le projet ne contient pas de jeu de données initial. Les premières citations s'ajoutent depuis l'interface, via le bouton « Ajouter une citation ».

### 4. Accéder à l'application

| Service | URL | Identifiants |
|---|---|---|
| Application | http://localhost:8000 | — |
| phpMyAdmin | http://localhost:8080 | `root` / `root` |

## Commandes utiles

```bash
# Démarrer / arrêter la stack
docker compose up -d
docker compose stop

# Arrêter et supprimer les conteneurs (les données MySQL sont conservées)
docker compose down

# Ouvrir un terminal dans le conteneur applicatif
docker compose exec app bash

# Lister les routes
docker compose exec app php bin/console debug:router

# Vider le cache
docker compose exec app php bin/console cache:clear

# Vérifier la syntaxe des templates
docker compose exec app php bin/console lint:twig templates

# Générer une migration après modification d'une entité
docker compose exec app php bin/console make:migration
docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction
```

## Configuration

La connexion à la base est définie dans `.env` :

```dotenv
DATABASE_URL="mysql://root:root@database:3306/citations_db?serverVersion=8.2&charset=utf8mb4"
```

L'hôte `database` correspond au nom du service Docker. Pour surcharger cette valeur sans modifier le fichier versionné, créer un `.env.local`.

## Structure du projet

```
config/                     Configuration Symfony (routes, packages, services)
migrations/                 Migrations Doctrine
public/                     Racine web (index.php, .htaccess, css/)
src/Controller/             CitationController : les 5 routes du CRUD
src/Entity/                 Citation : mapping Doctrine et contraintes de validation
src/Form/                   CitationType : formulaire et listes de choix dynamiques
src/Repository/             CitationRepository : requêtes et valeurs distinctes
src/Service/                CitationService : persistance et suppression
templates/                  Vues Twig (base, index, show, formulaires)
compose.override.yaml       Ports exposés en développement
docker-compose.yml          Services applicatif, MySQL et phpMyAdmin
Dockerfile                  Image PHP 8.2 + Symfony CLI
```

## Routes

| Méthode | URL | Nom | Rôle |
|---|---|---|---|
| GET | `/` | `app_citation_index` | Liste des citations |
| GET / POST | `/new` | `app_citation_new` | Ajout d'une citation |
| GET | `/{id}` | `app_citation_show` | Consultation d'une citation |
| GET / POST | `/{id}/edit` | `app_citation_edit` | Modification d'une citation |
| POST | `/{id}` | `app_citation_delete` | Suppression d'une citation |
