<?php
include_once '../utils/AuthHelper.php';

// Configuration
$base_url = "http://localhost/gsbvttMobile/API/";
$token = null;

// Test 1: Authentification
echo "Test 1: Authentification\n";
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
    die('Curl error: ' . curl_error($ch));
}

$auth_result = json_decode($response, true);
if (!$auth_result || !isset($auth_result['token'])) {
    die('Authentication failed: ' . $response);
}

$token = $auth_result['token'];
echo "Token reçu: " . $token . "\n\n";

// Test 2: Récupérer tous les visiteurs
echo "Test 2: GET tous les visiteurs\n";
$ch = curl_init($base_url . "ApiVisiteur.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
$response = curl_exec($ch);
echo $response . "\n\n";

// Test 3: Créer un nouveau visiteur
echo "Test 3: POST nouveau visiteur\n";
$ch = curl_init($base_url . "ApiVisiteur.php?nom=Test&prenom=User&email=test@test.com&telephone=0123456789");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
$response = curl_exec($ch);
echo $response . "\n\n";

// Test 4: Modifier un visiteur
echo "Test 4: PATCH visiteur\n";
$patch_data = http_build_query([
    'id' => '1',
    'nom' => 'Updated',
    'prenom' => 'Name'
]);
$ch = curl_init($base_url . "ApiVisiteur.php");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
curl_setopt($ch, CURLOPT_POSTFIELDS, $patch_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
$response = curl_exec($ch);
echo $response . "\n\n";

curl_close($ch);
