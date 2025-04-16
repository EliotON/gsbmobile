-- Création de la base de données
CREATE DATABASE GSBVTT;
USE GSBVTT;

-- Table des visiteurs
CREATE TABLE Visiteur (
  id_visiteur INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  prenom VARCHAR(100) NOT NULL,
  email VARCHAR(100),
  telephone VARCHAR(20)
);

-- Table des médecins avec le champ rpps
CREATE TABLE Medecin (
  id_medecin INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  prenom VARCHAR(100) NOT NULL,
  rue VARCHAR(255),
  ville VARCHAR(100),
  code_postal VARCHAR(20),
  telephone VARCHAR(20)
);

-- Table des visites
CREATE TABLE Visite (
  id_visite INT AUTO_INCREMENT PRIMARY KEY,
  id_visiteur INT NOT NULL,
  id_medecin INT NOT NULL,
  date_visite DATETIME NOT NULL,
  heure_arrivee TIME,
  temps_attente TIME,
  heure_depart TIME,
  temps_visite TIME,
  rendez_vous BOOLEAN,
  FOREIGN KEY (id_visiteur) REFERENCES Visiteur(id_visiteur),
  FOREIGN KEY (id_medecin) REFERENCES Medecin(id_medecin)
);
