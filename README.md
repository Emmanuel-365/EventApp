# EventApp - Plateforme de Gestion d'Événements Multi-Tenant

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.2-8892BF.svg)](https://php.net/)
[![Laravel Version](https://img.shields.io/badge/laravel-11.x-FF2D20.svg)](https://laravel.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

EventApp est une application web robuste conçue pour la gestion complète d'événements. Grâce à son architecture multi-tenant, elle permet à différentes organisations de gérer leurs propres événements, employés, et clients de manière totalement isolée, avec une base de données et un sous-domaine dédiés pour chaque locataire (tenant).

## ✨ Fonctionnalités Principales

- **Architecture Multi-Tenant** : Chaque organisation (tenant) dispose de sa propre base de données, de son propre stockage de fichiers et d'un accès par sous-domaine, garantissant une isolation complète des données.
- **Gestion des Rôles & Permissions** : Système de rôles hiérarchiques prédéfinis :
    - **Super Admin** : Gère l'ensemble de la plateforme, les administrateurs et la configuration globale.
    - **Admin** : Gère les organisations et les organisateurs.
    - **Organisateur** : Gère les événements, les employés et les clients pour sa propre organisation.
    - **Employé** : Gère les détails des événements auxquels il est assigné.
- **Gestion d'Événements** : Création, mise à jour, publication et suivi des événements.
- **Synchronisation Centralisée** : Les événements des tenants peuvent être synchronisés avec une base de données centrale pour un affichage public ou des analyses globales.
- **Authentification Sécurisée** : Système d'authentification complet avec réinitialisation de mot de passe et protection des routes.
- **Déploiement avec Docker** : Entièrement conteneurisé avec Docker et Docker Compose pour une installation et un déploiement simplifiés.

## 🛠️ Stack Technique

- **Backend** : PHP 8.2+, Laravel 11
- **Frontend** : Laravel Blade, Livewire 3, Tailwind CSS, Vite.js
- **Base de données** : MySQL (par défaut)
- **Serveur Web** : Nginx
- **Conteneurisation** : Docker, Docker Compose
- **Multi-tenancy** : `stancl/tenancy-for-laravel`

## 🚀 Installation et Démarrage

Ce projet est conçu pour être exécuté avec Docker. Assurez-vous d'avoir **Docker** et **Docker Compose** installés sur votre machine.

### 1. Cloner le Dépôt

```bash
git clone https://github.com/votre-utilisateur/EventApp.git
cd EventApp
```

### 2. Configuration de l'Environnement

Copiez les fichiers d'environnement d'exemple. Le fichier `.env` sera utilisé par Laravel et les commandes Artisan, tandis que `.env.docker-example` est utilisé par Docker Compose pour la configuration des services (comme la base de données).

```bash
# Copier le fichier d'environnement pour Laravel
cp .env.example .env

# Copier le fichier d'environnement pour Docker
cp .env.docker-example .env.docker
```

Modifiez le fichier `.env` et `.env.docker` si nécessaire (par exemple, pour changer les ports ou les identifiants de la base de données).

### 3. Démarrer les Conteneurs Docker

Construisez et démarrez les services (Nginx, PHP, MySQL) en arrière-plan.

```bash
docker-compose up -d --build
```

### 4. Installer les Dépendances

Exécutez `composer install` et `npm install` à l'intérieur du conteneur `app`.

```bash
# Installer les dépendances PHP
docker-compose exec app composer install

# Installer les dépendances Node.js
docker-compose exec app npm install
```

### 5. Configuration de l'Application

Exécutez les commandes suivantes pour finaliser la configuration de Laravel.

```bash
# Générer la clé d'application Laravel
docker-compose exec app php artisan key:generate

# Exécuter les migrations pour la base de données centrale (landlord)
docker-compose exec app php artisan migrate --seed

# Compiler les assets frontend
docker-compose exec app npm run build
```

### 6. Accéder à l'Application

Une fois toutes les étapes terminées, l'application principale devrait être accessible à l'adresse :

- **Application** : [http://localhost](http://localhost) (ou le port que vous avez configuré)

## Tenant (Locataire)

L'application est multi-tenant. Pour créer de nouveaux tenants (organisations) et voir leurs domaines, vous pouvez utiliser les commandes Artisan personnalisées.

### Créer des Tenants de Démonstration

Une commande a été créée pour peupler le système avec des données de démonstration.

```bash
docker-compose exec app php artisan app:setup-tenants
```

### Lister les Domaines des Tenants

Pour voir la liste de tous les domaines des tenants enregistrés :

```bash
docker-compose exec app php artisan app:list-tenant-domains
```

Chaque tenant sera accessible via un sous-domaine, par exemple `http://organisation-a.localhost`. Vous devrez peut-être ajouter ces domaines à votre fichier `hosts` local pour qu'ils pointent vers `127.0.0.1`.

## ✅ Lancer les Tests

Pour exécuter la suite de tests PHPUnit :

```bash
docker-compose exec app php artisan test
```

## 🤝 Contribution

Les contributions sont les bienvenues ! Veuillez suivre les étapes suivantes :
1. Fork le projet.
2. Créez une nouvelle branche (`git checkout -b feature/nouvelle-fonctionnalite`).
3. Committez vos changements (`git commit -am 'Ajout d'une nouvelle fonctionnalité'`).
4. Pushez vers la branche (`git push origin feature/nouvelle-fonctionnalite`).
5. Ouvrez une Pull Request.

