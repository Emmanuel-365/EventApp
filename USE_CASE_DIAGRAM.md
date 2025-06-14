# Diagramme des Cas d'Utilisation – Description Textuelle

Ce document décrit les acteurs et les cas d'utilisation du système de gestion de plateforme multi-tenant.

## 1. Acteurs Principaux

*   **Utilisateur Non Authentifié / Visiteur**: Personne n'ayant pas encore ouvert de session.
*   **SuperAdmin**: Administrateur principal de la plateforme.
*   **Admin**: Personnel autorisé à gérer les opérations courantes.
*   **Organisateur**: Utilisateur créant et gérant des Organisations (tenants).
*   **Client**: Utilisateur final des services d'une Organisation (rôle moins défini au niveau central).
*   **Système**: Processus automatisés et fonctionnalités transverses.

## 2. Cas d'Utilisation par Acteur

### 2.1. Utilisateur Non Authentifié / Visiteur

Personne n'ayant pas encore ouvert de session sur la plateforme.

*   **Cas d'utilisation :**
    *   `Consulter la page de connexion SuperAdmin` : Permet au visiteur d'accéder au formulaire de connexion destiné au SuperAdmin.
    *   `Consulter la page de connexion Admin` : Permet au visiteur d'accéder au formulaire de connexion destiné aux Admins.
    *   `Consulter la page d'inscription Admin` : Permet au visiteur d'accéder au formulaire d'inscription pour devenir Admin.
    *   `Consulter la page de connexion Organisateur` : Permet au visiteur d'accéder au formulaire de connexion destiné aux Organisateurs.
    *   `Consulter la page d'inscription Organisateur` : Permet au visiteur d'accéder au formulaire d'inscription pour devenir Organisateur.
    *   `Demander la réinitialisation du mot de passe` : Permet à un utilisateur ayant oublié son mot de passe d'initier le processus de réinitialisation.

### 2.2. SuperAdmin

L'administrateur principal de la plateforme, responsable de la gestion globale. Il ne peut y avoir qu'un seul SuperAdmin.

*   **Cas d'utilisation :**
    *   `Se connecter au panneau SuperAdmin` : Accéder à son interface de gestion.
        *   *Étend* : `Consulter la page de connexion SuperAdmin`.
    *   `Se déconnecter` : Fermer sa session.
    *   `Consulter son profil` : Voir les informations de son propre compte.
    *   `Gérer les Admins` : Gérer les comptes Admin.
        *   *Inclut* : `Créer un compte Admin`, `Voir la liste des Admins`, `Modifier les détails d'un Admin`, `Supprimer un Admin`.

### 2.3. Admin

Personnel autorisé à gérer les opérations courantes de la plateforme.

*   **Cas d'utilisation :**
    *   `Se connecter au panneau Admin` : Accéder à son interface de gestion.
        *   *Étend* : `Consulter la page de connexion Admin`.
    *   `S'inscrire en tant qu'Admin` : Créer un compte Admin.
        *   *Étend* : `Consulter la page d'inscription Admin`.
    *   `Se déconnecter` : Fermer sa session.
    *   `Consulter son profil` : Voir les informations de son propre compte.
    *   `Gérer les Organisateurs` : Gérer les comptes Organisateur.
        *   *Inclut* : `Créer un compte Organisateur`, `Voir la liste des Organisateurs`, `Modifier les détails d'un Organisateur`, `Supprimer un Organisateur`, `Vérifier le profil d'un Organisateur`, `Bannir un Organisateur`.
    *   `Gérer les Organisations (Tenants)` : Gérer les entités tenants.
        *   *Inclut* : `Créer une Organisation`, `Voir la liste des Organisations`, `Modifier les détails d'une Organisation`, `Valider une Organisation`, `Activer/Désactiver une Organisation`, `Consulter l'historique des statuts d'une Organisation`.
    *   `Gérer les affectations Organisateur-Organisation` : Associer ou dissocier des Organisateurs à des Organisations.
        *   *Inclut* : `Assigner un Organisateur à une Organisation`, `Révoquer un Organisateur d'une Organisation`.
        *   *Permet de* : `Consulter les Organisations d'un Organisateur spécifique`.
    *   `Gérer les Rôles et Permissions` : Définir et assigner des rôles et permissions.

### 2.4. Organisateur

Utilisateur principal gérant des Organisations (tenants).

*   **Cas d'utilisation :**
    *   `Se connecter au portail Organisateur` : Accéder à son interface de gestion.
        *   *Étend* : `Consulter la page de connexion Organisateur`.
    *   `S'inscrire en tant qu'Organisateur` : Créer un compte Organisateur.
        *   *Étend* : `Consulter la page d'inscription Organisateur`.
    *   `Se déconnecter` : Fermer sa session.
    *   `Gérer son profil` : Voir et modifier les informations de son compte.
        *   *Inclut* : `Consulter son profil`, `Modifier son profil`.
        *   *Peut inclure* : `Soumettre une demande de vérification de profil` (qui *étend* ce cas, ex: `Télécharger des pièces d'identité`).
    *   `Gérer ses Organisations` : Gérer les Organisations créées ou assignées.
        *   *Inclut* : `Voir la liste de ses Organisations`.
        *   *Peut inclure* : `Créer une nouvelle Organisation`, `Modifier les détails d'une Organisation`.
    *   `Gérer les Employés de son Organisation` (Spécifique au tenant) : Gérer les employés au sein d'une de ses organisations (en tant que `Patron`).
    *   `Gérer les Patrons de son Organisation` (Spécifique au tenant) : Gérer les utilisateurs `Patron` au sein du tenant.
    *   `Gérer les Événements de son Organisation` (Spécifique au tenant) : Créer et gérer des événements pour une organisation.

### 2.5. Client

Rôle moins défini au niveau central, potentiellement utilisateur final des services d'une Organisation.

*   **Cas d'utilisation :**
    *   Non définis au niveau central. Pourraient inclure `S'inscrire à un événement`, `Acheter des produits/services` (spécifique au tenant).

### 2.6. Système

Processus automatisés et fonctionnalités transverses.

*   **Cas d'utilisation :**
    *   `Authentifier l'utilisateur` : Vérifier les identifiants.
    *   `Autoriser les actions de l'utilisateur` : Contrôler l'accès basé sur les rôles/permissions.
    *   `Provisionner la base de données du tenant` : Créer une base de données pour une nouvelle Organisation.
    *   `Synchroniser les données de l'Organisateur vers le tenant` : Mettre à jour les `Patron` dans les tenants.
    *   `Envoyer un email OTP` : Envoyer un code à usage unique.
    *   `Gérer le processus de réinitialisation de mot de passe` : Orchestrer la réinitialisation de mot de passe.
    *   `Enregistrer l'historique des changements de statut d'une Organisation` : Tracer les modifications de statut.
    *   `Générer des identifiants uniques` : Créer des ID uniques (ex: `matricule`).

## 3. Syntaxe pour Diagramme Visuel (Optionnel - PlantUML en Français)

Pour générer un diagramme visuel, la syntaxe PlantUML suivante peut être utilisée comme point de départ. Elle reflète les acteurs et une partie des cas d'utilisation identifiés, avec des termes en français.

\`\`\`plantuml
@startuml
left to right direction

actor Visiteur
actor SuperAdmin
actor Admin
actor Organisateur
actor Client
actor Systeme

rectangle "Gestion de la Plateforme" {
  usecase "Consulter page connexion SuperAdmin" as CU_VOIR_LOGIN_SA
  usecase "Consulter page connexion Admin" as CU_VOIR_LOGIN_ADMIN
  usecase "Consulter page inscription Admin" as CU_VOIR_INSCRIPTION_ADMIN
  usecase "Consulter page connexion Organisateur" as CU_VOIR_LOGIN_ORGA
  usecase "Consulter page inscription Organisateur" as CU_VOIR_INSCRIPTION_ORGA
  usecase "Demander réinitialisation MDP" as CU_DEMANDE_RESET_MDP

  Visiteur -- CU_VOIR_LOGIN_SA
  Visiteur -- CU_VOIR_LOGIN_ADMIN
  Visiteur -- CU_VOIR_INSCRIPTION_ADMIN
  Visiteur -- CU_VOIR_LOGIN_ORGA
  Visiteur -- CU_VOIR_INSCRIPTION_ORGA
  Visiteur -- CU_DEMANDE_RESET_MDP

  rectangle "Fonctions SuperAdmin" {
    usecase "Se connecter (SuperAdmin)" as CU_LOGIN_SA
    usecase "Gérer Admins" as CU_GERER_ADMINS
    usecase "Voir profil (SuperAdmin)" as CU_PROFIL_SA
    usecase "Se déconnecter (SuperAdmin)" as CU_LOGOUT_SA

    SuperAdmin -- CU_LOGIN_SA
    SuperAdmin -- CU_GERER_ADMINS
    SuperAdmin -- CU_PROFIL_SA
    SuperAdmin -- CU_LOGOUT_SA
    CU_LOGIN_SA ..> CU_VOIR_LOGIN_SA : <<étend>>
  }

  rectangle "Fonctions Admin" {
    usecase "Se connecter (Admin)" as CU_LOGIN_ADMIN
    usecase "S'inscrire (Admin)" as CU_INSCRIPTION_ADMIN
    usecase "Gérer Organisateurs" as CU_GERER_ORGAS
    usecase "Gérer Organisations (Tenants)" as CU_GERER_TENANTS
    usecase "Gérer Rôles/Permissions" as CU_GERER_ROLES
    usecase "Voir profil (Admin)" as CU_PROFIL_ADMIN
    usecase "Se déconnecter (Admin)" as CU_LOGOUT_ADMIN

    Admin -- CU_LOGIN_ADMIN
    Admin -- CU_INSCRIPTION_ADMIN
    Admin -- CU_GERER_ORGAS
    Admin -- CU_GERER_TENANTS
    Admin -- CU_GERER_ROLES
    Admin -- CU_PROFIL_ADMIN
    Admin -- CU_LOGOUT_ADMIN
    CU_LOGIN_ADMIN ..> CU_VOIR_LOGIN_ADMIN : <<étend>>
    CU_INSCRIPTION_ADMIN ..> CU_VOIR_INSCRIPTION_ADMIN : <<étend>>
  }

  rectangle "Fonctions Organisateur" {
    usecase "Se connecter (Organisateur)" as CU_LOGIN_ORGA
    usecase "S'inscrire (Organisateur)" as CU_INSCRIPTION_ORGA
    usecase "Gérer son profil (Organisateur)" as CU_GERER_PROFIL_ORGA
    usecase "Gérer ses Organisations" as CU_GERER_SES_TENANTS
    usecase "Se déconnecter (Organisateur)" as CU_LOGOUT_ORGA

    Organisateur -- CU_LOGIN_ORGA
    Organisateur -- CU_INSCRIPTION_ORGA
    Organisateur -- CU_GERER_PROFIL_ORGA
    Organisateur -- CU_GERER_SES_TENANTS
    Organisateur -- CU_LOGOUT_ORGA
    CU_LOGIN_ORGA ..> CU_VOIR_LOGIN_ORGA : <<étend>>
    CU_INSCRIPTION_ORGA ..> CU_VOIR_INSCRIPTION_ORGA : <<étend>>

    rectangle "Fonctions Tenant (par Organisateur)" {
      usecase "Gérer Employés" as CU_GERER_EMPLOYES
      usecase "Gérer Patrons" as CU_GERER_PATRONS
      usecase "Gérer Événements" as CU_GERER_EVENEMENTS
      Organisateur -- CU_GERER_EMPLOYES
      Organisateur -- CU_GERER_PATRONS
      Organisateur -- CU_GERER_EVENEMENTS
    }
  }

  CU_GERER_ADMINS ..> CU_LOGIN_SA : <<inclut>>
  CU_GERER_ORGAS ..> CU_LOGIN_ADMIN : <<inclut>>
  CU_GERER_TENANTS ..> CU_LOGIN_ADMIN : <<inclut>>
  CU_GERER_SES_TENANTS ..> CU_LOGIN_ORGA : <<inclut>>

  Systeme --|> Client : "peut interagir avec (non défini)"

}

rectangle "Processus Système" {
  usecase "Authentifier Utilisateur" as CU_AUTH_UTILISATEUR
  usecase "Autoriser Actions" as CU_AUTORISER_ACTION
  usecase "Provisionner DB Tenant" as CU_PROVISION_DB_TENANT
  usecase "Synchroniser Données Organisateur" as CU_SYNC_DONNEES_ORGA
  usecase "Envoyer Email OTP" as CU_ENVOYER_EMAIL_OTP
  usecase "Gérer Réinitialisation MDP" as CU_GERER_RESET_MDP
  usecase "Logger Changements Statut Org." as CU_LOG_STATUT_ORG
  usecase "Générer IDs Uniques" as CU_GEN_IDS_UNIQUES

  Systeme -- CU_AUTH_UTILISATEUR
  Systeme -- CU_AUTORISER_ACTION
  Systeme -- CU_PROVISION_DB_TENANT
  Systeme -- CU_SYNC_DONNEES_ORGA
  Systeme -- CU_ENVOYER_EMAIL_OTP
  Systeme -- CU_GERER_RESET_MDP
  Systeme -- CU_LOG_STATUT_ORG
  Systeme -- CU_GEN_IDS_UNIQUES
}

@enduml
\`\`\`

Ce fichier servira de documentation pour comprendre les interactions possibles avec le système.
