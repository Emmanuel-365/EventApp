# Description du Diagramme de Composants de l'Application

Ce document décrit les principaux composants de l'application et leurs interactions, basés sur l'analyse de la structure du code Laravel.

## 1. Utilisateur

*   **Interface Utilisateur (Vues Blade & Livewire)** :
    *   **Responsabilité** : C'est le point d'interaction principal pour les utilisateurs finaux. Elle est construite avec les templates Blade de Laravel et les composants dynamiques Livewire. Elle affiche les informations et capture les entrées de l'utilisateur.
    *   **Interactions** : Envoie des requêtes HTTP (déclenchées par les actions de l'utilisateur) au **Routeur**. Reçoit et affiche les réponses (pages HTML, mises à jour dynamiques) des **Contrôleurs**.

## 2. Serveur Applicatif (Laravel)

*   **Routeur (routes/web.php, routes/tenant.php)** :
    *   **Responsabilité** : Intercepte toutes les requêtes HTTP entrantes. Il les achemine vers les **Contrôleurs** ou les composants Livewire appropriés en fonction des URL et des méthodes HTTP définies dans les fichiers de routes. Il existe des routes globales et des routes spécifiques aux locataires.
    *   **Interactions** : Reçoit les requêtes de l'**Interface Utilisateur**. Transmet les requêtes aux **Middlewares** avant d'atteindre les **Contrôleurs**.

*   **Middlewares (Auth, Rôles, Locataire)** :
    *   **Responsabilité** : Agissent comme des filtres pour les requêtes HTTP. Ils exécutent des logiques avant ou après qu'une requête atteigne sa destination finale. Les middlewares identifiés incluent ceux pour l'authentification (`Auth`), la gestion des rôles (par exemple, `AdminMiddleware`, `OrganizerMiddleware`), et l'identification du locataire (`TenantMiddleware` de stancl/tenancy).
    *   **Interactions** : Reçoivent les requêtes du **Routeur**. Peuvent interrompre le cycle de la requête (par exemple, en cas de non-autorisation) ou la transmettre aux **Contrôleurs**. Interagissent avec le système d'**Authentification & Autorisation** et la **Gestion des Locataires**.

*   **Contrôleurs (app/Http/Controllers, app/Livewire)** :
    *   **Responsabilité** : Traitent la logique de gestion des requêtes. Ils reçoivent les données des requêtes, interagissent avec les **Services Applicatifs** pour la logique métier et avec les **Modèles Eloquent** pour l'accès aux données. Ils préparent ensuite la réponse, souvent en retournant une vue à l'**Interface Utilisateur**. Les composants Livewire ont un rôle similaire pour les interactions dynamiques.
    *   **Interactions** : Reçoivent les requêtes des **Middlewares**. Utilisent les **Services Applicatifs** et les **Modèles Eloquent**. Utilisent le système d'**Authentification & Autorisation** pour vérifier les permissions. Peuvent déclencher l'envoi d'**Emails**. Retournent des réponses à l'**Interface Utilisateur**.

*   **Services Applicatifs (app/Services)** :
    *   **Responsabilité** : Encapsulent la logique métier complexe et réutilisable (par exemple, `BanService`, `OrganizationStatusService`, `OtpService`). Ils permettent de garder les **Contrôleurs** plus légers et de centraliser les opérations métier.
    *   **Interactions** : Sont utilisés par les **Contrôleurs** et les **Commandes Console**. Interagissent avec les **Modèles Eloquent** pour la manipulation des données. Peuvent utiliser d'autres services ou déclencher des **Emails**.

*   **Modèles Eloquent (app/Models) et Modèles Eloquent Spécifiques au Locataire (app/Models/Tenant)** :
    *   **Responsabilité** : Représentent les données de l'application et fournissent une interface orientée objet pour interagir avec la base de données (ORM Eloquent). Les modèles standards interagissent avec la **Base de Données Principale**, tandis que les modèles spécifiques au locataire (par exemple, `Employee`, `Patron`) interagissent avec les **Bases de Données des Locataires**.
    *   **Interactions** : Sont utilisés par les **Contrôleurs**, les **Services Applicatifs**, le système d'**Authentification & Autorisation**, et les **Commandes Console**. Accèdent et modifient les données dans les **Bases de Données** correspondantes.

*   **Gestion des Locataires (stancl/tenancy)** :
    *   **Responsabilité** : Gère l'architecture multi-locataire de l'application. Elle assure l'isolation des données entre les différentes organisations (locataires). Elle identifie le locataire actuel à partir de la requête (souvent via un sous-domaine) et configure dynamiquement la connexion à la base de données appropriée.
    *   **Interactions** : Est intégrée via des **Middlewares** pour identifier le locataire. Impacte la configuration de la base de données pour les **Modèles Eloquent Spécifiques au Locataire**. Gère la création et la maintenance des **Bases de Données des Locataires**. Est utilisée par les **Commandes Console** (par exemple, pour créer de nouveaux locataires).

*   **Système d'Authentification & Autorisation** :
    *   **Responsabilité** : Gère la connexion des utilisateurs (login, logout, réinitialisation de mot de passe) et la vérification de leurs droits d'accès aux différentes ressources et fonctionnalités. Ceci est basé sur les rôles (SuperAdmin, Admin, Organizer, Client) et potentiellement des permissions plus fines.
    *   **Interactions** : Utilise les **Modèles Eloquent** (comme `User`, `Admin`) pour stocker et vérifier les informations d'identification. Est invoqué par les **Middlewares** d'authentification et utilisé par les **Contrôleurs** pour les vérifications d'autorisation.

*   **Notifications & Emails (app/Mail)** :
    *   **Responsabilité** : Gère l'envoi d'emails transactionnels (par exemple, emails de bienvenue, notifications, OTP pour la vérification).
    *   **Interactions** : Est utilisé par les **Contrôleurs** ou les **Services Applicatifs** lorsqu'un email doit être envoyé.

*   **Commandes Console (app/Console/Commands)** :
    *   **Responsabilité** : Permet d'exécuter des tâches en arrière-plan ou des scripts de maintenance via l'interface en ligne de commande Artisan de Laravel (par exemple, `SetupTenants`, `ListTenantDomains`).
    *   **Interactions** : Peuvent utiliser les **Services Applicatifs**, les **Modèles Eloquent**, et interagir avec la **Gestion des Locataires**.

## 3. Infrastructure

*   **Base de Données Principale** :
    *   **Responsabilité** : Stocke les données globales de l'application, telles que la liste des utilisateurs centraux (SuperAdmins, Admins globaux), la liste des locataires (organisations), et d'autres configurations centrales.
    *   **Interactions** : Est accédée par les **Modèles Eloquent** standards.

*   **Bases de Données des Locataires** :
    *   **Responsabilité** : Chaque locataire (organisation) possède sa propre base de données pour stocker ses données spécifiques (par exemple, employés, clients du locataire, événements de l'organisation). Cela garantit l'isolation des données.
    *   **Interactions** : Sont accédées par les **Modèles Eloquent Spécifiques au Locataire**, après que la **Gestion des Locataires** ait configuré la connexion appropriée.
```
