<?php
class Cabinet implements JsonSerializable {
    private $id_cabinet;
    private $rue;
    private $ville;
    private $code_postal;
    private $telephone;
    private $latitude;
    private $longitude;
    protected $medecins = array();
    protected $distance;  // Added for proximity calculations

    public function __construct() {
        $num_args = func_num_args();
        switch ($num_args) {
            case 0:
                break;
            case 7:
                $this->id_cabinet = func_get_arg(0);
                $this->rue = func_get_arg(1);
                $this->ville = func_get_arg(2);
                $this->code_postal = func_get_arg(3);
                $this->telephone = func_get_arg(4);
                $this->latitude = func_get_arg(5);
                $this->longitude = func_get_arg(6);
                break;
        }
    }

    // Getters et Setters
    public function getIdCabinet() { return $this->id_cabinet; }
    public function getRue() { return $this->rue; }
    public function getVille() { return $this->ville; }
    public function getCodePostal() { return $this->code_postal; }
    public function getTelephone() { return $this->telephone; }
    public function getLatitude() { return $this->latitude; }
    public function getLongitude() { return $this->longitude; }
    public function getMedecins() { return $this->medecins; }
    public function getDistance() { return $this->distance; }

    public function setIdCabinet($id) { $this->id_cabinet = $id; return $this; }
    public function setRue($rue) { $this->rue = $rue; return $this; }
    public function setVille($ville) { $this->ville = $ville; return $this; }
    public function setCodePostal($cp) { $this->code_postal = $cp; return $this; }
    public function setTelephone($tel) { $this->telephone = $tel; return $this; }
    public function setLatitude($lat) { $this->latitude = $lat; return $this; }
    public function setLongitude($long) { $this->longitude = $long; return $this; }
    public function setDistance($distance) { $this->distance = $distance; return $this; }

    // Gestion des médecins
    public function addMedecins($medecins) {
        $this->medecins = $medecins;
        return $this;
    }

    public function jsonSerialize(): mixed {
        return [
            'id_cabinet' => $this->id_cabinet,
            'rue' => $this->rue,
            'ville' => $this->ville,
            'code_postal' => $this->code_postal,
            'telephone' => $this->telephone,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'medecins' => $this->medecins,
            'distance' => $this->distance
        ];
    }
}
