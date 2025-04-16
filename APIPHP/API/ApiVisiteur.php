<?php

include_once '../entity/Visiteur.php';
include_once '../modeles/DAOVisiteur.php';
include_once '../utils/AuthHelper.php';
echo(json_encode(getLesVisiteurs()));

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

// Ne pas vérifier le token pour l'authentification
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if (!isValidToken()) {
        http_response_code(401);
        echo json_encode(['error' => 'Non autorisé']);
        exit();
    }
}

$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $visiteur = getUnVisiteur($id);
            echo(json_encode($visiteur));
        } else {
            echo(json_encode(getLesVisiteurs()));
        }
        break;

    case 'POST':
        if (isset($_GET['nom']) && isset($_GET['prenom']) && isset($_GET['email']) && isset($_GET['telephone'])) {
            $nom = $_GET['nom'];
            $prenom = $_GET['prenom'];
            $email = $_GET['email'];
            $telephone = $_GET['telephone'];

            $visiteur = new Visiteur(0, $nom, $prenom, $email, $telephone);
            addVisiteur($visiteur);
            echo(json_encode(['message' => 'Visiteur ajouté !']));
        } else {
            echo(json_encode(['error' => 'Champs manquants']));
        }
        break;

    case 'PATCH': // /!\ ATTENTION : METTRE EN X-WWW-FORM-URLENCODED SUR POSTMAN /!\
        parse_str(file_get_contents('php://input'), $_PATCH);

        if (isset($_PATCH['id'])) {
            $id = $_PATCH['id'];

            // Préparez un tableau pour stocker les données à mettre à jour
            $visiteurData = [];

            if (isset($_PATCH['nom'])) {
                $visiteurData['nom'] = $_PATCH['nom'];
            }
            if (isset($_PATCH['prenom'])) {
                $visiteurData['prenom'] = $_PATCH['prenom'];
            }
            if (isset($_PATCH['email'])) {
                $visiteurData['email'] = $_PATCH['email'];
            }
            if (isset($_PATCH['telephone'])) {
                $visiteurData['telephone'] = $_PATCH['telephone'];
            }

            // Mettez à jour uniquement les champs qui ne sont pas vides
            if (!empty($visiteurData)) {
                updateVisiteur($id, $visiteurData);
                echo(json_encode(['message' => 'Visiteur modifié !']));
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
            deleteVisiteur($id);
            echo(json_encode(['message' => 'Visiteur supprimé !']));
        } else {
            echo(json_encode(['error' => 'ID manquant']));
        }
        break;

    default:
        echo(json_encode(['error' => 'Mauvaise méthode (GET, POST, PATCH, DELETE)']));
        break;
}
