<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Medecin implements JsonSerializable {
    private $id_medecin; 
    private $id_cabinet;  // Added property
    private $id_visiteur; // Added property
    private $nom;
    private $prenom;
    private $rue;
    private $ville;
    private $code_postal;
    private $telephone;

    public function __construct() {
        $num_args = func_num_args();
        switch ($num_args) {
            case 0:
                break;
            case 7:
                $this->id_medecin = func_get_arg(0); 
                $this->nom = func_get_arg(1); 
                $this->prenom = func_get_arg(2); 
                $this->rue = func_get_arg(3); 
                $this->ville = func_get_arg(4); 
                $this->code_postal = func_get_arg(5); 
                $this->telephone = func_get_arg(6); 
                break;
        }
    }

    // Getter et Setter pour id_medecin
    public function getIdMedecin() {
        return $this->id_medecin;
    }

    public function setIdMedecin($id_medecin) {
        $this->id_medecin = $id_medecin;
        return $this; 
    }

    // Getter et Setter pour id_cabinet
    public function getIdCabinet() {
        return $this->id_cabinet;
    }

    public function setIdCabinet($id_cabinet) {
        $this->id_cabinet = $id_cabinet;
        return $this;
    }

    // Getter et Setter pour id_visiteur
    public function getIdVisiteur() {
        return $this->id_visiteur;
    }

    public function setIdVisiteur($id_visiteur) {
        $this->id_visiteur = $id_visiteur;
        return $this;
    }

    // Getter et Setter pour nom
    public function getNom() {
        return $this->nom;
    }

    public function setNom($nom) {
        $this->nom = $nom;
        return $this;
    }

    // Getter et Setter pour prenom
    public function getPrenom() {
        return $this->prenom;
    }

    public function setPrenom($prenom) {
        $this->prenom = $prenom;
        return $this;
    }

    // Getter et Setter pour rue
    public function getRue() {
        return $this->rue;
    }

    public function setRue($rue) {
        $this->rue = $rue;
        return $this;
    }

    // Getter et Setter pour ville
    public function getVille() {
        return $this->ville;
    }

    public function setVille($ville) {
        $this->ville = $ville;
        return $this;
    }

    // Getter et Setter pour code_postal
    public function getCodePostal() {
        return $this->code_postal;
    }

    public function setCodePostal($code_postal) {
        $this->code_postal = $code_postal;
        return $this;
    }

    // Getter et Setter pour telephone
    public function getTelephone() {
        return $this->telephone;
    }

    public function setTelephone($telephone) {
        $this->telephone = $telephone;
        return $this;
    }

    public function jsonSerialize(): mixed {
        return [
            'id_medecin' => $this->id_medecin,
            'id_cabinet' => $this->id_cabinet,
            'id_visiteur' => $this->id_visiteur,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'rue' => $this->rue,
            'ville' => $this->ville,
            'code_postal' => $this->code_postal,
            'telephone' => $this->telephone,
        ];
    }
}
?>
