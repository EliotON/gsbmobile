<?php
/**
 * Script d'aide pour la gestion des en-têtes CORS
 * À inclure au début de tous les fichiers API
 */

// Configuration des en-têtes CORS standard
function setupCORS() {
    // Autorise toutes les origines (à restreindre en production)
    header("Access-Control-Allow-Origin: *");
    
    // Type de contenu standard pour les API
    header("Content-Type: application/json; charset=UTF-8");
    
    // Méthodes HTTP autorisées
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");
    
    // Durée de mise en cache des préférences CORS (en secondes)
    header("Access-Control-Max-Age: 3600");
    
    // En-têtes autorisés dans les requêtes
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    // En-têtes exposés aux clients
    header("Access-Control-Expose-Headers: Content-Length, X-JSON");
    
    // Gérer automatiquement les requêtes OPTIONS (pre-flight)
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

// Appliquer les en-têtes CORS par défaut
setupCORS();
