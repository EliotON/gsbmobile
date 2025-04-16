<?php
include_once '../utils/AuthHelper.php';

// Configuration
$base_url = "http://localhost/gsbvttMobile/API/";
$token = null;

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Authentication
echo "Starting authentication...\n";
$auth_data = [
    'email' => 'pierre.dupont@example.com',
    'password' => 'password'
];

$ch = curl_init($base_url . "ApiAuth.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($auth_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
if ($response === false) {
    die('Authentication Curl error: ' . curl_error($ch) . "\n");
}
$auth_result = json_decode($response, true);
if (!$auth_result || !isset($auth_result['token'])) {
    die('Authentication failed: ' . $response . "\n");
}
$token = $auth_result['token'];
echo "Authentication successful. Token: " . $token . "\n\n";

// Test 1: GET toutes les visites
echo "Test 1: GET toutes les visites\n";
$ch = curl_init($base_url . "ApiVisite.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
$response = curl_exec($ch);
if ($response === false) {
    echo 'GET Curl error: ' . curl_error($ch) . "\n";
} else {
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo "HTTP Response code: " . $http_code . "\n";
    echo "Response: " . $response . "\n\n";
}

// Récupérer les médecins existants pour obtenir un ID valide
echo "Récupération des médecins existants...\n";
$ch = curl_init($base_url . "ApiMedecin.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
$response = curl_exec($ch);
$medecins = json_decode($response, true);

if (empty($medecins)) {
    echo "Aucun médecin trouvé, création d'un médecin...\n";
    // Créer un médecin si aucun n'existe
    $medecin_url = $base_url . "ApiMedecin.php?" . http_build_query([
        'nom' => 'Test',
        'prenom' => 'Medecin',
        'rue' => '1 rue Test',
        'ville' => 'TestVille',
        'code_postal' => '75000',
        'telephone' => '0123456789'
    ]);
    
    $ch = curl_init($medecin_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
    curl_exec($ch);
    
    // Récupérer à nouveau la liste
    $ch = curl_init($base_url . "ApiMedecin.php");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
    $response = curl_exec($ch);
    $medecins = json_decode($response, true);
}

// Récupérer les visiteurs existants pour obtenir un ID valide
echo "Récupération des visiteurs existants...\n";
$ch = curl_init($base_url . "ApiVisiteur.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
$response = curl_exec($ch);
$visiteurs = json_decode($response, true);

if (empty($visiteurs)) {
    echo "Aucun visiteur trouvé, création d'un visiteur...\n";
    // Créer un visiteur si aucun n'existe
    $visiteur_url = $base_url . "ApiVisiteur.php?" . http_build_query([
        'nom' => 'Test',
        'prenom' => 'Visiteur',
        'email' => 'test@example.com',
        'telephone' => '0123456789'
    ]);
    
    $ch = curl_init($visiteur_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
    curl_exec($ch);
    
    // Récupérer à nouveau la liste
    $ch = curl_init($base_url . "ApiVisiteur.php");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
    $response = curl_exec($ch);
    $visiteurs = json_decode($response, true);
}

// Obtenir les IDs valides
$medecin_id = isset($medecins[0]['id_medecin']) ? $medecins[0]['id_medecin'] : (is_array($medecins) ? reset($medecins)['id_medecin'] : 1);
$visiteur_id = isset($visiteurs[0]['id_visiteur']) ? $visiteurs[0]['id_visiteur'] : (is_array($visiteurs) ? reset($visiteurs)['id_visiteur'] : 1);

echo "Utilisation du médecin ID: $medecin_id et visiteur ID: $visiteur_id\n";

// Test 2: POST nouvelle visite avec des IDs valides
echo "Test 2: POST nouvelle visite\n";
$visite_data = [
    'id_visiteur' => $visiteur_id,
    'id_medecin' => $medecin_id,
    'date_visite' => date('Y-m-d'),
    'heure_arrivee' => date('Y-m-d H:i:s'),
    'heure_debut_entretien' => date('Y-m-d H:i:s', strtotime('+15 minutes')),
    'temps_attente' => '00:15:00',
    'heure_depart' => date('Y-m-d H:i:s', strtotime('+45 minutes')),
    'temps_visite' => '00:30:00',
    'rendez_vous' => true
];

$ch = curl_init($base_url . "ApiVisite.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($visite_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json'
]);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "HTTP Response code: " . $http_code . "\n";
echo "Response: " . $response . "\n\n";

// Récupérer l'ID de la visite créée
$created_result = json_decode($response, true);
$visite_id = isset($created_result['id']) ? $created_result['id'] : 1;

// Test 3: PUT visite
echo "Test 3: PUT visite\n";
$visite_data['id_visite'] = $visite_id;
$visite_data['temps_attente'] = '00:20:00';

$ch = curl_init($base_url . "ApiVisite.php");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($visite_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json'
]);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "HTTP Response code: " . $http_code . "\n";
echo "Response: " . $response . "\n\n";

curl_close($ch);
