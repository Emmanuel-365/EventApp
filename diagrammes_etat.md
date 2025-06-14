# Diagrammes d'État des Organisations

## 1. Statut de Validation d'une Organisation

Ce diagramme illustre le processus de validation d'une organisation. Une organisation commence en état 'en attente'. Un administrateur peut ensuite l'approuver, la faisant passer à l'état 'acceptée', ou la refuser, la menant à l'état 'rejetée'.

```plantuml
@startuml
' Diagramme d'état pour la validation d'une organisation

title Statut de Validation d'une Organisation

state "en attente" as pending
state "acceptée" as accepted
state "rejetée" as rejected

[*] --> pending : Nouvelle organisation
pending --> accepted : Par Admin / Acceptation
pending --> rejected : Par Admin / Rejet

accepted --> [*] : Validée
rejected --> [*] : Rejetée
@enduml
```

## 2. Statut d'Activation d'une Organisation

Ce diagramme représente l'état d'activation d'une organisation qui a déjà été validée (acceptée). Une organisation est initialement 'activée'. Elle peut être 'désactivée' par un administrateur ou par l'organisateur responsable. Sa réactivation dépend de qui a effectué la désactivation : si c'est un administrateur, seul un administrateur peut la réactiver ; si c'est l'organisateur, l'organisateur lui-même ou un administrateur peut la réactiver.

```plantuml
@startuml
' Diagramme d'état pour l'activation d'une organisation
' Important: Ce diagramme suppose que l'organisation est déjà dans l'état de validation "acceptée".

title Statut d'Activation d'une Organisation (après validation)

state "activée" as enabled
state "désactivée" as disabled

[*] --> enabled : Initialement après acceptation
enabled --> disabled : Par Admin [Désactivation par Admin]
enabled --> disabled : Par Organisateur [Désactivation par Organisateur]

disabled --> enabled : Par Admin [Réactivation]
disabled --> enabled : Par Organisateur \n(si désactivée par l'Organisateur)

note right of disabled
  Si désactivée par un Admin,
  seul un Admin peut réactiver.
end note
@enduml
```
