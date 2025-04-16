<?php
include_once '../entity/Cabinet.php';
include_once '../modeles/DAOCabinet.php';
include_once '../utils/AuthHelper.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuration CORS renforcée
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Gérer les requêtes OPTIONS (pre-flight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Vérifier le token d'authentification (méthode améliorée)
$authHeader = getAuthorizationHeader();
if (!$authHeader) {
    http_response_code(401);
    echo json_encode([
        'error' => 'Token manquant', 
        'debug' => debugRequestHeaders(),
        'method' => $_SERVER['REQUEST_METHOD']
    ]);
    exit;
}

$token = str_replace('Bearer ', '', $authHeader);
if (!verifyToken($token)) {
    http_response_code(401);
    echo json_encode(['error' => 'Token invalide']);
    exit;
}

try {
    $method = $_SERVER['REQUEST_METHOD'];
    switch ($method) {
        case 'GET':
            if (isset($_GET['latitude']) && isset($_GET['longitude'])) {
                $cabinets = getCabinetsPlusProches($_GET['latitude'], $_GET['longitude']);
                echo json_encode($cabinets);
            } elseif (isset($_GET['id'])) {
                $cabinet = getUnCabinet($_GET['id']);
                if ($cabinet) {
                    $medecins = getMedecinsCabinet($cabinet->getIdCabinet());
                    $cabinet->addMedecins($medecins);
                }
                echo json_encode($cabinet);
            } else {
                $cabinets = getLesCabinets();
                echo json_encode($cabinets);
            }
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) {
                // Si pas de JSON, essayer les paramètres GET
                $data = [
                    'rue' => $_GET['rue'] ?? null,
                    'ville' => $_GET['ville'] ?? null,
                    'code_postal' => $_GET['code_postal'] ?? null,
                    'telephone' => $_GET['telephone'] ?? null,
                    'latitude' => $_GET['latitude'] ?? null,
                    'longitude' => $_GET['longitude'] ?? null
                ];
            }

            // Vérifier que tous les champs requis sont présents
            $required = ['rue', 'ville', 'code_postal', 'telephone', 'latitude', 'longitude'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    throw new Exception("Le champ $field est requis");
                }
            }

            $cabinet = new Cabinet();
            $cabinet->setRue($data['rue'])
                   ->setVille($data['ville'])
                   ->setCodePostal($data['code_postal'])
                   ->setTelephone($data['telephone'])
                   ->setLatitude($data['latitude'])
                   ->setLongitude($data['longitude']);

            $id = addCabinet($cabinet);
            echo json_encode(['message' => 'Cabinet créé', 'id' => $id]);
            break;

        case 'PATCH':
            parse_str(file_get_contents('php://input'), $_PATCH);
            
            if (!isset($_PATCH['id'])) {
                throw new Exception('ID du cabinet requis');
            }

            $cabinetData = [];
            $fields = ['rue', 'ville', 'code_postal', 'telephone', 'latitude', 'longitude'];
            
            foreach ($fields as $field) {
                if (isset($_PATCH[$field])) {
                    $cabinetData[$field] = $_PATCH[$field];
                }
            }

            if (empty($cabinetData)) {
                throw new Exception('Aucune donnée à mettre à jour');
            }

            updateCabinet($_PATCH['id'], $cabinetData);
            echo json_encode(['message' => 'Cabinet mis à jour']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non supportée']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Erreur serveur',
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
