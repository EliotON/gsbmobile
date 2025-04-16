<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'Personne.php';

class Visiteur extends Personne implements JsonSerializable {
    protected $id_visiteur; 
    protected $nom;
    protected $prenom;
    protected $email;
    protected $telephone;
    private $password;
    private $auth_token;
    private $token_expiration;
    private $visites = array();

    public function __construct($id = null, $nom = null, $prenom = null, $email = null, $telephone = null, $password = null) {
        parent::__construct($id, $nom, $prenom, $email, $telephone);
        $this->password = $password;
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

    // Getter et Setter pour email
    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
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

    // Getter et Setter pour password
    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    // Nouvelles méthodes pour la double navigabilité
    public function getVisites() {
        return $this->visites;
    }

    public function addVisite($visite) {
        $this->visites[] = $visite;
        return $this;
    }

    public function removeVisite($visite) {
        if (($key = array_search($visite, $this->visites)) !== false) {
            unset($this->visites[$key]);
        }
        return $this;
    }



    public function jsonSerialize(): mixed {
        $parentData = parent::jsonSerialize();
        return array_merge($parentData, [
            'id_visiteur' => $this->id_visiteur,
            'visites' => $this->visites
        ]);
    }
}
?>
