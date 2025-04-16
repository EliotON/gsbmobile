<?php
/**
 * Fichier commun pour les configurations d'API
 * À inclure au début de tous les fichiers API
 */

// Activer le rapport d'erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclure la configuration CORS
require_once __DIR__ . '/../cors.php';

// Inclure l'aide à l'authentification
require_once __DIR__ . '/../utils/AuthHelper.php';

/**
 * Vérifie l'authentification pour toutes les routes qui en ont besoin
 * 
 * @param bool $skipAuth Si true, ignore la vérification d'authentification
 * @return void
 */
function checkAuthentication($skipAuth = false) {
    // Ignorer la vérification si demandé
    if ($skipAuth) {
        return;
    }
    
    // Récupérer l'en-tête d'autorisation
    $authHeader = getAuthorizationHeader();
    
    // Vérifier si l'en-tête existe
    if (!$authHeader) {
        http_response_code(401);
        echo json_encode(['error' => 'Token manquant']);
        exit;
    }
    
    // Extraire et vérifier le token
    $token = str_replace('Bearer ', '', $authHeader);
    if (!verifyToken($token)) {
        http_response_code(401);
        echo json_encode(['error' => 'Token invalide']);
        exit;
    }
}
