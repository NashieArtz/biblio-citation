```markdown
# Bibliotheque de Citations

## Presentation du projet
Application web permettant de constituer et gerer une bibliotheque de citations. Elle integre l'ajout, la consultation, la modification et la suppression (CRUD) avec un controle des saisies, le tout dans une interface simple et lisible. 

## Fonctionnalites
* Gestion complete des citations (CRUD).
* Validation des donnees saisies via le composant Validator.
* Protection des formulaires via l'installation et la configuration de `security-csrf`.

## Pre-requis
* Git
* Docker et Docker Compose

## Installation et Lancement

### 1. Obtenir le projet
```bash
# Cloner le depot localement
git clone [URL_DU_REPO]

# Acceder au dossier du projet
cd [NOM_DU_DOSSIER]

```

### 2. Lancer l'environnement

```bash
# Construire et demarrer les conteneurs Docker en arriere-plan
docker compose up -d --build

```

### 3. Initialiser l'application

```bash
# Acceder au terminal du conteneur de l'application (remplacer par le bon nom si besoin)
docker exec -it bibliotheque-citations-app-1 bash

# --- Commandes a executer A L'INTERIEUR du conteneur ---

# Installer toutes les dependances PHP (incluant form, validator, orm et security-csrf)
composer install

# Creer la base de donnees (si elle n'existe pas deja)
php bin/console doctrine:database:create --if-not-exists

# Appliquer la derniere migration pour creer les tables (incluant les nouvelles donnees)
php bin/console doctrine:migrations:migrate --no-interaction

```

### 4. Acces

* Application Symfony : http://localhost:8000
* Interface phpMyAdmin : http://localhost:8080

```

```
