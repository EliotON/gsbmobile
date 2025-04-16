<?php

include '../entity/Medecin.php';
include '../modeles/DAOMedecin.php';
include_once '../utils/AuthHelper.php';

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
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $medecin = getUnMedecin($id);
                echo(json_encode($medecin));
            } else {
                echo(json_encode(getLesMedecins()));
            }
            break;

        case 'POST':
            if (isset($_GET['nom']) && isset($_GET['prenom']) && isset($_GET['rue']) && isset($_GET['ville']) && isset($_GET['code_postal']) && isset($_GET['telephone'])) {
                $nom = $_GET['nom'];
                $prenom = $_GET['prenom'];
                $rue = $_GET['rue'];
                $ville = $_GET['ville'];
                $code_postal = $_GET['code_postal'];
                $telephone = $_GET['telephone'];
                
                $medecin = new Medecin(0, $nom, $prenom, $rue, $ville, $code_postal, $telephone);
                addMedecin($medecin);
                echo(json_encode(['message' => 'Médecin ajouté !']));
            } else {
                echo(json_encode(['error' => 'Champs manquants']));
            }
            break;
        
        case 'PATCH': // /!\ATTENTION METTRE EN X-WWW-FORM-URLENCODED SUR POSTMAN/!\
            parse_str(file_get_contents('php://input'), $_PATCH);
        
            // Vérifiez si l'ID est présent
            if (isset($_PATCH['id'])) {
                $id = $_PATCH['id'];
        
                // Préparez un tableau pour stocker les données à mettre à jour
                $medecinData = [];
                
                if (isset($_PATCH['nom'])) {
                    $medecinData['nom'] = $_PATCH['nom'];
                }
                if (isset($_PATCH['prenom'])) {
                    $medecinData['prenom'] = $_PATCH['prenom'];
                }
                if (isset($_PATCH['rue'])) {
                    $medecinData['rue'] = $_PATCH['rue'];
                }
                if (isset($_PATCH['ville'])) {
                    $medecinData['ville'] = $_PATCH['ville'];
                }
                if (isset($_PATCH['code_postal'])) {
                    $medecinData['code_postal'] = $_PATCH['code_postal'];
                }
                if (isset($_PATCH['telephone'])) {
                    $medecinData['telephone'] = $_PATCH['telephone'];
                }
        
                // Mettez à jour uniquement les champs qui ne sont pas vides
                if (!empty($medecinData)) {
                    updateMedecin($id, $medecinData);
                    echo(json_encode(['message' => 'Médecin modifié !']));
                } else {
                    echo(json_encode(['error' => 'Aucune donnée à mettre à jour']));
                }
            } else {
                echo(json_encode(['error' => 'ID manquant dans la requête PATCH']));
            }
            break;
        
        case 'DELETE':
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                deleteMedecin($id);
                echo(json_encode(['message' => 'Médecin supprimé !']));
            } else {
                echo(json_encode(['error' => 'ID manquant']));
            }
            break;

        default:
            echo(json_encode(['error' => 'Mauvaise méthode (GET, POST, PATCH, DELETE)']));
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
