# Guide Complet de Déploiement Docker pour l'Application Laravel Multi-Tenant

## Table des Matières

1.  [Introduction et Prérequis](#1-introduction-et-prérequis)
2.  [Structure des Fichiers Docker](#2-structure-des-fichiers-docker)
3.  [Le `Dockerfile` : Construction de l'Image de l'Application](#3-le-dockerfile--construction-de-limage-de-lapplication)
    *   [Stage 1 : `frontend_builder`](#stage-1--frontend_builder)
    *   [Stage 2 : `php_application`](#stage-2--php_application)
    *   [Variables d'environnement de build et arguments](#variables-denvironnement-de-build-et-arguments)
    *   [Optimisations et Bonnes Pratiques du Dockerfile](#optimisations-et-bonnes-pratiques-du-dockerfile)
4.  [Le Fichier `.dockerignore`](#4-le-fichier-dockerignore)
5.  [Configuration Nginx (`docker/nginx/default.conf`)](#5-configuration-nginx-dockernginxdefaultconf)
6.  [Le `docker-compose.yml` : Orchestration pour le Développement Local](#6-le-docker-composeyml--orchestration-pour-le-développement-local)
    *   [Services Principaux](#services-principaux)
        *   [`app`](#service--app-)
        *   [`web`](#service--web-)
        *   [`db`](#service--db-)
        *   [`redis`](#service--redis-)
    *   [Services Auxiliaires](#services-auxiliaires)
        *   [`queue`](#service--queue-)
        *   [`scheduler`](#service--scheduler-)
        *   [`mailpit`](#service--mailpit-)
    *   [Réseaux et Volumes](#réseaux-et-volumes)
    *   [Gestion des Variables d'Environnement](#gestion-des-variables-denvironnement)
7.  [Le Script d'Entrée (`docker-entrypoint.sh`)](#7-le-script-dentrée-docker-entrypointsh)
8.  [Guide de Démarrage Rapide (Développement Local)](#8-guide-de-démarrage-rapide-développement-local)
    *   [Préparation Initiale](#préparation-initiale)
    *   [Configuration du Fichier `.env`](#configuration-du-fichier-env)
    *   [Build et Démarrage des Conteneurs](#build-et-démarrage-des-conteneurs)
    *   [Accès aux Services](#accès-aux-services)
    *   [Multi-Location en Local](#multi-location-en-local)
    *   [Commandes Docker Compose Utiles](#commandes-docker-compose-utiles)
9.  [Passage à Docker Swarm (Production/Staging)](#9-passage-à-docker-swarm-productionstaging)
    *   [Pourquoi Docker Swarm ?](#pourquoi-docker-swarm-)
    *   [Principes Clés pour Swarm](#principes-clés-pour-swarm)
10. [Le Fichier `docker-stack.yml` : Déploiement sur Docker Swarm](#10-le-fichier-docker-stackyml--déploiement-sur-docker-swarm)
    *   [Structure Générale](#structure-générale-du-docker-stackyml)
    *   [Gestion des Images Applicatives](#gestion-des-images-applicatives)
    *   [Gestion Cruciale des Secrets Docker](#gestion-cruciale-des-secrets-docker)
    *   [Gestion des Configurations Docker](#gestion-des-configurations-docker)
    *   [Services dans le Stack Swarm](#services-dans-le-stack-swarm)
    *   [Stockage Persistent en Swarm](#stockage-persistent-en-swarm)
    *   [Migrations et Tâches d'Initialisation](#migrations-et-tâches-dinitialisation)
    *   [Ingress, Reverse Proxy et Domaines/SSL](#ingress-reverse-proxy-et-domainesssl)
11. [Déploiement sur Docker Swarm : Étapes Pratiques](#11-déploiement-sur-docker-swarm--étapes-pratiques)
    *   [Prérequis pour Swarm](#prérequis-pour-swarm)
    *   [Étapes de Déploiement](#étapes-de-déploiement)
12. [Considérations de Sécurité et Bonnes Pratiques Docker](#12-considérations-de-sécurité-et-bonnes-pratiques-docker)
    *   [Sécurité des Images](#sécurité-des-images)
    *   [Sécurité des Conteneurs en Exécution](#sécurité-des-conteneurs-en-exécution)
    *   [Gestion des Secrets](#gestion-des-secrets)
    *   [Réseau](#réseau)
    *   [Logging et Monitoring](#logging-et-monitoring)

---

## 1. Introduction et Prérequis

Ce guide détaille la mise en place d'un environnement de développement et de production conteneurisé pour une application Laravel multi-tenant en utilisant Docker, Docker Compose et Docker Swarm.

**Prérequis Généraux :**

*   Connaissances de base de Laravel.
*   Connaissances de base de Docker (images, conteneurs, volumes, réseaux).
*   Docker Engine et Docker Compose installés sur votre machine de développement.
*   Pour la partie Swarm : un cluster Docker Swarm fonctionnel (peut être initialisé localement pour des tests).
*   Accès à un registre Docker (Docker Hub, GitLab Registry, AWS ECR, etc.) pour la partie Swarm.
*   Git pour la gestion de version.

## 2. Structure des Fichiers Docker

Voici les principaux fichiers que nous allons créer et utiliser :

*   `Dockerfile`: Définit comment construire l'image de votre application Laravel.
*   `.dockerignore`: Spécifie les fichiers et répertoires à exclure lors de la création de l'image Docker pour optimiser la taille et la vitesse de build.
*   `docker-compose.yml`: Orchestre les services nécessaires pour l'environnement de développement local (application, base de données, Redis, Nginx, etc.).
*   `docker-stack.yml`: Similaire à `docker-compose.yml`, mais adapté pour le déploiement en mode Swarm (production/staging).
*   `docker/nginx/default.conf`: Fichier de configuration pour Nginx.
*   `docker-entrypoint.sh`: Script exécuté au démarrage du conteneur de l'application pour des tâches d'initialisation.
*   `.env.docker-example`: Modèle pour le fichier `.env` spécifique à l'environnement Docker.
*   `GUIDE_DEPLOIEMENT_DOCKER.md` (ce fichier) : Votre guide de référence.

## 3. Le `Dockerfile` : Construction de l'Image de l'Application

Le `Dockerfile` est la recette pour construire l'image Docker de votre application Laravel. Nous utilisons une approche multi-stage pour optimiser la taille de l'image finale et séparer les dépendances de build des dépendances d'exécution.

```dockerfile
# Stage 1: Build frontend assets
FROM node:lts-alpine as frontend_builder

WORKDIR /app_frontend

# Install git, needed for some yarn dependencies
RUN apk add --no-cache git

COPY package.json yarn.lock ./
RUN yarn install --frozen-lockfile

COPY . .
RUN yarn build

# Stage 2: Setup PHP application
FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

# Install system dependencies
RUN apk add --no-cache \
    bash \
    curl \
    libzip-dev \
    libpng-dev \
    jpeg-dev \
    freetype-dev \
    icu-dev \
    oniguruma-dev \
    supervisor \
    mysql-client \
    redis

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    intl \
    bcmath \
    opcache \
    pdo_mysql \
    pcntl \
    exif \
    zip \
    sockets

RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.revalidate_freq=0'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_cli=1'; \
    echo 'upload_max_filesize = 100M'; \
    echo 'post_max_size = 100M'; \
    echo 'memory_limit = 256M'; \
    echo 'max_execution_time = 300'; \
} > /usr/local/etc/php/conf.d/zz-laravel-optimizations.ini

COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-plugins --no-scripts --no-dev --prefer-dist --optimize-autoloader

COPY . .

COPY --chown=www-data:www-data --from=frontend_builder /app_frontend/public/build /var/www/html/public/build
COPY --chown=www-data:www-data --from=frontend_builder /app_frontend/public/hot /var/www/html/public/hot

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php-fpm"]
```

### Stage 1 : `frontend_builder`

*   `FROM node:lts-alpine as frontend_builder`: Utilise une image Node.js Alpine (légère) pour compiler les assets frontend. Le `as frontend_builder` nomme ce stage.
*   `WORKDIR /app_frontend`: Définit le répertoire de travail.
*   `RUN apk add --no-cache git`: Installe Git, parfois nécessaire pour certaines dépendances Yarn.
*   `COPY package.json yarn.lock ./`: Copie les fichiers de dépendances.
*   `RUN yarn install --frozen-lockfile`: Installe les dépendances Node.js.
*   `COPY . .`: Copie tout le code source de l'application (nécessaire pour que Vite/Webpack trouve les fichiers sources).
*   `RUN yarn build`: Exécute le script de build frontend (ex: `vite build` ou `npm run prod`).

### Stage 2 : `php_application`

C'est le stage qui produira l'image finale de notre application.

*   `FROM php:8.2-fpm-alpine`: Utilise une image PHP 8.2 FPM Alpine. FPM (FastCGI Process Manager) est un gestionnaire de processus PHP haute performance.
*   `WORKDIR /var/www/html`: Définit le répertoire de travail standard pour les applications web.
*   `apk add --no-cache ...`: Installe les dépendances système nécessaires :
    *   `bash`, `curl`: Utilitaires courants.
    *   `libzip-dev`, `libpng-dev`, `jpeg-dev`, `freetype-dev`, `icu-dev`, `oniguruma-dev`: Bibliothèques pour les extensions PHP (zip, gd, intl, mbstring).
    *   `supervisor`: Peut être utilisé pour gérer des processus (comme les workers de file d'attente), bien que dans notre `docker-compose.yml` nous ayons un service dédié pour le worker.
    *   `mysql-client`: Pour que l'application puisse se connecter à MySQL et pour les commandes `mysqladmin` dans le script d'entrée.
    *   `redis`: Pour l'extension Redis et le CLI `redis-cli`.
*   `docker-php-ext-configure gd ... && docker-php-ext-install ...`: Configure et installe les extensions PHP couramment utilisées par Laravel :
    *   `gd`: Traitement d'images.
    *   `intl`: Internationalisation.
    *   `bcmath`: Calculs de précision arbitraire.
    *   `opcache`: Améliore les performances PHP en mettant en cache le bytecode précompilé.
    *   `pdo_mysql`: Driver PHP pour MySQL.
    *   `pcntl`: Contrôle des processus (utile pour les workers de file d'attente).
    *   `exif`: Lecture des métadonnées d'images.
    *   `zip`: Manipulation des archives ZIP.
    *   `sockets`: Communication réseau bas niveau.
*   `pecl install redis && docker-php-ext-enable redis`: Installe l'extension Redis via PECL.
*   `COPY --from=composer/composer:latest-bin /composer /usr/bin/composer`: Copie l'exécutable Composer depuis une image officielle pour l'avoir globalement.
*   `RUN { ... } > /usr/local/etc/php/conf.d/zz-laravel-optimizations.ini`: Crée un fichier de configuration PHP avec des optimisations recommandées pour OPcache et des paramètres courants pour Laravel (limites de upload, mémoire, temps d'exécution).
*   `COPY composer.json composer.lock ./`: Copie les fichiers de dépendances PHP.
*   `RUN composer install --no-interaction --no-plugins --no-scripts --no-dev --prefer-dist --optimize-autoloader`: Installe les dépendances PHP pour la production (`--no-dev`) et optimise l'autoloader.
*   `COPY . .`: Copie le reste du code de l'application.
*   `COPY --chown=www-data:www-data --from=frontend_builder ...`: Copie les assets frontend compilés depuis le stage `frontend_builder` vers le répertoire `public/build` de l'application PHP. `public/hot` est également copié au cas où il serait utilisé par Vite en mode build. L'option `--chown=www-data:www-data` s'assure que l'utilisateur `www-data` (sous lequel PHP-FPM s'exécute) est propriétaire de ces fichiers.
*   `RUN chown -R www-data:www-data ... && chmod -R 775 ...`: Définit les permissions correctes pour les répertoires `storage` et `bootstrap/cache` pour que PHP puisse y écrire.
*   `COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh`: Copie le script d'entrée.
*   `RUN chmod +x /usr/local/bin/docker-entrypoint.sh`: Rend le script d'entrée exécutable.
*   `EXPOSE 9000`: Expose le port sur lequel PHP-FPM écoute.
*   `ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]`: Définit le script d'entrée à exécuter lorsque le conteneur démarre.
*   `CMD ["php-fpm"]`: Définit la commande par défaut si l'entrypoint réussit (lance PHP-FPM).

### Optimisations et Bonnes Pratiques du Dockerfile

*   **Utilisation d'images Alpine :** Plus petites, réduisant la surface d'attaque.
*   **Multi-stage builds :** Sépare l'environnement de build de l'environnement d'exécution pour une image finale plus légère.
*   **Minimiser le nombre de layers :** Regrouper les commandes `RUN` autant que possible.
*   **Gestion du cache Docker :** Placer les commandes qui changent moins souvent (installation de dépendances système) avant celles qui changent plus souvent (copie du code source).
*   **Utilisation de `--no-cache` avec `apk add` :** Réduit la taille de l'image en ne conservant pas le cache du gestionnaire de paquets.
*   **Installation sélective des dépendances :** N'installer que ce qui est strictement nécessaire.
*   **Permissions :** S'assurer que l'utilisateur non-root (`www-data`) peut écrire dans les répertoires nécessaires mais n'a pas plus de droits que requis.

## 4. Le Fichier `.dockerignore`

Ce fichier spécifie les fichiers et répertoires à ignorer lors de l'envoi du contexte de build au démon Docker. Cela accélère le build et évite d'inclure des fichiers inutiles ou sensibles dans l'image.

```dockerignore
# Git
.git
.gitignore
.gitattributes

# Docker
Dockerfile
.dockerignore
docker-compose.yml
docker-stack.yml # Ajout du fichier stack
docker/ # Répertoire des configurations Docker (Nginx, etc.)

# Node.js
node_modules
npm-debug.log
yarn-error.log

# Laravel / PHP
storage/logs/*
storage/framework/sessions/*
storage/framework/cache/data/* # Plus spécifique pour le cache de données
storage/framework/testing/*
storage/framework/views/*
bootstrap/cache/*.php
bootstrap/cache/*.key
public/storage # Le lien symbolique, pas le contenu réel de storage/app/public
.env
.env.*
!.env.example # On veut garder l'exemple
!.env.docker-example # On veut garder cet exemple aussi

# Vendor (PHP dependencies)
vendor/

# IDE / OS files
.idea
.vscode
*.swp
*.swo
*~
.DS_Store
Thumbs.db

# Documentation
GUIDE_DEPLOIEMENT_DOCKER.md
```
*   On ignore les répertoires de versioning, les dépendances locales (`node_modules`, `vendor` car elles sont installées dans le Dockerfile), les fichiers d'environnement locaux, les caches, etc.

## 5. Configuration Nginx (`docker/nginx/default.conf`)

Ce fichier configure Nginx pour servir l'application Laravel.

```nginx
server {
    listen 80;
    server_name _; # Accepte n'importe quel nom d'hôte

    root /var/www/html/public; # Racine pointe vers le répertoire public de Laravel
    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?$query_string; # Règle standard de Laravel pour le front controller
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    location ~ /\. { # Empêche l'accès aux fichiers cachés
        deny all;
    }

    # Transmet les scripts PHP à PHP-FPM
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        # 'app' est le nom du service PHP-FPM dans docker-compose.yml
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_param SERVER_PORT $server_port; # Important pour l'identification des sous-domaines par Stancl/Tenancy
        include fastcgi_params; # Paramètres FastCGI standards
        fastcgi_read_timeout 300;
        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
        client_max_body_size 100M; # Permet des uploads plus importants
    }

    # Mise en cache des assets statiques
    location ~ /\.(css|js|jpg|jpeg|png|gif|svg|ico|woff|woff2|ttf|eot)$ {
        expires 1M;
        access_log off;
        add_header Cache-Control "public";
    }

    # Sécurité : Bloquer l'accès direct à certains fichiers/dossiers sensibles
    location ~ /\.env { deny all; }
    location ~ /(storage|bootstrap/cache)/ { deny all; } # Ne devrait pas être nécessaire si la racine est bien public/
    location ~ /\.ht { deny all; }
}
```
*   `listen 80`: Nginx écoute sur le port 80.
*   `server_name _;`: Permet à Nginx de répondre à n'importe quel nom d'hôte, utile pour les sous-domaines des tenants en développement.
*   `root /var/www/html/public;`: La racine du site est le répertoire `public` de Laravel.
*   `try_files $uri $uri/ /index.php?$query_string;`: Si un fichier ou répertoire n'est pas trouvé, la requête est passée à `index.php`.
*   `fastcgi_pass app:9000;`: Transmet les requêtes PHP au service `app` (notre conteneur PHP-FPM) sur le port 9000.
*   `client_max_body_size 100M;`: Augmente la taille maximale des requêtes (utile pour les uploads de fichiers).

## 6. Le `docker-compose.yml` : Orchestration pour le Développement Local

Ce fichier définit et configure les services, réseaux et volumes nécessaires pour faire fonctionner l'application en local.

```yaml
version: '3.8'

networks:
  sail: # Nom du réseau interne, similaire à Laravel Sail
    driver: bridge

volumes:
  sail-mysql: # Volume pour la persistance des données MySQL
    driver: local
  sail-redis: # Volume pour la persistance des données Redis (optionnel)
    driver: local

services:
  # Service de l'application PHP
  app:
    build:
      context: . # Construit l'image à partir du Dockerfile dans le répertoire courant
      dockerfile: Dockerfile
    # container_name: ${APP_NAME:-laravel_app}_app # Décommenter si vous voulez un nom fixe (utile pour certains scripts)
    restart: unless-stopped
    working_dir: /var/www/html
    volumes:
      - .:/var/www/html # Monte le code source local dans le conteneur pour développement live
      # Potentiellement d'autres volumes pour le stockage persistant des tenants si non géré par S3 en dev
    environment: # Variables d'environnement pour le conteneur app
      - "DB_HOST=db"
      - "DB_PORT=${DB_PORT:-3306}"
      - "DB_DATABASE=${DB_DATABASE:-laravel}"
      - "DB_USERNAME=${DB_USERNAME:-sail}"
      - "DB_PASSWORD=${DB_PASSWORD:-password}"
      - "REDIS_HOST=redis"
      - "SESSION_DRIVER=${SESSION_DRIVER:-redis}"
      - "CACHE_STORE=${CACHE_STORE:-redis}"
      - "QUEUE_CONNECTION=${QUEUE_CONNECTION:-redis}"
      - "PHP_IDE_CONFIG=serverName=Docker" # Pour Xdebug (si configuré)
      - "APP_URL=${APP_URL:-http://localhost}"
      - "VITE_ASSET_URL=${APP_URL:-http://localhost}/build" # Pour les assets Vite servis par Nginx
      - "APP_ENV=${APP_ENV:-local}"
      - "APP_DEBUG=${APP_DEBUG:-true}"
      - "APP_KEY=${APP_KEY}" # Doit être défini dans .env
      - "OPTIMIZE_APP=${OPTIMIZE_APP:-false}" # Contrôle les optimisations Laravel dans l'entrypoint
      - "SETUP_TENANTS_ON_STARTUP=${SETUP_TENANTS_ON_STARTUP:-false}" # Contrôle l'exécution de tenants:setup
      - "AUTO_MIGRATE_TENANTS=${AUTO_MIGRATE_TENANTS:-true}" # Contrôle l'exécution de tenants:migrate
    depends_on: # Dépendances pour l'ordre de démarrage
      - db
      - redis
    networks:
      - sail

  # Service Web Nginx
  web:
    image: nginx:alpine
    restart: unless-stopped
    ports:
      - "${APP_PORT:-80}:80" # Mappe le port 80 du conteneur au port APP_PORT de l'hôte
    volumes:
      - .:/var/www/html:ro # Accès en lecture seule au code pour servir les assets statiques (si besoin)
      - ./public:/var/www/html/public:ro # Plus spécifique pour les assets publics
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf:ro # Monte la configuration Nginx
    depends_on:
      - app
    networks:
      - sail

  # Service Base de Données (MySQL)
  db:
    image: mysql:8.0 # Peut être remplacé par mariadb ou postgres
    restart: unless-stopped
    ports:
      - "${FORWARD_DB_PORT:-3306}:3306" # Mappe le port MySQL pour accès externe
    environment:
      MYSQL_ROOT_PASSWORD: "${DB_ROOT_PASSWORD:-secret}" # Mot de passe root de MySQL
      MYSQL_DATABASE: "${DB_DATABASE:-laravel}" # Base de données à créer au démarrage (pour l'app centrale)
      MYSQL_USER: "${DB_USERNAME:-sail}" # Utilisateur à créer
      MYSQL_PASSWORD: "${DB_PASSWORD:-password}" # Mot de passe de l'utilisateur
    volumes:
      - sail-mysql:/var/lib/mysql # Volume pour la persistance des données
      # - ./docker/mysql/my.cnf:/etc/mysql/conf.d/my.cnf # Config MySQL perso (optionnel)
    networks:
      - sail
    healthcheck: # Vérifie la santé du service
      test: ["CMD", "mysqladmin", "ping", "-p${DB_PASSWORD:-password}"]
      retries: 3
      timeout: 5s

  # Service Redis
  redis:
    image: redis:alpine
    restart: unless-stopped
    ports:
      - "${FORWARD_REDIS_PORT:-6379}:6379" # Mappe le port Redis
    volumes:
      - sail-redis:/data # Volume pour la persistance (si activée dans la config Redis)
    networks:
      - sail
    healthcheck:
      test: ["CMD", "redis-cli", "ping"]
      retries: 3
      timeout: 5s

  # Service Worker de File d'Attente
  queue:
    build: # Utilise la même image que le service 'app'
      context: .
      dockerfile: Dockerfile
    restart: unless-stopped
    command: php artisan queue:work --verbose --tries=3 --timeout=90 # Commande pour lancer le worker
    working_dir: /var/www/html
    volumes:
      - .:/var/www/html # Accès au code
    environment: # Variables nécessaires pour le worker
      - "DB_HOST=db"
      - "REDIS_HOST=redis"
      - "APP_ENV=${APP_ENV:-local}"
      - "APP_KEY=${APP_KEY}"
      # ... autres variables d'environnement de 'app' si nécessaires
    depends_on:
      - app # Pour s'assurer que le code est disponible (bien que db et redis soient les vraies dépendances)
      - db
      - redis
    networks:
      - sail

  # Service Planificateur de Tâches (Scheduler)
  scheduler:
    build:
      context: .
      dockerfile: Dockerfile
    restart: unless-stopped
    command: | # Lance le scheduler Laravel en boucle
      bash -c " \
        echo 'Starting Laravel Scheduler...' && \
        while [ true ] ; do \
          php /var/www/html/artisan schedule:run --verbose --no-interaction & \
          sleep 60 ; \
        done \
      "
    working_dir: /var/www/html
    volumes:
      - .:/var/www/html
    environment:
      - "DB_HOST=db"
      - "REDIS_HOST=redis"
      - "APP_ENV=${APP_ENV:-local}"
      - "APP_KEY=${APP_KEY}"
    depends_on:
      - app
      - db
      - redis
    networks:
      - sail

  # Service Mailpit (pour intercepter les emails en local)
  mailpit:
    image: axllent/mailpit:latest
    restart: unless-stopped
    ports:
      - "${FORWARD_MAILPIT_PORT:-1025}:1025" # Port SMTP
      - "${FORWARD_MAILPIT_UI_PORT:-8025}:8025" # Port Web UI
    networks:
      - sail
    # environment: # Options de configuration pour Mailpit (voir leur documentation)
    #   MP_MAX_MESSAGES: 5000
    # volumes: # Persistance des emails de Mailpit (optionnel)
    #   - ./docker/mailpit_data:/data
```

### Services Principaux

*   **`app`**:
    *   Construit à partir du `Dockerfile`.
    *   Exécute PHP-FPM.
    *   Monte le code source local (`.:/var/www/html`) pour permettre le développement en direct sans avoir à reconstruire l'image à chaque changement de code PHP.
    *   Définit des variables d'environnement pour la configuration de Laravel (connexion base de données, Redis, etc.).
*   **`web`**:
    *   Utilise l'image `nginx:alpine`.
    *   Sert de reverse proxy pour le service `app`.
    *   Mappe le port 80 de l'hôte au port 80 du conteneur (configurable via `APP_PORT` dans `.env`).
    *   Monte la configuration Nginx personnalisée.
*   **`db`**:
    *   Utilise l'image `mysql:8.0`.
    *   Expose le port MySQL à l'hôte (configurable via `FORWARD_DB_PORT`).
    *   Utilise un volume nommé (`sail-mysql`) pour la persistance des données de la base de données.
    *   Définit des variables d'environnement pour initialiser la base de données et l'utilisateur.
*   **`redis`**:
    *   Utilise l'image `redis:alpine`.
    *   Expose le port Redis à l'hôte (configurable via `FORWARD_REDIS_PORT`).
    *   Utilise un volume nommé (`sail-redis`) pour la persistance des données Redis (si nécessaire).

### Services Auxiliaires

*   **`queue`**:
    *   Utilise la même image que `app`.
    *   Exécute la commande `php artisan queue:work` pour traiter les jobs en file d'attente.
*   **`scheduler`**:
    *   Utilise la même image que `app`.
    *   Exécute `php artisan schedule:run` toutes les minutes pour les tâches planifiées.
*   **`mailpit`**:
    *   Image `axllent/mailpit` pour capturer les e-mails envoyés par l'application en développement. Accessible via une interface web.

### Réseaux et Volumes

*   **`networks: sail`**: Crée un réseau bridge personnalisé. Tous les services y sont attachés, leur permettant de communiquer entre eux en utilisant leurs noms de service comme noms d'hôte (ex: `app` peut joindre `db` via l'hôte `db`).
*   **`volumes: sail-mysql, sail-redis`**: Crée des volumes Docker nommés. Ces volumes sont gérés par Docker et persistent même si les conteneurs sont supprimés et recréés. C'est essentiel pour ne pas perdre les données de la base de données ou de Redis.

### Gestion des Variables d'Environnement

*   Le `docker-compose.yml` utilise des variables d'environnement (ex: `${APP_PORT:-80}`). Celles-ci sont substituées par Docker Compose à partir :
    1.  D'un fichier `.env` à la racine du projet.
    2.  Des variables d'environnement de votre shell.
    3.  Des valeurs par défaut spécifiées après `:-`.
*   Le service `app` reçoit un ensemble de variables d'environnement qui configurent Laravel (ex: `DB_HOST=db`).

## 7. Le Script d'Entrée (`docker-entrypoint.sh`)

Ce script est exécuté chaque fois que le conteneur `app` démarre. Il effectue des tâches d'initialisation avant de lancer la commande principale (PHP-FPM).

```bash
#!/bin/bash
set -e # Arrête le script si une commande échoue

# Attend que la base de données soit prête
echo "Waiting for database connection..."
timeout 60 bash -c 'until mysqladmin ping -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" --silent; do
  echo "Database is unavailable - sleeping"
  sleep 1
done'
echo "Database is up - executing command"

# Optimisations Laravel (si APP_ENV=production ou OPTIMIZE_APP=true)
if [[ "$APP_ENV" == "production" ]] || [[ "$OPTIMIZE_APP" == "true" ]]; then
  echo "Running Laravel optimizations..."
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  # php artisan event:cache # Décommenter si utilisé
fi

# Migrations de la base de données centrale
echo "Running central migrations..."
php artisan migrate --force

# Logique de gestion des tenants
if [[ "$SETUP_TENANTS_ON_STARTUP" == "true" ]] && [[ -f "app/Console/Commands/SetupTenants.php" ]]; then
  echo "Running initial tenant setup (creation, migration, seeding via tenants:setup)..."
  php artisan tenants:setup # Exécute la commande personnalisée de setup des tenants
elif [[ "$AUTO_MIGRATE_TENANTS" == "true" ]]; then
  echo "Running migrations for existing tenants (via tenants:migrate)..."
  php artisan tenants:migrate --force # Commande stancl/tenancy standard
else
  echo "Skipping automatic tenant setup/migration. Manual execution might be required for tenants."
fi

# Crée le lien symbolique pour le stockage public (si non production et lien inexistant)
if [ ! -L "/var/www/html/public/storage" ] && [ "$APP_ENV" != "production" ]; then
  echo "Linking storage directory..."
  php artisan storage:link
else
  echo "Storage link already exists or in production mode (skipping auto-link)."
fi

# Exécute la commande passée au conteneur (par défaut: php-fpm)
echo "Starting PHP-FPM or executing command: $@"
exec "$@"
```
*   Attend que la base de données soit accessible.
*   Exécute les commandes d'optimisation de Laravel (`config:cache`, `route:cache`, `view:cache`) si `APP_ENV` est `production` ou si `OPTIMIZE_APP` est `true`.
*   Exécute les migrations pour la base de données centrale (`php artisan migrate --force`).
*   Gère la configuration des tenants :
    *   Si `SETUP_TENANTS_ON_STARTUP=true`, il exécute `php artisan tenants:setup` (votre commande personnalisée qui devrait gérer la création de la base de données du tenant, les migrations et le seeding).
    *   Sinon, si `AUTO_MIGRATE_TENANTS=true`, il exécute `php artisan tenants:migrate --force` pour migrer les bases de données des tenants existants.
*   Crée le lien symbolique `public/storage` via `php artisan storage:link` si l'environnement n'est pas la production et que le lien n'existe pas déjà.
*   Finalement, `exec "$@"` exécute la commande principale du conteneur (définie par `CMD` dans le `Dockerfile`, soit `php-fpm`).

## 8. Guide de Démarrage Rapide (Développement Local)

Suivez ces étapes pour lancer l'environnement de développement.

### Préparation Initiale

1.  **Clonez le projet** (si ce n'est pas déjà fait).
2.  Assurez-vous que **Docker et Docker Compose sont installés** et en cours d'exécution.

### Configuration du Fichier `.env`

1.  **Copiez `.env.docker-example` vers `.env`**:
    ```bash
    cp .env.docker-example .env
    ```
2.  **Ouvrez le fichier `.env` et configurez-le :**
    *   **`APP_KEY`**: C'est la plus importante. Une fois les conteneurs construits, générez la clé avec :
        ```bash
        docker-compose run --rm app php artisan key:generate
        ```
        Copiez la clé générée (ex: `base64:...`) dans `APP_KEY=` de votre fichier `.env`.
        Alternativement, si vous avez PHP localement : `php artisan key:generate --show` et copiez la clé.
    *   **`APP_URL`**: Par défaut `http://localhost`. Si vous utilisez un autre domaine local (ex: `http://myapp.test`), mettez-le à jour ici et dans votre fichier `/etc/hosts`.
    *   **`DB_DATABASE`**: Nom de votre base de données centrale (ex: `laravel_central`).
    *   **`DB_USERNAME` et `DB_PASSWORD`**:
        *   **Pour la multi-location et simplifier les permissions en développement**, il est **fortement recommandé** d'utiliser l'utilisateur `root` de MySQL pour la connexion de l'application.
            *   Définissez `DB_USERNAME=root`.
            *   Définissez `DB_PASSWORD=secret` (ou la valeur que vous mettrez pour `MYSQL_ROOT_PASSWORD`).
            *   Assurez-vous que `MYSQL_ROOT_PASSWORD` dans la section `db` du `docker-compose.yml` (ou dans votre `.env` si vous le surchargez) correspond à ce `DB_PASSWORD`.
        *   Si vous préférez un utilisateur dédié (ex: `sail`):
            *   `DB_USERNAME=sail`
            *   `DB_PASSWORD=password`
            *   Ces valeurs doivent correspondre à `MYSQL_USER` et `MYSQL_PASSWORD` pour le service `db`.
            *   **Crucial :** Cet utilisateur `sail` doit avoir les droits `CREATE DATABASE` sur le serveur MySQL pour que `stancl/tenancy` puisse créer les bases de données des tenants. Vous devrez accorder ce droit manuellement ou via un script d'initialisation MySQL.
    *   **`MYSQL_ROOT_PASSWORD`**: Mot de passe pour l'utilisateur `root` de MySQL à l'intérieur du conteneur `db`. (ex: `secret`).
    *   **`FORWARD_DB_PORT`**: Si le port `3306` est déjà utilisé sur votre machine hôte, changez cette valeur (ex: `3307`).
    *   **`SETUP_TENANTS_ON_STARTUP`**: Mettez à `true` si vous voulez que `php artisan tenants:setup` s'exécute au démarrage pour initialiser les tenants existants dans la DB centrale.
    *   **`AUTO_MIGRATE_TENANTS`**: Mettez à `true` (si `SETUP_TENANTS_ON_STARTUP` est `false`) pour que `php artisan tenants:migrate --force` s'exécute.
    *   **`CENTRAL_DOMAIN`**: Doit correspondre au domaine de `APP_URL` (ex: `localhost` ou `myapp.test`).

### Build et Démarrage des Conteneurs

1.  **Construire les images Docker** (nécessaire la première fois ou après des modifications du `Dockerfile` ou des fichiers de configuration copiés dans l'image) :
    ```bash
    docker-compose build
    ```
2.  **Démarrer les services en arrière-plan (detached mode)** :
    ```bash
    docker-compose up -d
    ```

### Accès aux Services

*   **Application Web**: Ouvrez votre navigateur et allez à `http://localhost` (ou la valeur de `APP_URL` et `APP_PORT` si vous avez changé le port par défaut `80`).
*   **Mailpit (Interface Web)**: `http://localhost:8025` (ou le port défini par `FORWARD_MAILPIT_UI_PORT`). Les e-mails envoyés par Laravel apparaîtront ici.
*   **Base de Données (accès externe)**:
    *   Hôte: `127.0.0.1`
    *   Port: `3306` (ou la valeur de `FORWARD_DB_PORT`)
    *   Utilisateur: celui défini dans `.env` (`DB_USERNAME`, ex: `root` ou `sail`)
    *   Mot de passe: celui défini dans `.env` (`DB_PASSWORD`)
    *   Base de données : celle définie dans `.env` (`DB_DATABASE` pour la base centrale)

### Multi-Location en Local

1.  **Configuration du fichier `hosts`**:
    Pour accéder aux tenants par leurs sous-domaines (ex: `client1.localhost`), vous devez modifier le fichier `hosts` de votre système :
    *   macOS/Linux : `/etc/hosts`
    *   Windows : `C:\Windows\System32\drivers\etc\hosts` (modifiez en tant qu'administrateur)
    Ajoutez des entrées pour chaque domaine de tenant que vous prévoyez d'utiliser, pointant vers `127.0.0.1` :
    ```
    127.0.0.1 localhost
    127.0.0.1 client1.localhost
    127.0.0.1 autreclient.localhost
    ```
2.  **Création des Tenants**:
    *   La création des tenants se fait généralement via votre application (interface d'administration centrale).
    *   Lorsque vous créez un tenant (une `Organization` dans ce projet) et lui assignez un domaine (ex: `client1`), `stancl/tenancy` s'occupe de l'associer.
    *   Le script `docker-entrypoint.sh` (via `tenants:setup` ou `tenants:migrate`) tentera de configurer la base de données pour les tenants déjà enregistrés dans la base de données centrale.
    *   Si un tenant est créé après le démarrage initial, assurez-vous que votre logique applicative déclenche la création de sa base de données, les migrations et le seeding (Stancl/Tenancy fournit des Jobs comme `CreateDatabase`, `MigrateDatabase`, `SeedDatabase` pour cela). La commande `tenants:setup` est conçue pour gérer les tenants existants.

### Commandes Docker Compose Utiles

*   **Voir les logs des services** (en temps réel) :
    ```bash
    docker-compose logs -f
    docker-compose logs -f app # Logs spécifiques au service 'app'
    ```
*   **Arrêter les services** :
    ```bash
    docker-compose down
    ```
*   **Arrêter et supprimer les volumes** (ATTENTION : supprime les données de la DB, Redis, etc.) :
    ```bash
    docker-compose down -v
    ```
*   **Exécuter une commande Artisan** :
    ```bash
    docker-compose exec app php artisan <votre_commande>
    # Exemple:
    docker-compose exec app php artisan migrate:status
    docker-compose exec app php artisan tenants:list
    ```
*   **Ouvrir un shell Bash dans le conteneur `app`** :
    ```bash
    docker-compose exec app bash
    ```
*   **Reconstruire une image spécifique** :
    ```bash
    docker-compose build app
    ```

## 9. Passage à Docker Swarm (Production/Staging)

Docker Swarm est l'outil d'orchestration natif de Docker. Il permet de gérer un cluster de plusieurs machines Docker (nœuds) et de déployer des applications distribuées et scalables.

### Pourquoi Docker Swarm ?

*   **Scalabilité**: Augmenter ou diminuer facilement le nombre de réplicas de vos services.
*   **Haute Disponibilité**: Répartition des conteneurs sur plusieurs nœuds ; redémarrage automatique en cas d'échec.
*   **Gestion Simplifiée**: Commandes similaires à Docker Compose.
*   **Load Balancing Intégré**: Répartit le trafic entrant entre les réplicas d'un service.
*   **Déploiements Continus (Rolling Updates)**: Mises à jour sans interruption de service.

### Principes Clés pour Swarm

*   **Images Immuables**: Les images Docker doivent contenir tout le code et les dépendances. Pas de montage de volume pour le code source en production. Les mises à jour se font en déployant une nouvelle version de l'image.
*   **Services vs Conteneurs**: Vous définissez des "services" dans Swarm (ex: 3 réplicas du service `app`). Swarm gère la création et la surveillance des conteneurs (appelés "tasks").
*   **Configuration Centralisée**: Utilisation de Docker Secrets pour les données sensibles et Docker Configs pour les fichiers de configuration.
*   **Réseaux Overlay**: Permettent la communication entre conteneurs sur différents nœuds du cluster.
*   **Stockage Persistent Distribué**: Crucial pour les services stateful (bases de données). Nécessite des drivers de volume compatibles Swarm (NFS, Ceph, GlusterFS) ou l'utilisation de services de base de données managés externes (AWS RDS, Azure SQL Database, etc.).
*   **Logging et Monitoring Centralisés**: Indispensable pour suivre l'état de santé d'une application distribuée.

## 10. Le Fichier `docker-stack.yml` : Déploiement sur Docker Swarm

Le fichier `docker-stack.yml` décrit l'état désiré de votre application sur le cluster Swarm. Sa syntaxe est très proche de `docker-compose.yml` version 3+.

```yaml
version: '3.8'

# ... (Contenu du docker-stack.yml généré précédemment) ...
# Voir le fichier docker-stack.yml pour le contenu complet.
# Les points importants sont expliqués ci-dessous.
```

*(Le contenu complet du `docker-stack.yml` est celui que j'ai généré précédemment. Je vais ici expliquer ses sections clés).*

### Structure Générale du `docker-stack.yml`

*   `version: '3.8'`: Spécifie la version de la syntaxe du fichier.
*   `networks`: Définit les réseaux overlay pour la communication inter-services.
*   `volumes`: Définit les volumes, mais en Swarm, leur gestion pour la persistance multi-nœuds est plus complexe (voir "Stockage Persistent").
*   `secrets`: Référence les secrets Docker créés en amont dans le Swarm.
*   `configs`: Référence les configurations Docker créées en amont.
*   `services`: Définit chaque composant de votre application.

### Gestion des Images Applicatives

*   Dans `docker-stack.yml`, la directive `image` pour vos services applicatifs (comme `app`, `queue`, `scheduler`) doit pointer vers une image hébergée sur un **registre Docker** accessible par tous les nœuds de votre Swarm.
    ```yaml
    services:
      app:
        image: your-registry/your-app:${APP_VERSION:-latest}
    ```
    Remplacez `your-registry/your-app` par le chemin vers votre image (ex: `hub.docker.com/username/app`, `gitlab.example.com:5050/group/project/image`, `accountid.dkr.ecr.region.amazonaws.com/imagename`). `APP_VERSION` peut être un tag (ex: `v1.2.3`, `latest`).

### Gestion Cruciale des Secrets Docker

En production, **NE JAMAIS** coder en dur les mots de passe, clés d'API, ou autres informations sensibles. Utilisez Docker Secrets.

1.  **Création des Secrets dans Swarm** (à faire une seule fois par secret, ou lors de la mise à jour) :
    ```bash
    # Pour un mot de passe de base de données
    echo "MonMotDePasseSuperSecret" | docker secret create db_password -
    # Pour la clé d'application Laravel
    echo "base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx=" | docker secret create app_key -
    # Pour le mot de passe root de MySQL
    echo "MonMotDePasseRoot" | docker secret create db_root_password -
    ```
    Le `-` à la fin indique que la valeur du secret est lue depuis l'entrée standard (stdin).
2.  **Référencer les Secrets dans `docker-stack.yml`** :
    ```yaml
    secrets:
      app_key:
        external: true # Indique que le secret existe déjà dans Swarm
      db_password:
        external: true

    services:
      app:
        image: ...
        secrets:
          - app_key # Rend le secret 'app_key' accessible dans le conteneur
          - db_password
        environment:
          APP_KEY_FILE: /run/secrets/app_key # Laravel lira la clé depuis ce fichier
          DB_PASSWORD_FILE: /run/secrets/db_password # idem pour le mot de passe
    ```
3.  **Adapter l'Application pour Lire les Secrets Fichiers** :
    *   Laravel permet de spécifier un chemin de fichier pour certaines configurations. Par exemple, pour la base de données, vous pouvez modifier `config/database.php` pour lire le mot de passe depuis le fichier pointé par `DB_PASSWORD_FILE`.
    *   Une approche plus simple est d'utiliser le script d'entrée (`docker-entrypoint.sh`) pour lire le contenu des fichiers secrets et les exporter en tant que variables d'environnement classiques avant de démarrer l'application. **Attention :** cela rend le secret visible en tant que variable d'environnement dans le conteneur, ce qui est moins sécurisé que de le lire directement depuis le fichier secret monté.
        ```bash
        # Dans docker-entrypoint.sh (pour Swarm)
        if [ -f "/run/secrets/app_key" ]; then
          export APP_KEY=$(cat /run/secrets/app_key)
        fi
        if [ -f "/run/secrets/db_password" ]; then
          export DB_PASSWORD=$(cat /run/secrets/db_password)
        fi
        # ... puis exec "$@"
        ```
        Si vous adoptez cette méthode, assurez-vous que votre `Dockerfile` pour l'image de production inclut un `ENTRYPOINT` qui exécute ce script modifié.

### Gestion des Configurations Docker

Similaire aux secrets, mais pour les fichiers de configuration non sensibles.

1.  **Création des Configs dans Swarm** :
    ```bash
    docker config create nginx_config ./docker/nginx/default.conf
    # Pour une config PHP personnalisée
    # docker config create php_custom_ini ./docker/php/custom.ini
    ```
2.  **Référencer les Configs dans `docker-stack.yml`** :
    ```yaml
    configs:
      nginx_config:
        external: true

    services:
      web: # Service Nginx
        image: nginx:alpine
        configs:
          - source: nginx_config # Nom du config dans Swarm
            target: /etc/nginx/conf.d/default.conf # Chemin dans le conteneur
    ```

### Services dans le Stack Swarm

*   **`app`, `queue`, `scheduler`**:
    *   Utilisent l'image de votre application poussée sur un registre.
    *   La section `deploy` contrôle le nombre de `replicas`, la stratégie de mise à jour (`update_config`), la politique de redémarrage (`restart_policy`), et potentiellement les contraintes de placement (`placement`).
    *   **Pas de `build`**: L'image est tirée du registre.
    *   **Pas de `volumes` pour le code source**: Le code est dans l'image.
*   **`web` (Nginx)**:
    *   Utilise une image Nginx standard.
    *   Monte la configuration Nginx via Docker Configs.
    *   Les ports (80, 443) sont généralement exposés via un Ingress Controller plutôt que directement sur chaque réplica Nginx, sauf si Nginx lui-même fait office d'Ingress.
*   **`db` (MySQL), `redis`**:
    *   **Réplicas généralement à 1** à moins d'utiliser des solutions de clustering spécifiques (MySQL Cluster/Galera, Redis Cluster) qui sont complexes à gérer en auto-hébergé.
    *   **Stockage Persistent CRUCIAL** (voir section suivante).
    *   Peuvent être contraints à des nœuds spécifiques avec des labels (`placement`).

### Stockage Persistent en Swarm

C'est l'un des aspects les plus critiques et complexes.

*   **Bases de Données (`db`) et Redis (si persistant)**:
    *   Les volumes Docker par défaut sont liés au nœud. Si un conteneur `db` est redémarré sur un autre nœud, il perdra ses données.
    *   **Solutions**:
        1.  **Services de Base de Données Managés Externes**: AWS RDS, Azure Database, Google Cloud SQL. C'est souvent la solution la plus simple et la plus robuste en production. L'application s'y connecte via le réseau.
        2.  **Drivers de Volume pour Stockage Distribué**:
            *   **NFS**: Monter un partage NFS sur tous les nœuds Swarm et l'utiliser comme backend pour les volumes Docker.
            *   **GlusterFS, Ceph**: Systèmes de fichiers distribués plus complexes à mettre en place.
            *   **Solutions Commerciales/Open Source**: Portworx, StorageOS, Longhorn (plus orienté K8s mais des concepts similaires).
            Ces drivers permettent aux volumes de suivre les conteneurs à travers les nœuds.
    *   Dans `docker-stack.yml`, vous spécifiez le `driver` et les `driver_opts` pour le volume :
        ```yaml
        volumes:
          db-data:
            driver: your-nfs-driver # ou autre
            driver_opts:
              share: "nfs-server:/path/to/db_data"
        ```
*   **Fichiers des Tenants (`storage/tenant...`)**:
    *   Si vous utilisez le stockage local pour les fichiers des tenants (comme configuré pour le développement), cela ne fonctionnera pas en Swarm car chaque réplica `app` aurait son propre stockage isolé.
    *   **Solution Recommandée : Stockage Objet (S3)**:
        *   Configurer Laravel pour utiliser un disque `s3` (via `config/filesystems.php`).
        *   Utiliser AWS S3, ou un service compatible S3 (MinIO, DigitalOcean Spaces, etc.).
        *   Les identifiants AWS (ou équivalents) doivent être gérés via Docker Secrets ou des rôles IAM si hébergé sur AWS.
        *   Mettre à jour `config/tenancy.php` pour que le `FILESYSTEM_DISK` par défaut pour les tenants soit `s3`.
    *   **Alternative : Volume Partagé (NFS, etc.)**: Monter un volume partagé (ex: NFS) sur tous les nœuds où les services `app` et `web` (si Nginx sert les fichiers directement) s'exécutent. Le répertoire `storage` de Laravel pointerait vers ce montage. Moins scalable et plus complexe à gérer que S3 pour les fichiers.

### Migrations et Tâches d'Initialisation

L'exécution de `php artisan migrate` ou `tenants:setup` dans le `docker-entrypoint.sh` de chaque réplica `app` est problématique en Swarm (risques de conflits, exécutions multiples).

*   **Stratégies**:
    1.  **Tâches Ponctuelles (One-Off Tasks)**:
        La meilleure approche. Avant de mettre à jour le service `app` principal, lancez un conteneur temporaire basé sur la même image pour exécuter les migrations.
        ```bash
        # Exemple pour migrations centrales
        docker service create \
          --name myapp_migrations \
          --network app-network \ # Même réseau que les autres services
          --secret app_key \
          --secret db_password \
          # ... autres secrets et variables d'env nécessaires ...
          -e DB_HOST=db \
          -e APP_ENV=production \
          --restart-condition none \ # Ne pas redémarrer après exécution
          your-registry/your-app:${APP_VERSION} \
          php artisan migrate --force --seed # Le --seed est optionnel

        # Attendre la fin, puis supprimer le service de migration
        # docker service logs myapp_migrations -f
        # docker service rm myapp_migrations

        # Idem pour tenants:setup ou tenants:migrate
        docker service create --name myapp_tenant_setup ... your-registry/your-app:${APP_VERSION} php artisan tenants:setup
        ```
        Ce processus doit être intégré à votre pipeline CI/CD.
    2.  **Optimisations Laravel dans l'Image**: Les commandes comme `php artisan config:cache`, `route:cache`, `view:cache` doivent être exécutées lors de la **construction de l'image Docker** (`Dockerfile`) pour la production, pas au démarrage du conteneur. Cela rend les images plus rapides à démarrer et vraiment immuables.
        ```dockerfile
        # Dans le Dockerfile, après composer install et la copie du code :
        RUN php artisan config:cache && \
            php artisan route:cache && \
            php artisan view:cache && \
            # php artisan event:cache # si utilisé
        ```

### Ingress, Reverse Proxy et Domaines/SSL

Pour exposer votre application au monde et gérer les domaines des tenants.

*   **Ingress Controller**: Un service qui gère le trafic entrant vers votre cluster Swarm.
    *   **Traefik** est un choix populaire et s'intègre bien avec Docker Swarm. Il peut découvrir automatiquement les services et configurer le routage via des labels Docker.
    *   Nginx peut aussi être utilisé comme Ingress, mais sa configuration dynamique en Swarm est moins native.
*   **Configuration DNS**:
    *   Le domaine principal de votre application (ex: `votredomaine.com`) et un enregistrement wildcard (`*.votredomaine.com`) doivent pointer vers l'adresse IP de votre (ou vos) nœud(s) Ingress Swarm.
*   **SSL/TLS**:
    *   L'Ingress Controller (ex: Traefik) peut gérer automatiquement la création et le renouvellement de certificats SSL/TLS via Let's Encrypt.
*   **Exemple avec Traefik (labels dans `docker-stack.yml` pour le service `web` ou `app`)**:
    ```yaml
    services:
      web: # Ou directement le service 'app' si Traefik parle à PHP-FPM
        # ...
        deploy:
          labels:
            - "traefik.enable=true"
            # Routeur pour le domaine central
            - "traefik.http.routers.${APP_NAME:-myapp}-main.rule=Host(`${CENTRAL_DOMAIN:-central.example.com}`)"
            - "traefik.http.routers.${APP_NAME:-myapp}-main.entrypoints=websecure" # websecure = HTTPS
            - "traefik.http.routers.${APP_NAME:-myapp}-main.tls.certresolver=myresolver" # myresolver = config Let's Encrypt dans Traefik
            - "traefik.http.services.${APP_NAME:-myapp}-main-svc.loadbalancer.server.port=80" # Port interne du service web/nginx

            # Routeur pour les domaines des tenants (wildcard)
            # Attention: La gestion des domaines des tenants avec Traefik peut nécessiter une configuration plus avancée
            # ou un service dédié pour la découverte/routage dynamique si les domaines sont nombreux et changent souvent.
            # Une approche simple est un routeur wildcard qui pointe vers le service, Laravel/Stancl gérant ensuite le tenant.
            - "traefik.http.routers.${APP_NAME:-myapp}-tenants.rule=HostRegexp(`{subdomain:.+}.${CENTRAL_DOMAIN:-central.example.com}`)"
            - "traefik.http.routers.${APP_NAME:-myapp}-tenants.entrypoints=websecure"
            - "traefik.http.routers.${APP_NAME:-myapp}-tenants.tls.certresolver=myresolver"
            # Le service est le même que pour le domaine principal
            - "traefik.http.services.${APP_NAME:-myapp}-tenants-svc.loadbalancer.server.port=80"
    ```
    Traefik lui-même serait déployé comme un autre service dans votre Swarm.

## 11. Déploiement sur Docker Swarm : Étapes Pratiques

### Prérequis pour Swarm

1.  **Un cluster Docker Swarm initialisé et fonctionnel.**
    *   `docker swarm init` sur le premier nœud (manager).
    *   `docker swarm join --token ...` sur les autres nœuds pour les ajouter au cluster.
2.  **Un registre Docker configuré** et accessible par les nœuds Swarm.
3.  **Votre image applicative (`your-registry/your-app:tag`) poussée sur ce registre.**
4.  **Les secrets et configurations Docker créés dans le Swarm.**
5.  **Solution de stockage persistent configurée** si vous n'utilisez pas de services managés externes.
6.  **Ingress Controller (ex: Traefik) déployé et configuré** (si non géré manuellement).

### Étapes de Déploiement

1.  **Préparer le fichier `docker-stack.yml`**:
    *   Adaptez le fichier `docker-stack.yml` fourni avec vos noms d'image, de secrets, de configs, et les spécificités de votre infrastructure (stockage, etc.).
2.  **Déployer le Stack**:
    Sur un nœud manager de votre Swarm :
    ```bash
    docker stack deploy -c docker-stack.yml nom_de_votre_stack
    ```
    (ex: `docker stack deploy -c docker-stack.yml mylaravelapp`)
3.  **Vérifier le Déploiement**:
    *   Lister les stacks : `docker stack ls`
    *   Lister les services du stack : `docker stack services nom_de_votre_stack`
    *   Voir les tâches (conteneurs) d'un service : `docker service ps nom_de_votre_stack_app`
    *   Voir les logs d'un service : `docker service logs nom_de_votre_stack_app -f`
4.  **Exécuter les Tâches Ponctuelles (Migrations, etc.)**:
    Comme décrit précédemment, utilisez `docker service create --restart-condition none ...` pour les migrations et autres tâches d'initialisation après le déploiement initial du code ou lors de mises à jour de schéma.
5.  **Tester l'Application**:
    *   Accédez via le domaine principal et les domaines des tenants.
    *   Vérifiez toutes les fonctionnalités clés.
6.  **Mises à Jour**:
    1.  Construire et pousser une nouvelle version de l'image (`your-registry/your-app:new-tag`).
    2.  Mettre à jour la définition du service dans `docker-stack.yml` (si nécessaire) ou directement le service :
        ```bash
        # Option A: Mettre à jour l'image pour un service spécifique
        docker service update --image your-registry/your-app:new-tag nom_de_votre_stack_app
        # Option B: Redéployer tout le stack si docker-stack.yml a été mis à jour
        docker stack deploy -c docker-stack.yml nom_de_votre_stack
        ```
        Swarm effectuera une mise à jour progressive (rolling update) selon la `update_config` définie.
    3.  N'oubliez pas d'exécuter les migrations *avant* de mettre à jour le service applicatif si la nouvelle version du code en dépend.

## 12. Considérations de Sécurité et Bonnes Pratiques Docker

*   **Images Minimales**: Utilisez des images de base minimales (comme Alpine) pour réduire la surface d'attaque.
*   **Utilisateurs Non-Root**: Exécutez les processus dans les conteneurs avec des utilisateurs non-root lorsque c'est possible (PHP-FPM s'exécute déjà avec `www-data`).
*   **Scan de Vulnérabilités**: Utilisez des outils comme Trivy, Clair, ou Docker Scan pour scanner vos images à la recherche de vulnérabilités connues.
*   **Mises à Jour Régulières**: Maintenez à jour les images de base, les dépendances système et applicatives.
*   **Secrets Management**: Utilisez **toujours** Docker Secrets (ou Vault) pour les informations sensibles. Ne les mettez jamais en dur dans les images ou les fichiers de configuration versionnés.
*   **Limitation des Ressources**: Définissez des limites de CPU et de mémoire pour vos services dans la section `deploy.resources` de `docker-stack.yml` pour éviter qu'un service ne consomme toutes les ressources d'un nœud.
*   **Réseaux Segmentés**: Utilisez des réseaux Docker pour isoler les services. N'exposez que les ports strictement nécessaires.
*   **Logging Centralisé**: Envoyez les logs des conteneurs vers un système centralisé (ELK, Graylog, Loki) pour analyse et audit.
*   **Monitoring**: Mettez en place un monitoring de l'état de santé du cluster Swarm, des nœuds, des services et de l'application (Prometheus, Grafana, etc.).
*   **Sauvegardes Régulières**: Pour les données persistantes (bases de données, volumes de fichiers importants).

Ce guide devrait vous fournir une base solide pour dockeriser votre application Laravel multi-tenant et la déployer, que ce soit en développement local ou sur un cluster Docker Swarm. N'oubliez pas que chaque application et chaque infrastructure a ses spécificités, donc une adaptation sera toujours nécessaire.
