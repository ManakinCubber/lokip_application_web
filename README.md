# README
## Présentation du Projet
Ce projet est une application web développée avec Symfony, un framework PHP. Elle est conçue pour gérer les alertes et les adresses IP associées à différents ordinateurs. L'application propose des fonctionnalités telles que la suppression d'alertes, l'autorisation de pays, l'arrêt des alertes, et bien plus encore. Elle s'intègre également avec des services externes comme l'API Hector et l'API IP2Location pour récupérer des données supplémentaires.

## Fonctionnalités de l'Application
### Gestion des Alertes
L'application permet aux utilisateurs de gérer les alertes associées à différents ordinateurs. Les utilisateurs peuvent supprimer des alertes individuelles ou toutes les alertes associées à un ordinateur.

### Autorisation de Pays
Les utilisateurs peuvent autoriser des pays pour un ordinateur spécifique. Cette fonctionnalité est utile pour mettre sur liste blanche des pays pour un ordinateur et arrêter de recevoir des alertes inutiles.

### Arrêt des Alertes
Les utilisateurs peuvent arrêter toutes les alertes pour un ordinateur spécifique.

### Gestion des Adresses IP
L'application gère les adresses IP associées à différents ordinateurs. Elle récupère les données de pays pour les adresses IP en utilisant l'API IP2Location.

### Authentification Utilisateur
L'application comprend des fonctionnalités d'authentification utilisateur. Les utilisateurs peuvent se connecter à l'application en utilisant leurs identifiants google.

## Configuration
Pour mettre en marche l'application, vous devez configurer quelques variables d'environnement dans le fichier .env. Voici les variables requises :

- HECTOR_PASSWORD : Le mot de passe pour l'API Hector. Cela est utilisé pour s'authentifier avec l'API Hector et récupérer des données.
- IP2LOCATION_API_KEY : La clé API pour l'API IP2Location. Cela est utilisé pour récupérer des données de pays pour les adresses IP.
- HECTOR_API_URL : L'URL pour l'API Hector.
- HECTOR_INSTANCE : L'ID d'instance pour l'API Hector.
- HECTOR_EMAIL : L'email pour l'API Hector.
- SSL_CERT_PATH : Le chemin vers le certificat SSL pour des connexions sécurisées.

Veuillez remplacer les espaces réservés dans le fichier .env avec vos valeurs réelles.

## Informations Additionnelles
L'application utilise une base de données PostgreSQL. Assurez-vous de configurer les détails de connexion à votre base de données dans le fichier .env. L'application utilise également Doctrine ORM pour les opérations de base de données. Vous pouvez trouver les classes d'entité dans le répertoire src/Entity et les classes de repository dans le répertoire src/Repository.

