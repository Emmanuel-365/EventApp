# Description des Packages de l'Application

Ce document décrit les principaux packages (espaces de noms) de l'application, leurs responsabilités et leurs interactions.

## Structure Globale

L'application suit une architecture modulaire, principalement organisée autour des fonctionnalités clés et des rôles utilisateurs. Les packages principaux sont situés dans le répertoire `app/`, mais d'autres répertoires comme `config/`, `database/`, `routes/`, et `resources/` jouent également des rôles cruciaux dans la structure globale.

## Descriptions des Packages Principaux

### 1. `App` (Application Core)
C'est le cœur de l'application Laravel.

   - #### `App\Console`
     - **Rôle:** Contient les commandes Artisan personnalisées. Ces commandes sont utilisées pour des tâches en ligne de commande, comme la configuration des locataires (`SetupTenants`) ou la liste des domaines (`ListTenantDomains`).
     - **Interactions:** Peut interagir avec les `Services` et les `Models` pour effectuer ses tâches.

   - #### `App\Enums`
     - **Rôle:** Définit les énumérations (Enums) utilisées à travers l'application, comme `TypeEntreprise`. Cela aide à maintenir la cohérence des valeurs spécifiques.

   - #### `App\Http`
     - **Rôle:** Gère tout ce qui concerne les requêtes HTTP et les réponses.
       - **`App\Http\Controllers`**: Contient les contrôleurs qui gèrent la logique de réception des requêtes et de retour des réponses. Ils sont organisés par rôles (`Admin`, `Organizer`, `SuperAdmin`) et un contrôleur de base.
       - **`App\Http\Middleware`**: Contient les middlewares qui filtrent les requêtes HTTP entrantes (par exemple, pour l'authentification ou l'autorisation spécifique à un rôle).
     - **Interactions:** Les `Controllers` utilisent les `Services` pour la logique métier et les `Models` pour l'accès aux données. Ils retournent souvent des `Resources\Views`. Les `Middleware` peuvent utiliser des `Services` pour vérifier les permissions.

   - #### `App\Livewire`
     - **Rôle:** Contient les composants Livewire, qui permettent de créer des interfaces utilisateur dynamiques avec PHP. Ces composants sont également souvent organisés par fonctionnalités ou rôles (`Admin`, `Auth`, `GestionRoles`, `Organization`, `SuperAdmin`).
     - **Interactions:** Les composants `Livewire` interagissent fortement avec les `Services` pour la logique métier et les `Models` pour les données. Ils rendent des fragments de `Resources\Views`.

   - #### `App\Mail`
     - **Rôle:** Contient les classes Mailable pour l'envoi d'e-mails (par exemple, `OtpMail` pour l'envoi de mots de passe à usage unique).
     - **Interactions:** Généralement utilisés par les `Services` pour envoyer des notifications ou des informations par e-mail.

   - #### `App\Models`
     - **Rôle:** Contient les modèles Eloquent qui représentent les tables de la base de données et gèrent les interactions avec celles-ci.
       - **`App\Models\Tenant`**: Sous-espace de noms pour les modèles spécifiques à la logique multi-locataire (par exemple, `Employee`, `Patron`).
     - **Interactions:** Utilisés par presque tous les autres packages qui nécessitent un accès aux données (Contrôleurs, Services, Livewire, Commandes, Seeders, Factories).

   - #### `App\Providers`
     - **Rôle:** Contient les fournisseurs de services qui enregistrent et configurent les services, les événements, les routes, et d'autres aspects de l'application au démarrage.
     - **Interactions:** Peuvent lire la `Config` et enregistrer/configurer des `Services`.

   - #### `App\Services`
     - **Rôle:** Contient la logique métier principale de l'application (par exemple, `BanService`, `OrganizationStatusService`, `OtpService`). Ce package vise à découpler la logique métier des contrôleurs et des composants Livewire.
     - **Interactions:** Utilisent les `Models` pour l'accès aux données et peuvent utiliser d'autres `Services` ou des classes `Mail`. Sont utilisés par les `Controllers`, `Livewire`, et `Console Commands`.

### 2. `Config`
   - **Rôle:** Contient tous les fichiers de configuration de l'application (base de données, authentification, services tiers, etc.).
   - **Interactions:** Lus par les `Providers` et d'autres parties du framework Laravel pour initialiser les paramètres.

### 3. `Database`
   - **Rôle:** Gère la structure et les données initiales de la base de données.
     - **`Database\Factories`**: Contient les usines de modèles pour générer des données de test.
     - **`Database\Migrations`**: Contient les migrations pour créer et modifier le schéma de la base de données (y compris les migrations spécifiques aux locataires dans `Database\Migrations\Tenant`).
     - **`Database\Seeders`**: Contient les classes pour peupler la base de données avec des données initiales ou de démonstration.
   - **Interactions:** Les `Migrations` définissent la structure pour les `Models`. Les `Seeders` et `Factories` créent des instances de `Models`.

### 4. `Resources`
   - **Rôle:** Contient les assets non compilés et les vues.
     - **`Resources\Views`**: Contient les templates Blade utilisés pour rendre l'interface utilisateur.
   - **Interactions:** Les `Views` sont rendues par les `Controllers` et les composants `Livewire`.

### 5. `Routes`
   - **Rôle:** Définit les routes de l'application.
     - **`Routes\Web.php`**: Routes pour les navigateurs web.
     - **`Routes\Console.php`**: Routes pour les commandes Artisan.
     - **`Routes\Tenant.php`**: Routes spécifiques aux locataires.
   - **Interactions:** Mappent les URLs aux `Controllers` et aux composants `Livewire`. Les routes de console définissent les `Console Commands`.

### 6. `Tests`
   - **Rôle:** Contient les tests automatisés de l'application.
     - **`Tests\Feature`**: Tests d'intégration qui testent des fonctionnalités complètes de l'application (par exemple, des requêtes HTTP).
     - **`Tests\Unit`**: Tests unitaires qui testent des composants isolés (par exemple, une méthode spécifique d'un `Service` ou d'un `Model`).
   - **Interactions:** Les tests `Feature` interagissent avec les `Controllers`, `Livewire`, et d'autres composants via des requêtes HTTP simulées. Les tests `Unit` instancient et testent directement des classes comme les `Services` ou les `Models`.

## Diagramme Visuel

Pour une représentation visuelle des packages et de leurs dépendances, veuillez consulter le fichier `diagramme_packages.plantuml`.
