<?php


namespace services;

use PDO;
use PDOStatement;
class MoodService
{
    /**
     * Cette fonction permet de visualisé l'ensemble des humeurs
     * @param PDO $pdo la connexion a la base de données
     * @param int $idUtil clé primaire de l'utilisateur dans la base de données
     * @return \PDOStatement the statement referencing the result set
     */
    public function viewMoods(PDO $pdo,int $idUtil): \PDOStatement
    {
        $sql = "SELECT h.codeHumeur, h.dateHumeur, h.heure, h.contexte, l.libelleHumeur, l.emoji, h.idUtil FROM humeur h, libelle l WHERE h.libelle = l.codeLibelle AND idUtil = :id ORDER BY h.dateHumeur DESC, h.heure DESC";
        $searchStmt = $pdo->prepare($sql);
        $searchStmt->bindParam('id', $idUtil);
        $searchStmt->execute();
        return $searchStmt;
    }
    /**
     * Cette fonction récuperer l'ensemble des libelles
     * @param PDO $pdo la connexion a la base de données
     * @return \PDOStatement $searchStmt PDO Object qui stock tout les libelles d'humeur
     */
    public function libelles($pdo){
        $sql = "SELECT codeLibelle, libelleHumeur, emoji FROM Libelle ORDER BY libelleHumeur";
        $searchStmt = $pdo->prepare($sql);
        $searchStmt->execute();
        return $searchStmt;
    }



    private static $defaultMoodService;
    public static function getDefaultMoodService()
    {
        if (MoodService::$defaultMoodService == null) {
            MoodService::$defaultMoodService = new MoodService();
        }
        return MoodService::$defaultMoodService;
    }
}