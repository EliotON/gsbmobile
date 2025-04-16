<?php
include_once '../utils/AuthHelper.php';

// Configuration
$base_url = "http://localhost/gsbvttMobile/API/";
$token = null;

// Debug mode
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Authentification
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

// Test 1: GET - Récupérer tous les cabinets
echo "Test 1: GET tous les cabinets\n";
$ch = curl_init($base_url . "ApiCabinet.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
$response = curl_exec($ch);
echo $response . "\n\n";

// Test 2: POST - Créer un nouveau cabinet
echo "Test 2: POST nouveau cabinet\n";
$cabinet_data = [
    'rue' => '10 rue Test',
    'ville' => 'VilleTest',
    'code_postal' => '75000',
    'telephone' => '0123456789',
    'latitude' => '48.8566',
    'longitude' => '2.3522'
];

$ch = curl_init($base_url . "ApiCabinet.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($cabinet_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json'
]);
$response = curl_exec($ch);
echo $response . "\n\n";
$created_id = json_decode($response, true)['id'] ?? null;

// Test 3: GET - Récupérer le cabinet créé
if ($created_id) {
    echo "Test 3: GET cabinet spécifique (ID: $created_id)\n";
    $ch = curl_init($base_url . "ApiCabinet.php?id=" . $created_id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
    $response = curl_exec($ch);
    echo $response . "\n\n";
}

// Test 4: PATCH - Modifier le cabinet
echo "Test 4: PATCH cabinet\n";
$patch_data = [
    'id' => $created_id,
    'rue' => '12 rue Modifiée',
    'ville' => 'VilleModifiée'
];

$ch = curl_init($base_url . "ApiCabinet.php");
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
echo "Test 5: GET cabinet modifié\n";
$ch = curl_init($base_url . "ApiCabinet.php?id=" . $created_id);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
$response = curl_exec($ch);
echo $response . "\n\n";

// Test 6: GET - Récupérer les cabinets les plus proches
echo "Test 6: GET cabinets les plus proches\n";
$latitude = '48.8566';
$longitude = '2.3522';
$ch = curl_init($base_url . "ApiCabinet.php?latitude=$latitude&longitude=$longitude");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
$response = curl_exec($ch);
echo $response . "\n\n";

curl_close($ch);
