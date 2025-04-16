<?php
include_once '../utils/AuthHelper.php';

// Configuration
$base_url = "http://localhost/gsbvttMobile/API/";
$token = null;

// Debug mode
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Test 0: Authentification
echo "Test 0: Authentification\n";
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
$auth_result = json_decode($response, true);
if (!$auth_result || !isset($auth_result['token'])) {
    die('Authentication failed: ' . $response);
}
$token = $auth_result['token'];
echo "Token reçu: " . $token . "\n\n";

// Test 1: GET - Récupérer tous les médecins
echo "Test 1: GET tous les médecins\n";
$ch = curl_init($base_url . "ApiMedecin.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
$response = curl_exec($ch);
echo $response . "\n\n";

// Test 2: POST - Créer un nouveau médecin
echo "Test 2: POST nouveau médecin\n";
$url = $base_url . "ApiMedecin.php?" . http_build_query([
    'nom' => 'Dupont',
    'prenom' => 'Jean',
    'rue' => '15 rue du Test',
    'ville' => 'TestVille',
    'code_postal' => '75000',
    'telephone' => '0123456789'
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
$response = curl_exec($ch);
echo $response . "\n\n";

// Test 3: GET - Récupérer un médecin spécifique
echo "Test 3: GET médecin spécifique\n";
$ch = curl_init($base_url . "ApiMedecin.php?id=1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
$response = curl_exec($ch);
echo $response . "\n\n";

// Test 4: PATCH - Modifier un médecin
echo "Test 4: PATCH médecin\n";
$patch_data = [
    'id' => '1',
    'nom' => 'DupontModifié',
    'prenom' => 'JeanModifié',
    'telephone' => '0987654321'
];

$ch = curl_init($base_url . "ApiMedecin.php");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($patch_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/x-www-form-urlencoded'
]);
$response = curl_exec($ch);
echo $response . "\n\n";

// Test 5: GET - Vérifier les modifications
echo "Test 5: GET médecin modifié\n";
$ch = curl_init($base_url . "ApiMedecin.php?id=1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
$response = curl_exec($ch);
echo $response . "\n\n";

// Test 6: DELETE - Supprimer un médecin
echo "Test 6: DELETE médecin\n";
$ch = curl_init($base_url . "ApiMedecin.php?id=1");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
$response = curl_exec($ch);
echo $response . "\n\n";

// Test 7: GET - Vérifier la suppression
echo "Test 7: GET médecin supprimé\n";
$ch = curl_init($base_url . "ApiMedecin.php?id=1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
$response = curl_exec($ch);
echo $response . "\n\n";

curl_close($ch);
