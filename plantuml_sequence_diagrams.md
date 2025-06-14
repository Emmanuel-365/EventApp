# Diagrammes de Séquence PlantUML

## 1. Connexion du SuperAdmin

Description en français : Le SuperAdmin demande à se connecter. Le système vérifie les identifiants. Si les identifiants sont valides, le SuperAdmin est connecté et redirigé vers son tableau de bord. Sinon, un message d'erreur est affiché.

```plantuml
@startuml
actor SuperAdmin as SA
participant Système as SYS

SA -> SYS: Demande de connexion (email, mot de passe)
activate SYS
SYS -> SYS: Vérification des identifiants
alt Identifiants valides
    SYS -> SA: Connexion réussie
    SA -> SYS: Demande d'accès au tableau de bord
    SYS -> SA: Affichage du tableau de bord SuperAdmin
else Identifiants invalides
    SYS -> SA: Message d'erreur (identifiants incorrects)
end
deactivate SYS
@enduml
```

## 2. Création d'un Admin par le SuperAdmin

Description en français : Le SuperAdmin, une fois connecté, accède à la fonctionnalité de création d'administrateur. Il remplit un formulaire avec les informations du nouvel administrateur et soumet. Le système valide les données. Si elles sont valides, le nouvel administrateur est créé, une notification est envoyée, et le SuperAdmin est informé du succès. Sinon, un message d'erreur est affiché.

```plantuml
@startuml
actor SuperAdmin as SA
participant Système as SYS
database BaseDeDonnées as BDD

SA -> SYS: Accède à la création d'Admin
activate SYS
SYS -> SA: Affiche le formulaire de création d'Admin
SA -> SYS: Soumet le formulaire (nom, email, mot de passe)
SYS -> SYS: Valide les données du formulaire
alt Données valides
    SYS -> BDD: Crée un nouvel enregistrement Admin
    activate BDD
    BDD --> SYS: Confirmation de création Admin
    deactivate BDD
    SYS -> SYS: Envoie une notification au nouvel Admin (optionnel)
    SYS -> SA: Message de succès (Admin créé)
else Données invalides
    SYS -> SA: Message d'erreur (données invalides)
end
deactivate SYS
@enduml
```

## 3. Inscription d'un Organisateur

Description en français : Un visiteur souhaite s'inscrire en tant qu'organisateur. Il remplit le formulaire d'inscription. Le système valide les données. Si valides, un nouveau compte organisateur est créé, un email de vérification est envoyé, et un message de succès s'affiche. Sinon, des erreurs sont montrées.

```plantuml
@startuml
actor Visiteur as V
participant Système as SYS
database BaseDeDonnées as BDD
participant ServiceEmail as SE

V -> SYS: Accède à la page d'inscription Organisateur
activate SYS
SYS -> V: Affiche le formulaire d'inscription
V -> SYS: Soumet le formulaire (nom, email, mot de passe, etc.)
SYS -> SYS: Valide les données du formulaire
alt Données valides
    SYS -> BDD: Crée un compte Organisateur (statut "en attente de vérification")
    activate BDD
    BDD --> SYS: Confirmation de création
    deactivate BDD
    SYS -> SE: Demande d'envoi d'email de vérification
    activate SE
    SE --> SYS: Confirmation d'envoi
    deactivate SE
    SYS -> V: Message de succès et instruction de vérifier email
else Données invalides
    SYS -> V: Message d'erreur avec détails
end
deactivate SYS
@enduml
```

## 4. Création d'une Organisation par l'Organisateur

Description en français : L'organisateur, après s'être connecté et avoir fait vérifier son compte, souhaite créer une nouvelle organisation. Il remplit un formulaire avec les détails de l'organisation. Le système valide ces informations. Si elles sont correctes, l'organisation est créée et associée à l'organisateur. Un message de succès est affiché. Sinon, des erreurs sont indiquées.

```plantuml
@startuml
actor Organisateur as ORG
participant Système as SYS
database BaseDeDonnées as BDD

ORG -> SYS: Demande de création d'organisation
activate SYS
SYS -> ORG: Affiche le formulaire de création d'organisation
ORG -> SYS: Soumet le formulaire (nom de l'organisation, détails, etc.)
SYS -> SYS: Valide les données de l'organisation
alt Données valides
    SYS -> BDD: Crée une nouvelle organisation liée à ORG
    activate BDD
    BDD --> SYS: Confirmation de création
    deactivate BDD
    SYS -> ORG: Message de succès (Organisation créée)
else Données invalides
    SYS -> ORG: Message d'erreur (données invalides)
end
deactivate SYS
@enduml
```

## 5. Création d'un Événement par l'Organisateur

Description en français : L'organisateur, connecté et ayant sélectionné une organisation, souhaite créer un nouvel événement. Il remplit un formulaire avec les détails de l'événement (nom, date, lieu, etc.). Le système valide ces informations. Si correctes, l'événement est créé et associé à l'organisation. Un message de succès est affiché. Sinon, des erreurs sont indiquées. (Note : Les noms des participants sont hypothétiques pour illustrer le flux.)

```plantuml
@startuml
actor Organisateur as UserOrg
participant "Système (Frontend)" as FE
participant "Système (Backend)" as BE
database BaseDeDonnées as DB

UserOrg -> FE: Ouvre la page de création d'événement pour "Organisation Alpha"
activate FE
FE -> BE: GET /api/organisations/OrgID123/evenements/form-data
activate BE
BE -> DB: Récupère les infos nécessaires (ex: lieux, catégories)
DB --> BE: Données pour le formulaire
BE --> FE: JSON {lieux: [...], categories: [...]}
deactivate BE
FE -> UserOrg: Affiche le formulaire de création d'événement pré-rempli si infos

UserOrg -> FE: Remplit et soumet le formulaire (nom="Festival de Musique", date="2024-07-20", lieu="Stade DeLyon")
FE -> BE: POST /api/organisations/OrgID123/evenements (payload: {nomEvent:"Festival de Musique", ...})
activate BE
BE -> BE: Valide les données de l'événement (format, champs obligatoires, droits UserOrg sur OrgID123)
alt Données valides
    BE -> DB: INSERT INTO evenements (nom, date, lieu_id, organisation_id) VALUES ("Festival de Musique", "2024-07-20", LieuID456, OrgID123)
    activate DB
    DB --> BE: Confirmation (eventID: EvtID789)
    deactivate DB
    BE --> FE: HTTP 201 Created (eventID: EvtID789, message: "Événement créé avec succès")
    FE -> UserOrg: Affiche "Événement 'Festival de Musique' créé avec succès !"
else Données invalides ou erreur de droits
    BE --> FE: HTTP 400/403 Bad Request/Forbidden (erreurs: {champDate: "Format invalide", ...})
    FE -> UserOrg: Affiche les messages d'erreur près des champs concernés
end
deactivate BE
deactivate FE
@enduml
```

## 6. Initialisation de l'accès Tenant (après paiement validé)

Description en français : Suite à la validation du paiement pour un abonnement (par exemple, par Stripe), le système doit initialiser l'accès "Tenant" pour l'organisation. Cela implique la création ou l'activation de l'organisation en tant que Tenant, la configuration de son sous-domaine, et la notification à l'organisateur.

```plantuml
@startuml
participant "Service de Paiement (ex: Stripe)" as SP
participant Système as SYS
participant Organisateur as ORG
database BaseDeDonnées as BDD
participant ServiceEmail as SE

SP -> SYS: Webhook: Paiement Réussi (payment_id, customer_id, subscription_id, org_details)
activate SYS

SYS -> BDD: Recherche Organisation basée sur org_details ou customer_id
alt Organisation trouvée et en attente d'activation Tenant
    SYS -> BDD: Met à jour statut Organisation vers "Tenant Actif"
    BDD -> SYS: Confirme mise à jour

    SYS -> SYS: Génère/Configure le sous-domaine (ex: orgname.votredomaine.com)
    note right: Logique de configuration DNS/proxy ici (peut être asynchrone)

    SYS -> SE: Demande d'envoi d'email à ORG (accès Tenant prêt, URL: orgname.votredomaine.com)
    activate SE
    SE --> SYS: Confirmation d'envoi
    deactivate SE

    SYS -> SP: HTTP 200 OK (accusé de réception du webhook)
else Organisation non trouvée ou déjà active
    SYS -> SYS: Log l'erreur/incohérence
    SYS -> SP: HTTP 200 OK (pour accuser réception, mais erreur traitée en interne)
end

deactivate SYS
@enduml
```
