# Coffreo : Recruitment Challenge

## Installation

Le but de cet exercice est de reprendre le code source de l'application fournie et de l'améliorer en utilisant les bonnes pratiques de développement.
Le projet est composé de plusieurs worker PHP utilisant RabbitMQ pour la communication asynchrone.

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

- Symfony : pour l'injection de dépendance et de pas avoir à recreer Kernel / command.

