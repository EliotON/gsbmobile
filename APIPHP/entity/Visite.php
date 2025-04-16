<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Visite implements JsonSerializable {
    private $id_visite; 
    private $id_visiteur;
    private $id_medecin;
    private $date_visite;
    private $heure_arrivee;
    private $temps_attente;
    private $heure_depart;
    private $temps_visite;
    private $rendez_vous;
    private $heure_debut_entretien;

    public function __construct() {
        $num_args = func_num_args();
        switch ($num_args) {
            case 0:
                break;
            case 10:
                $this->id_visite = func_get_arg(0); 
                $this->id_visiteur = func_get_arg(1); 
                $this->id_medecin = func_get_arg(2); 
                $this->date_visite = $this->formatDate(func_get_arg(3));  
                $this->heure_arrivee = $this->formatDateTime(func_get_arg(4));
                $this->heure_debut_entretien = $this->formatDateTime(func_get_arg(5));
                $this->temps_attente = func_get_arg(6);
                $this->heure_depart = $this->formatDateTime(func_get_arg(7));
                $this->temps_visite = func_get_arg(8);
                $this->rendez_vous = func_get_arg(9);
                break;
        }
    }

    // Méthodes pour formater les dates
    private function formatDate($date) {
        $dateTime = DateTime::createFromFormat('Y-m-d', $date);
        return $dateTime ? $dateTime->format('Y-m-d') : null; // Retourne null si le format est invalide
    }

    private function formatDateTime($dateTime) {
        $dateTimeObj = DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);
        return $dateTimeObj ? $dateTimeObj->format('Y-m-d H:i:s') : null; // Retourne null si le format est invalide
    }

    // Getters et Setters pour chaque propriété
    public function getIdVisite() {
        return $this->id_visite;
    }

    public function setIdVisite($id_visite) {
        $this->id_visite = $id_visite;
        return $this; 
    }

    public function getIdVisiteur() {
        return $this->id_visiteur;
    }

    public function setIdVisiteur($id_visiteur) {
        $this->id_visiteur = $id_visiteur;
        return $this;
    }

    public function getIdMedecin() {
        return $this->id_medecin;
    }

    public function setIdMedecin($id_medecin) {
        $this->id_medecin = $id_medecin;
        return $this;
    }

    public function getDateVisite() {
        return $this->date_visite;
    }

    public function setDateVisite($date_visite) {
        $this->date_visite = $this->formatDate($date_visite);
        return $this;
    }

    public function getHeureArrivee() {
        return $this->heure_arrivee;
    }

    public function setHeureArrivee($heure_arrivee) {
        $this->heure_arrivee = $this->formatDateTime($heure_arrivee);
        return $this;
    }

    public function getTempsAttente() {
        return $this->temps_attente;
    }

    public function setTempsAttente($temps_attente) {
        $this->temps_attente = $temps_attente;
        return $this;
    }

    public function getHeureDepart() {
        return $this->heure_depart;
    }

    public function setHeureDepart($heure_depart) {
        $this->heure_depart = $this->formatDateTime($heure_depart);
        return $this;
    }

    public function getTempsVisite() {
        return $this->temps_visite;
    }

    public function setTempsVisite($temps_visite) {
        $this->temps_visite = $temps_visite;
        return $this;
    }

    public function getRendezVous() {
        return $this->rendez_vous;
    }

    public function setRendezVous($rendez_vous) {
        $this->rendez_vous = $rendez_vous;
        return $this;
    }

    public function getHeureDebutEntretien() {
        return $this->heure_debut_entretien;
    }

    public function setHeureDebutEntretien($heure_debut_entretien) {
        $this->heure_debut_entretien = $this->formatDateTime($heure_debut_entretien);
        return $this;
    }

    public function jsonSerialize(): mixed { 
        return [
            'id_visite' => $this->id_visite,
            'id_visiteur' => $this->id_visiteur,
            'id_medecin' => $this->id_medecin,
            'date_visite' => $this->date_visite,
            'heure_arrivee' => $this->heure_arrivee,
            'heure_debut_entretien' => $this->heure_debut_entretien,
            'temps_attente' => $this->temps_attente,
            'heure_depart' => $this->heure_depart,
            'temps_visite' => $this->temps_visite,
            'rendez_vous' => $this->rendez_vous,
        ];
    }
}
?>
