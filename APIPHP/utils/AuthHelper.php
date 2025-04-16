<?php

// Vérifier si l'autoloader existe
$autoloadFile = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoloadFile)) {
    die("Veuillez exécuter 'composer install' dans le répertoire du projet");
}

require_once $autoloadFile;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class AuthHelper {
    private static $secretKey = 'votre_clé_secrète_très_longue_et_complexe';
    private static $algorithm = 'HS256';

    /**
     * Génère un token JWT pour un utilisateur
     */
    public static function generateToken($userId, $additionalData = []) {
        $issuedAt = time();
        $expirationTime = $issuedAt + 86400; // 24 heures de validité

        $payload = array(
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'userId' => $userId,
            'data' => $additionalData
        );

        return JWT::encode($payload, self::$secretKey, self::$algorithm);
    }

    /**
     * Vérifie si un token JWT est valide
     */
    public static function verifyToken($token) {
        try {
            $decoded = JWT::decode($token, new Key(self::$secretKey, self::$algorithm));
            return !empty($decoded->userId);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Récupère l'en-tête d'autorisation
     */
    public static function getAuthorizationHeader() {
        $headers = null;
        
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(
                array_map('ucwords', array_keys($requestHeaders)),
                array_values($requestHeaders)
            );
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        
        return $headers;
    }

    /**
     * Debug: affiche les en-têtes de la requête
     */
    public static function debugRequestHeaders() {
        return [
            'SERVER' => $_SERVER,
            'apache_request_headers' => function_exists('apache_request_headers') ? apache_request_headers() : 'Function not available'
        ];
    }

    /**
     * Vérifie si un token est valide et lève une exception si ce n'est pas le cas
     * @throws Exception si le token est invalide ou manquant
     */
    public static function requireValidToken() {
        $authHeader = self::getAuthorizationHeader();
        if (!$authHeader) {
            throw new Exception("Token d'authentification manquant");
        }
        
        if (strpos($authHeader, 'Bearer ') !== 0) {
            throw new Exception("Format de token invalide");
        }
        
        $token = substr($authHeader, 7);
        if (!self::verifyToken($token)) {
            throw new Exception("Token invalide ou expiré");
        }
        
        return true;
    }
}

// Fonctions globales d'aide à l'authentification
function isCliMode() {
    return (php_sapi_name() === 'cli');
}

function getAuthorizationHeader() {
    return AuthHelper::getAuthorizationHeader();
}

function verifyToken($token) {
    return AuthHelper::verifyToken($token);
}

function generateToken($userId, $additionalData = []) {
    return AuthHelper::generateToken($userId, $additionalData);
}

function debugRequestHeaders() {
    return AuthHelper::debugRequestHeaders();
}

// Add backwards compatibility for isValidToken()
function isValidToken() {
    $authHeader = getAuthorizationHeader();
    if (!$authHeader) {
        return false;
    }
    
    $token = str_replace('Bearer ', '', $authHeader);
    return verifyToken($token);
}

function requireValidToken() {
    return AuthHelper::requireValidToken();
}
