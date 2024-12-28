# Coffreo : Recruitment Challenge

## Installation



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
