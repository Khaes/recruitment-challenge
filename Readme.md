# Coffreo : Recruitment Challenge

## NOTE 
La DSN de rabbitmq est volontairement en clair pour vous evitez de recréer le fichier

Je n'ai pas fait de gitflow étant seul
## Installation

Commande à executer :
- `docker-build`
- `compose-build`
- `compose-run`
- `compose-logs`

sur un autre shell : 
`send-test` ou `send-test-big`

## Plan d'action

- Recherche de bibliothèque
- Refactoring
- Test unitaires
- CI/CD : Ajouter un pipeline CI/CD pour automatiser les tests et le déploiement de l’application.
- Gestion des erreurs
- Docker : Modifier le dockerfile ou le docker-compose pour faciliter le déploiement de l’application.
- Bonus : scalable / extensible / perfomante / Securité
- Maxi bonus: autres worker

## Bibliothèque

- Symfony : pour l'injection de dépendance, les fichiers de config et de pas avoir à recreer Kernel / command.
- Messenger + ext-amqp : Le choix entre amqp-lib et ext-amqp a été longuement refléchi.
la finesse de la lib (amqp-lib) ne justifiait pas pour le moment de la préférer à messenger et par soucis de temps et de facilité pour le testing.
Je pense que sur un gros projet la lib va offrir de meilleurs options et serait un meilleur choix.
Niveau performance il semble que ext-amqp soit meilleur.
- MAJ de rabbit vers la 4, offre de base un retry à 20 et non plus illimité, bien que messenger le limite deja à 3 de base.

## Futur

L'utilisation de Kubernetes sur une infra type cloud pour la scalabilité, qui instancie plus de worker au besoin (CPU / RAM > 80%)
Optimiser rabbitmq via la config et properties et rajouter une dead letter queue
la sécurité : de ne plus être en guest/guest ou admin/admin pour les credentials rabbitmq, une infra privée derrière un Reverse Proxy + Bastion
Un parser custom pour messenger, si il est conservé au projet
Une lib commune aux workers
Un repo par Worker / CICD