<?php
require_once __DIR__ . '/../utils/AuthHelper.php';
require_once '../modeles/DAOVisite.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuration CORS complète
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Expose-Headers: Content-Length, X-JSON");

// Gérer les requêtes OPTIONS (pre-flight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Vérifier le token d'authentification
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
    $pdo = PDO2::getInstance();
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            // Récupérer les visites filtrées par id_visiteur si le paramètre est fourni
            if (isset($_GET['id'])) {
                $stmt = $pdo->prepare("SELECT v.*, 
                                              m.nom AS nom_medecin, m.prenom AS prenom_medecin, 
                                              vis.nom AS nom_visiteur, vis.prenom AS prenom_visiteur,
                                              c.rue AS rue_cabinet, c.ville AS ville_cabinet, c.code_postal AS code_postal_cabinet,
                                              v.heure_debut_entretien AS heure_rdv
                                       FROM Visite v
                                       JOIN Medecin m ON v.id_medecin = m.id_medecin
                                       JOIN Visiteur vis ON v.id_visiteur = vis.id_visiteur
                                       JOIN Cabinet c ON m.id_cabinet = c.id_cabinet
                                       WHERE v.id_visiteur = :id");
                $stmt->execute(['id' => $_GET['id']]);
            } else {
                $stmt = $pdo->prepare("SELECT v.*, 
                                              m.nom AS nom_medecin, m.prenom AS prenom_medecin, 
                                              vis.nom AS nom_visiteur, vis.prenom AS prenom_visiteur,
                                              c.rue AS rue_cabinet, c.ville AS ville_cabinet, c.code_postal AS code_postal_cabinet,
                                              v.heure_debut_entretien AS heure_rdv
                                       FROM Visite v
                                       JOIN Medecin m ON v.id_medecin = m.id_medecin
                                       JOIN Visiteur vis ON v.id_visiteur = vis.id_visiteur
                                       JOIN Cabinet c ON m.id_cabinet = c.id_cabinet");
                $stmt->execute();
            }
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        case 'POST':
            // Créer une nouvelle visite
            $data = json_decode(file_get_contents('php://input'), true);
            // Decode the JWT to set the correct id_visiteur in the body
            require_once __DIR__ . '/../vendor/autoload.php'; // Assure-toi que firebase/php-jwt est installé
            use \Firebase\JWT\JWT;
            $secretKey = 'your_secret_key'; // Remplacer par la clé secrète utilisée pour générer le token
            $decoded = JWT::decode($token, $secretKey, array('HS256'));
            $data['id_visiteur'] = $decoded->id_visiteur;
            
            $sql = "INSERT INTO Visite (id_visiteur, id_medecin, date_visite, heure_arrivee, 
                    heure_debut_entretien, temps_attente, heure_depart, temps_visite, rendez_vous) 
                    VALUES (:id_visiteur, :id_medecin, :date_visite, :heure_arrivee, 
                    :heure_debut_entretien, :temps_attente, :heure_depart, :temps_visite, :rendez_vous)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            echo json_encode(['message' => 'Visite créée', 'id' => $pdo->lastInsertId()]);
            break;

        case 'PUT':
            // Modifier une visite existante
            $data = json_decode(file_get_contents('php://input'), true);
            if (!isset($data['id_visite'])) {
                throw new Exception('ID de visite requis');
            }

            $sql = "UPDATE Visite SET 
                    id_visiteur = :id_visiteur,
                    id_medecin = :id_medecin,
                    date_visite = :date_visite,
                    heure_arrivee = :heure_arrivee,
                    heure_debut_entretien = :heure_debut_entretien,
                    temps_attente = :temps_attente,
                    heure_depart = :heure_depart,
                    temps_visite = :temps_visite,
                    rendez_vous = :rendez_vous
                    WHERE id_visite = :id_visite";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            echo json_encode(['message' => 'Visite mise à jour']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Erreur serveur',
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>
