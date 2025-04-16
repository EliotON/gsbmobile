-- Ajout de la colonne pour l'heure de début d'entretien
ALTER TABLE Visite
ADD COLUMN heure_debut_entretien datetime DEFAULT NULL
AFTER heure_arrivee;
