<?php


namespace services;

use PDOException;

class DonneesService
{


    /**
     * Permet de récuperer (le code, la date, l'heure, le contexte le libelle de l'humeur,l'emoji, idUtil) ordonnée par date et heure de l'utilisateur $idUtil
     * @param $pdo
     * @param $idUtil ID de l'utilisateur
     * @return mixed $searchStmt Objet PDO qui stocke toutes les humeurs de l'utilisateur $idUtil
     */
    public function viewMoods($pdo, $idUtil)
    {
        $sql = "SELECT h.codeHumeur, h.dateHumeur, h.heure, h.contexte, l.libelleHumeur, l.emoji, h.idUtil 
                FROM humeur h, libelle l 
                WHERE h.libelle = l.codeLibelle 
                AND idUtil = :id 
                ORDER BY h.dateHumeur 
DESC, h.heure DESC";
        $searchStmt = $pdo->prepare($sql);
        $searchStmt->bindParam('id', $idUtil);
        $searchStmt->execute();
        return $searchStmt;
    }

    /**
     * @param $pdo  La connexion a la base de données
     * @param $idUtil ID utilisateur dans la base de données
     * @return int Le nombre d'humeur de l'utilisateur
     * 
     */
    public function nombreHumeur($pdo, $idUtil)
    {
        $sql = "SELECT COUNT(codeHumeur) as nbHumeur FROM humeur h WHERE idUtil = :id";
        $searchStmt = $pdo->prepare($sql);
        $searchStmt->bindParam('id', $idUtil);
        $searchStmt->execute();
        return $searchStmt->fetchColumn(0);
    }



    /**
     * Cette fonction permet de visualiser les humeurs jusqu'a le nombre $parPage d'humeur pour une page  de l'utilisateur
     * @param $pdo l'objet pdo
     * @param int $idUtil clé primaire de l'utilisateur dans la base de données
     * @param $premier
     * @param $parPage
     * @return \PDOStatement $searchStmt
     */
    public function viewMoodsPagination($pdo, int $idUtil, $premier, $parPage)
    {
        $sql = "SELECT h.codeHumeur, h.dateHumeur, h.heure, h.contexte, l.libelleHumeur, l.emoji, h.idUtil 
                FROM humeur h, libelle l 
                WHERE h.libelle = l.codeLibelle AND idUtil = :id ORDER BY h.dateHumeur DESC, h.heure DESC LIMIT :premier, :parpage";
        $searchStmt = $pdo->prepare($sql);
        $searchStmt->bindParam('id', $idUtil);
        $searchStmt->bindParam('premier', $premier);
        $searchStmt->bindParam('parpage', $parPage);
        $searchStmt->execute();
        return $searchStmt;
    }    


	public function supprimerCompte($pdo,$idUtil): string
    {
	   try{
			$pdo->beginTransaction();
			$sql = "DELETE FROM humeur WHERE  idUtil = :id";
			$searchStmt = $pdo->prepare($sql);
			$searchStmt->bindParam('id',$idUtil);
			$searchStmt->execute();
			$sql = "DELETE FROM utilisateur WHERE codeUtil = :id ";
			$searchStmt = $pdo->prepare($sql);
			$searchStmt->bindParam('id',$idUtil);
			$searchStmt->execute();
			$pdo->commit();
			return "ok";
			
	   }catch(PDOException $e){
		   $pdo->rollBack();
		   return "nOk";
	   }
   }
    private static $defaultDonneesService ;
    public static function getDefaultDonneesService()
    {
        if (DonneesService::$defaultDonneesService == null) {
            DonneesService::$defaultDonneesService = new DonneesService();
        }
        return DonneesService::$defaultDonneesService;
    }
	
	
	
}