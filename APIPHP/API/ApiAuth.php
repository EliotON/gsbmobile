<?php
require_once __DIR__ . '/../utils/AuthHelper.php';
require_once __DIR__ . '/../modeles/DAOVisiteur.php';
require_once __DIR__ . '/../pdo.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuration CORS renforcée
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Gérer les requêtes OPTIONS (pre-flight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    try {
        // Récupérer les données d'authentification
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Vérifier si email et password sont fournis
        if (!isset($data['email']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Email et mot de passe requis']);
            exit;
        }
        
        $email = $data['email'];
        $password = $data['password'];
        
        // Obtenir une instance de PDO
        $pdo = PDO2::getInstance();
        
        // Rechercher l'utilisateur par email
        $stmt = $pdo->prepare("SELECT id_visiteur, nom, prenom, email, password FROM Visiteur WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Vérifier si l'utilisateur existe
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Email ou mot de passe incorrect']);
            exit;
        }
        
        // Pour le développement, accepter le mot de passe 'password'
        // En production, vous devriez vérifier le hash
        if ($password === 'password' || password_verify($password, $user['password'])) {
            // Générer un token JWT
            $token = generateToken($user['id_visiteur'], [ // Ensure id_visiteur is included
                'id_visiteur' => $user['id_visiteur'], // Add id_visiteur to the payload
                'email' => $user['email'],
                'nom' => $user['nom'],
                'prenom' => $user['prenom']
            ]);
            
            // Stocker le token dans la base de données
            $tokenHash = hash('sha256', $token);
            $expiration = date('Y-m-d H:i:s', time() + 86400); // 24 heures
            
            $stmt = $pdo->prepare("UPDATE Visiteur SET auth_token = :token, token_expiration = :expiration WHERE id_visiteur = :id");
            $stmt->execute([
                'token' => $tokenHash,
                'expiration' => $expiration,
                'id' => $user['id_visiteur']
            ]);
            
            // Retourner le token au client
            echo json_encode([
                'status' => 'success',
                'message' => 'Authentification réussie',
                'user' => [
                    'id' => $user['id_visiteur'],
                    'nom' => $user['nom'],
                    'prenom' => $user['prenom'],
                    'email' => $user['email']
                ],
                'token' => $token
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Email ou mot de passe incorrect']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'error' => 'Erreur serveur',
            'message' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
}
