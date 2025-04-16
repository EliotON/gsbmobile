<?php
require_once __DIR__ . '/../pdo.php';
require_once __DIR__ . '/../entity/Cabinet.php';
require_once __DIR__ . '/../entity/Medecin.php';

function getUnCabinet($id) {
    try {
        $pdo = PDO2::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM Cabinet WHERE id_cabinet = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Cabinet');
        return $stmt->fetch();
    } catch (PDOException $e) {
        throw new Exception("Erreur lors de la récupération du cabinet: " . $e->getMessage());
    }
}

function getLesCabinets() {
    try {
        $pdo = PDO2::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM Cabinet");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, "Cabinet");
    } catch (PDOException $e) {
        throw new Exception("Erreur lors de la récupération des cabinets: " . $e->getMessage());
    }
}

function getCabinetsPlusProches($latitude, $longitude) {
    try {
        $pdo = PDO2::getInstance();
        $stmt = $pdo->prepare("
            SELECT *, 
            (6371 * acos(cos(radians(:lat)) * cos(radians(latitude)) * 
            cos(radians(longitude) - radians(:long)) + sin(radians(:lat)) * 
            sin(radians(latitude)))) AS distance 
            FROM Cabinet 
            ORDER BY distance ASC
        ");
        $stmt->bindParam(':lat', $latitude, PDO::PARAM_STR);
        $stmt->bindParam(':long', $longitude, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, "Cabinet");
    } catch (PDOException $e) {
        throw new Exception("Erreur lors de la recherche des cabinets proches: " . $e->getMessage());
    }
}

function getMedecinsCabinet($id_cabinet) {
    try {
        $pdo = PDO2::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM Medecin WHERE id_cabinet = :id_cabinet");
        $stmt->bindParam(':id_cabinet', $id_cabinet, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, "Medecin");
    } catch (PDOException $e) {
        throw new Exception("Erreur lors de la récupération des médecins: " . $e->getMessage());
    }
}

function addCabinet(Cabinet $cabinet) {
    try {
        $pdo = PDO2::getInstance();
        $sql = "INSERT INTO Cabinet (rue, ville, code_postal, telephone, latitude, longitude) 
                VALUES (:rue, :ville, :code_postal, :telephone, :latitude, :longitude)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'rue' => $cabinet->getRue(),
            'ville' => $cabinet->getVille(),
            'code_postal' => $cabinet->getCodePostal(),
            'telephone' => $cabinet->getTelephone(),
            'latitude' => $cabinet->getLatitude(),
            'longitude' => $cabinet->getLongitude()
        ]);
        
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        throw new Exception("Erreur lors de l'ajout du cabinet: " . $e->getMessage());
    }
}

function updateCabinet($id, array $data) {
    try {
        $pdo = PDO2::getInstance();
        $sets = [];
        foreach ($data as $key => $value) {
            $sets[] = "$key = :$key";
        }
        
        $sql = "UPDATE Cabinet SET " . implode(', ', $sets) . " WHERE id_cabinet = :id";
        $data['id'] = $id;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
        
        if ($stmt->rowCount() === 0) {
            throw new Exception("Cabinet non trouvé");
        }
    } catch (PDOException $e) {
        throw new Exception("Erreur lors de la mise à jour du cabinet: " . $e->getMessage());
    }
}
