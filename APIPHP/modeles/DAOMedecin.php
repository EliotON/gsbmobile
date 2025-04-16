<?php

include_once '../entity/Medecin.php';
include_once '../pdo.php';
include_once '../global/config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


function getUnMedecin($id){
    $pdo = PDO2::getInstance();
    $requete = $pdo->prepare("SELECT * FROM Medecin WHERE id_medecin = :id");
    $requete->bindParam(':id', $id, PDO::PARAM_INT);
    $requete->execute();
    $requete->setFetchMode(PDO::FETCH_CLASS, 'Medecin');
    $medecin = $requete->fetch();
    $requete->closeCursor();
    return $medecin;
}

function getLesMedecins(){
    $pdo = PDO2::getInstance();
    $requete = $pdo->prepare("SELECT * FROM Medecin");
    $requete->execute();
    return $requete->fetchAll(PDO::FETCH_CLASS, "Medecin");
}

function addMedecin($medecin) {
    $pdo = PDO2::getInstance();
    $requete = $pdo->prepare("INSERT INTO Medecin (nom, prenom, rue, ville, code_postal, telephone) VALUES (:nom, :prenom, :rue, :ville, :code_postal, :telephone)");

    // Stockez les valeurs dans des variables
    $nom = $medecin->getNom();
    $prenom = $medecin->getPrenom();
    $rue = $medecin->getRue();
    $ville = $medecin->getVille();
    $code_postal = $medecin->getCodePostal();
    $telephone = $medecin->getTelephone();

    // Passez maintenant les variables à bindParam
    $requete->bindParam(':nom', $nom, PDO::PARAM_STR);
    $requete->bindParam(':prenom', $prenom, PDO::PARAM_STR);
    $requete->bindParam(':rue', $rue, PDO::PARAM_STR);
    $requete->bindParam(':ville', $ville, PDO::PARAM_STR);
    $requete->bindParam(':code_postal', $code_postal, PDO::PARAM_STR);
    $requete->bindParam(':telephone', $telephone, PDO::PARAM_STR);

    $requete->execute();
    $requete->closeCursor();
}


function updateMedecin($id, $medecinData) {
    $pdo = PDO2::getInstance();

    // Créer une liste dynamique des colonnes à mettre à jour
    $sets = [];
    foreach ($medecinData as $key => $value) {
        $sets[] = "$key = :$key";
    }
    $setString = implode(', ', $sets);

    // Préparer la requête SQL
    $sql = "UPDATE Medecin SET $setString WHERE id_medecin = :id";
    $requete = $pdo->prepare($sql);

    // Vérifier si le médecin existe avant la mise à jour
    if (getUnMedecin($id) === false) {
        return "Médecin non trouvé !";
    }

    // Lier les paramètres
    foreach ($medecinData as $key => $value) {
        $requete->bindParam(":$key", $medecinData[$key], PDO::PARAM_STR);
    }
    $requete->bindParam(':id', $id, PDO::PARAM_INT);

    // Exécuter la requête
    $requete->execute();
    $requete->closeCursor();

    return "Médecin modifié !";
}


function deleteMedecin($id) {
    $pdo = PDO2::getInstance();
    $requete = $pdo->prepare("DELETE FROM Medecin WHERE id_medecin = :id");
    $requete->bindParam(':id', $id, PDO::PARAM_INT);
    $requete->execute();
    $requete->closeCursor();
}

function getMedecinsByVisiteur($id_visiteur) {
    $pdo = PDO2::getInstance();
    $requete = $pdo->prepare("SELECT * FROM Medecin WHERE id_visiteur = :id_visiteur");
    $requete->bindParam(':id_visiteur', $id_visiteur, PDO::PARAM_INT);
    $requete->execute();
    return $requete->fetchAll(PDO::FETCH_CLASS, "Medecin");
}
