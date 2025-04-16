<?php

include_once '../entity/Visite.php';
include_once '../pdo.php';
include_once '../global/config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function getUneVisite($id){
    $pdo = PDO2::getInstance();
    $requete = $pdo->prepare("SELECT * FROM Visite WHERE id_visite = :id");
    $requete->bindParam(':id', $id, PDO::PARAM_INT);
    $requete->execute();
    $requete->setFetchMode(PDO::FETCH_CLASS, 'Visite');
    $visite = $requete->fetch();
    $requete->closeCursor();
    return $visite;
}

function getLesVisites(){
    $pdo = PDO2::getInstance();
    $requete = $pdo->prepare("SELECT * FROM Visite");
    $requete->execute();
    return $requete->fetchAll(PDO::FETCH_CLASS, "Visite");
}

function addVisite($visite) {
    $pdo = PDO2::getInstance();
    $requete = $pdo->prepare("INSERT INTO Visite (id_visiteur, id_medecin, date_visite, heure_arrivee, temps_attente, heure_depart, temps_visite, rendez_vous) VALUES (:id_visiteur, :id_medecin, :date_visite, :heure_arrivee, :temps_attente, :heure_depart, :temps_visite, :rendez_vous)");

    // Stockez les valeurs dans des variables
    $id_visiteur = $visite->getIdVisiteur();
    $id_medecin = $visite->getIdMedecin();
    $date_visite = $visite->getDateVisite();
    $heure_arrivee = $visite->getHeureArrivee();
    $temps_attente = $visite->getTempsAttente();
    $heure_depart = $visite->getHeureDepart();
    $temps_visite = $visite->getTempsVisite();
    $rendez_vous = $visite->getRendezVous();

    // Passez maintenant les variables à bindParam
    $requete->bindParam(':id_visiteur', $id_visiteur, PDO::PARAM_INT);
    $requete->bindParam(':id_medecin', $id_medecin, PDO::PARAM_INT);
    $requete->bindParam(':date_visite', $date_visite, PDO::PARAM_STR);
    $requete->bindParam(':heure_arrivee', $heure_arrivee, PDO::PARAM_STR);
    $requete->bindParam(':temps_attente', $temps_attente, PDO::PARAM_STR);
    $requete->bindParam(':heure_depart', $heure_depart, PDO::PARAM_STR);
    $requete->bindParam(':temps_visite', $temps_visite, PDO::PARAM_STR);
    $requete->bindParam(':rendez_vous', $rendez_vous, PDO::PARAM_BOOL);

    $requete->execute();
    $requete->closeCursor();
}

function updateVisite($id, $visiteData) {
    $pdo = PDO2::getInstance();

    // Créer une liste dynamique des colonnes à mettre à jour
    $sets = [];
    foreach ($visiteData as $key => $value) {
        $sets[] = "$key = :$key";
    }
    $setString = implode(', ', $sets);

    // Préparer la requête SQL
    $sql = "UPDATE Visite SET $setString WHERE id_visite = :id";
    $requete = $pdo->prepare($sql);

    // Vérifier si la visite existe avant la mise à jour
    if (getUneVisite($id) === false) { // Corrigé ici
        return "Visite non trouvée !";
    }

    // Lier les paramètres
    foreach ($visiteData as $key => $value) {
        $requete->bindParam(":$key", $visiteData[$key], PDO::PARAM_STR);
    }
    $requete->bindParam(':id', $id, PDO::PARAM_INT);

    // Exécuter la requête
    $requete->execute();
    $requete->closeCursor();

    return "Visite modifiée !";
}

function deleteVisite($id) {
    $pdo = PDO2::getInstance();
    $requete = $pdo->prepare("DELETE FROM Visite WHERE id_visite = :id");
    $requete->bindParam(':id', $id, PDO::PARAM_INT);
    $requete->execute();
    $requete->closeCursor();
}
