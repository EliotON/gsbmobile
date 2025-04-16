<?php

include_once '../entity/Visiteur.php';
include_once '../pdo.php';
include_once '../global/config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function getUnVisiteur($id) {
    $pdo = PDO2::getInstance();
    $requete = $pdo->prepare("SELECT * FROM Visiteur WHERE id_visiteur = :id");
    $requete->bindParam(':id', $id, PDO::PARAM_INT);
    $requete->execute();
    $requete->setFetchMode(PDO::FETCH_CLASS, 'Visiteur');
    $visiteur = $requete->fetch();
    $requete->closeCursor();
    return $visiteur;
}

function getLesVisiteurs() {
    $pdo = PDO2::getInstance();
    $requete = $pdo->prepare("SELECT * FROM Visiteur");
    $requete->execute();
    return $requete->fetchAll(PDO::FETCH_CLASS, "Visiteur");
}

function getLesMedecins(){
    $pdo = PDO2::getInstance();
    $requete = $pdo->prepare("SELECT * FROM Medecin");
    $requete->execute();
    return $requete->fetchAll(PDO::FETCH_CLASS, "Medecin");
}

function addVisiteur($visiteur) {
    $pdo = PDO2::getInstance();
    $requete = $pdo->prepare("INSERT INTO Visiteur (nom, prenom, email, telephone) VALUES (:nom, :prenom, :email, :telephone)");

    // Stockez les valeurs dans des variables
    $nom = $visiteur->getNom();
    $prenom = $visiteur->getPrenom();
    $email = $visiteur->getEmail();
    $telephone = $visiteur->getTelephone();

    // Passez maintenant les variables à bindParam
    $requete->bindParam(':nom', $nom, PDO::PARAM_STR);
    $requete->bindParam(':prenom', $prenom, PDO::PARAM_STR);
    $requete->bindParam(':email', $email, PDO::PARAM_STR);
    $requete->bindParam(':telephone', $telephone, PDO::PARAM_STR);

    $requete->execute();
    $requete->closeCursor();
}


function updateVisiteur($id, $visiteurData) {
    $pdo = PDO2::getInstance();

    // Créer une liste dynamique des colonnes à mettre à jour
    $sets = [];
    foreach ($visiteurData as $key => $value) {
        $sets[] = "$key = :$key";
    }
    $setString = implode(', ', $sets);

    // Préparer la requête SQL
    $sql = "UPDATE Visiteur SET $setString WHERE id_visiteur = :id";
    $requete = $pdo->prepare($sql);

    // Vérifier si le visiteur existe avant la mise à jour
    if (getUnVisiteur($id) === false) {
        return "Visiteur non trouvé !";
    }

    // Lier les paramètres
    foreach ($visiteurData as $key => $value) {
        $requete->bindParam(":$key", $visiteurData[$key], PDO::PARAM_STR);
    }
    $requete->bindParam(':id', $id, PDO::PARAM_INT);

    // Exécuter la requête
    $requete->execute();
    $requete->closeCursor();

    return "Visiteur modifié !";
}

function deleteVisiteur($id) {
    $pdo = PDO2::getInstance();
    $requete = $pdo->prepare("DELETE FROM Visiteur WHERE id_visiteur = :id");
    $requete->bindParam(':id', $id, PDO::PARAM_INT);
    $requete->execute();
    $requete->closeCursor();
}
