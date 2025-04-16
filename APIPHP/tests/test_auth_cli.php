<?php
/**
 * Script de test pour l'authentification en ligne de commande 
 * Usage: php test_auth_cli.php [token]
 */
require_once __DIR__ . '/../utils/AuthHelper.php';

// Définir un token manuellement pour tester
if ($argc > 1) {
    $_ENV['AUTH_TOKEN'] = $argv[1];
    echo "Token défini: " . $argv[1] . "\n";
}

// Générer un token de test si aucun n'est fourni
if (!isset($_ENV['AUTH_TOKEN'])) {
    $token = generateToken(1, ['test' => true]);
    $_ENV['AUTH_TOKEN'] = $token;
    echo "Token de test généré: $token\n";
}

// Simuler l'en-tête d'autorisation en mode CLI
if (isCliMode() && isset($_ENV['AUTH_TOKEN'])) {
    $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $_ENV['AUTH_TOKEN'];
}

// Vérifier si nous sommes en mode CLI
echo "Environnement: " . (isCliMode() ? "CLI" : "Web") . "\n";

// Tester la vérification du token
$authHeader = getAuthorizationHeader();
echo "En-tête d'autorisation: " . ($authHeader ? $authHeader : "Non trouvé") . "\n";

if ($authHeader && strpos($authHeader, 'Bearer ') === 0) {
    $token = substr($authHeader, 7);
    $decoded = AuthHelper::verifyToken($token);
    echo "Décodage du token: " . ($decoded ? "Réussi" : "Échoué") . "\n";
    if ($decoded) {
        echo "Contenu du token:\n";
        print_r($decoded);
    }
} else {
    echo "Format de token incorrect ou token manquant\n";
}

echo "\nÉtat de isValidToken(): " . (isValidToken() ? "Valide" : "Invalide") . "\n";

// Tester AuthHelper::requireValidToken() directement
echo "Test de AuthHelper::requireValidToken(): ";
try {
    AuthHelper::requireValidToken();
    echo "Autorisé\n";
} catch (Exception $e) {
    echo "Non autorisé: " . $e->getMessage() . "\n";
}

echo "\nTest terminé\n";
