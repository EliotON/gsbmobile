# Documentation API GSB VTT Mobile

## 🌐 Base URL
https://s5-4242.nuage-peda.fr/gsbvttMobile/API

## 🔑 Authentification

### Login
- **Endpoint**: POST /ApiAuth.php
- **Content-Type**: application/json
- **Body**:
{http://localhost:19006/
    "email": "pierre.dupont@example.com",
    "password": "password"
}

- **Réponse Success**:
{
    "status": "success",
    "message": "Authentification réussie",
    "user": {
        "id": 1,
        "nom": "Dupont",
        "prenom": "Jean",
        "email": "user@example.com"
    },
    "token": "eyJ0eXAiOi..."
}

### Utilisation du Token
Pour toutes les autres requêtes API, inclure le token dans le header :
Authorization: Bearer eyJ0eXAiOi...

## 📱 Endpoints API

### 1. Visiteurs (ApiVisiteur.php)

#### Lister tous les visiteurs
- **GET** /ApiVisiteur.php
- **Auth**: Required
- **Response**: Array of visitors

#### Obtenir un visiteur
- **GET** /ApiVisiteur.php?id=1
- **Auth**: Required

#### Créer un visiteur
- **POST** /ApiVisiteur.php
- **Auth**: Required
- **Params**:
  - nom
  - prenom
  - email
  - telephone

#### Modifier un visiteur
- **PATCH** /ApiVisiteur.php
- **Auth**: Required
- **Content-Type**: application/x-www-form-urlencoded
- **Body**:
  - id (required)
  - nom (optional)
  - prenom (optional)
  - email (optional)
  - telephone (optional)

#### Supprimer un visiteur
- **DELETE** /ApiVisiteur.php?id=1
- **Auth**: Required

### 2. Médecins (ApiMedecin.php)

#### Lister tous les médecins
- **GET** /ApiMedecin.php
- **Auth**: Required

#### Obtenir un médecin
- **GET** /ApiMedecin.php?id=1
- **Auth**: Required

#### Créer un médecin
- **POST** /ApiMedecin.php
- **Auth**: Required
- **Params**:
  - nom
  - prenom
  - rue
  - ville
  - code_postal
  - telephone

#### Modifier un médecin
- **PATCH** /ApiMedecin.php
- **Auth**: Required
- **Content-Type**: application/x-www-form-urlencoded
- **Body**:
  - id (required)
  - nom (optional)
  - prenom (optional)
  - rue (optional)
  - ville (optional)
  - code_postal (optional)
  - telephone (optional)

#### Supprimer un médecin
- **DELETE** /ApiMedecin.php?id=1
- **Auth**: Required

### 3. Cabinets (ApiCabinet.php)

#### Lister tous les cabinets
- **GET** /ApiCabinet.php
- **Auth**: Required

#### Obtenir un cabinet
- **GET** /ApiCabinet.php?id=1
- **Auth**: Required

#### Trouver les cabinets proches
- **GET** /ApiCabinet.php?latitude=48.8566&longitude=2.3522
- **Auth**: Required

#### Créer un cabinet
- **POST** /ApiCabinet.php
- **Auth**: Required
- **Content-Type**: application/json
- **Body**:
{
    "rue": "15 rue Example",
    "ville": "Paris",
    "code_postal": "75001",
    "telephone": "0123456789",
    "latitude": 48.8566,
    "longitude": 2.3522
}

#### Modifier un cabinet
- **PATCH** /ApiCabinet.php
- **Auth**: Required
- **Content-Type**: application/x-www-form-urlencoded
- **Body**:
  - id (required)
  - rue (optional)
  - ville (optional)
  - code_postal (optional)
  - telephone (optional)
  - latitude (optional)
  - longitude (optional)

### 4. Visites (ApiVisite.php)

#### Lister toutes les visites
- **GET** /ApiVisite.php
- **Auth**: Required

#### Obtenir une visite
- **GET** /ApiVisite.php?id=1
- **Auth**: Required

#### Créer une visite
- **POST** /ApiVisite.php
- **Auth**: Required
- **Content-Type**: application/json
- **Body**:
{
    "id_visiteur": 1,
    "id_medecin": 1,
    "date_visite": "2024-03-15",
    "heure_arrivee": "10:00:00",
    "heure_debut_entretien": "10:15:00",
    "temps_attente": 15,
    "heure_depart": "11:00:00",
    "temps_visite": 45,
    "rendez_vous": true
}

#### Modifier une visite
- **PUT** /ApiVisite.php
- **Auth**: Required
- **Content-Type**: application/json
- **Body**: (même structure que POST avec id_visite)

## 📊 Codes de Réponse

- **200**: Succès
- **400**: Requête invalide
- **401**: Non authentifié
- **403**: Non autorisé
- **404**: Ressource non trouvée
- **405**: Méthode non autorisée
- **500**: Erreur serveur

## 🔍 Format des Réponses

### Succès
{
    "message": "Description du succès",
    "data": {} // Données optionnelles
}

### Erreur
{
    "error": "Description de l'erreur",
    "message": "Details supplémentaires (optionnel)"
}