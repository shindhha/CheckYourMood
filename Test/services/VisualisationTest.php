<?php

namespace services;

use DataBase;
use PHPUnit\Framework\TestCase;

require_once 'yasmf/datasource.php';

require_once 'services/VisualisationService.php';

require_once 'Test/DataBase.php';

class VisualisationTest extends TestCase
{
    private $pdo;
    private $services;

    protected function setUp(): void
    {
        $this->services = VisualisationService::getDefaultVisualisationService();
        $this->pdo = DataBase::getPDOTest();
        $this->pdo->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->pdo->rollBack();
    }

    public function testVisualisationRadarSuccess()
    {
    //given : un tableau de semaine avec le nombres d'humeurs par jour

        $tabSemaine = [
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 3,
            7 => 0
        ];
        $idUtil = 1;
        $code = 1;
        $week = 0;
        $anneeAComparer = '2021';
        // WHEN : on appelle la fonction
        $result = $this->services->visualisationRadar($this->pdo, $idUtil, $code, $week, $anneeAComparer);
        // THEN : on vérifie que le tableau de semaine est le même
        $this->assertEquals($tabSemaine, $result);
    }

    public function testVisualisationRadarFailled()
    {

        // GIVEN : un tableau de semaine avec un nombre d'humeurs par jour faux
        $tabSemaine = [
            1 => 0,
            2 => 0,
            3 => 1,
            4 => 0,
            5 => 0,
            6 => 0,
            7 => 0
        ];
        $idUtil = 1;
        $code = 22;
        $week = 0;
        $anneeAComparer = '2022';
        // WHEN : on appelle la fonction
        $result = $this->services->visualisationRadar($this->pdo, $idUtil, $code, $week, $anneeAComparer);
        // THEN : on vérifie que le tableau de semaine est différent
        $this->assertFalse($tabSemaine == $result);
    }

    public function testVisualisationDoughnutSuccess()
    {
        // GIVEN : une date avec un id utilisateur
        $date = '2021-01-01';
        $idUtil = 1;
        $tab = [
            'Admiration' => 3   ,
            'Adoration' => 2
        ];
        // WHEN : on appelle la fonction
        $result = $this->services->visualisationDoughnut($this->pdo, $idUtil, $date);
        // THEN : on vérifie que le tableau d'humeurs est le même
        $this->assertEquals($result, $tab);
    }

    public function testVisualisationDoughnutFailled()
    {
        //GIVEN une date avec un id utilisateur
        $date = '2022-01-01';
        $idUtil = 1;
        $tab = [
            'Peur' => 2,
            'Soulagement' => 1
        ];
        //WHEN on execute la fonction
        $result = $this->services->visualisationDoughnut($this->pdo, $idUtil, $date);
        //THEN on verifie que les tableaux sont différents
        $this->assertFalse($result == $tab);
    }

    public function testVisualisationTableauSuccess()
    {
        //GIVEN une année avec un id utilisateur et une semaine
        $week = 0;
        $idUtil = 1;
        $anneeAComparer = '2021';
        //WHEN on execute la fonction
        $result = $this->services->visualisationTableau($this->pdo, $idUtil, $week, $anneeAComparer);
        //THEN on verifie que le nombre de ligne est le même
        $this->assertTrue($result->rowCount() ==6);
    }

    public function testVisualisationTableauFailled()
    {
        //GIVEN une année avec un id utilisateur et une semaine
        $week = 0;
        $idUtil = 1;
        $anneeAComparer = '2022';
        //WHEN on execute la fonction
        $result = $this->services->visualisationTableau($this->pdo, $idUtil, $week, $anneeAComparer);
        // vérification du nombre ligne est invalide
        $this->assertFalse($result->rowCount() == 0);
    }

    public function testVisualisationHumeurAnneeLaPlusSuccess()
    {
        // GIVEN : un id utilisateur et une année
        $idUtil = 1;
        $year = '2021';
        // WHEN : on appelle la fonction
        $result = $this->services->visualisationHumeurAnneeLaPlus($this->pdo, $idUtil, $year);
        // THEN : on vérifie que l'humeur est la même
        $this->assertTrue($result->fetchAll()[0]['libelle'] == 'Admiration');
    }

    public function testVisualisationHumeurAnneeLaPlus()
    {
        // GIVEN : un id utilisateur et une année
        $idUtil = 1;
        $year = '2022';
        // WHEN : on appelle la fonction
        $result = $this->services->visualisationHumeurAnneeLaPlus($this->pdo, $idUtil, $year);
        // THEN : on vérifie que l'humeur est soulagement
        $this->assertFalse($result->fetchAll()[0]['libelle'] == 'Soulagement');
    }


    public function testVisualisationHumeurJourSuccess()
    {
        // GIVEN : un id utilisateur et une date
        $idUtil = 1;
        $dateJour = '2021-01-01';
        // WHEN : on appelle la fonction
        $result = $this->services->visualisationHumeurJour($this->pdo, $idUtil, $dateJour);
        // THEN : on vérifie que l'humeur est Admiration
        $resultatAttendu = 'Admiration';
        $this->assertEquals($result->fetchAll()[0]['libelle'], $resultatAttendu);
    }


    public function testVisualisationHumeurJourFailled()
    {
        // GIVEN : un id utilisateur et une date
        $idUtil = 1;
        // date avec aucun ajout d'humeur
        $dateJour = '2010-01-01';
        // WHEN : on appelle la fonction
        $result = $this->services->visualisationHumeurJour($this->pdo, $idUtil, $dateJour);
        // THEN : on vérifie que le nombre de ligne retourné est 0
        $this->assertEquals($result->rowCount(), 0);
    }


    public function testVisualisationHumeurSemaineSuccess()
    {
        // GIVEN : un id utilisateur et une semaine
        $idUtil = 1;
        $week = 0;
        // WHEN : on appelle la fonction
        $result = $this->services->VisualisationHumeurSemaine($this->pdo, $idUtil, $week);
        $resultatAttendu = 'Admiration';
        // THEN : on vérifie que l'humeur Admiration est la plus populaire de la semaine
        $this->assertTrue($result->fetchAll()[0]['libelle'] == $resultatAttendu);
    }

    public function testVisualisationHumeurSemaineFailled()
    {
        // GIVEN : un id utilisateur et une semaine
        $idUtil = 1;
        $week = 1;
        // WHEN : on appelle la fonction
        $result = $this->services->VisualisationHumeurSemaine($this->pdo, $idUtil, $week);
        $resultatAttendu = 'Joie';// mauvais résultat
        // THEN : on vérifie que l'humeur Joie n'est pas la plus populaire de la semaine
        $this->assertFalse($result->fetchAll()[0]['libelle'] == $resultatAttendu);
    }

    public function testVisualisationHumeurAnneeSuccess()
    {
        //GIVEN : un id utilisateur et une année et un moi avec un nombre d'humeur
        $idUtil = 1;
        $annee = '2021';
        $libelle = 1;
        $tabAttendu = ['janvier' => 3,
        ];
        //WHEN : on appelle la fonction
        $tab = $this->services->visualisationHumeurAnnee($this->pdo, $idUtil, $annee, $libelle);
        //THEN : on vérifie que pour le mois de janvier il y a 3 humeur avec pour libelle 1
        $this->assertEquals($tab[0]['y'], $tabAttendu['janvier']);
    }

    public function testVisualisationHumeurAnneeFailled()
    {
        //GIVEN : un id utilisateur et une année et un moi avec un nombre d'humeur
        $idUtil = 1;
        $annee = '2022';
        $libelle = 22;
        $tabAttendu = ['janvier' => 5,//nombre incorrecte d'humeur pour le mois de janvier
        ];
        //WHEN : on appelle la fonction
        $tab = $this->services->visualisationHumeurAnnee($this->pdo, $idUtil, $annee, $libelle);
        //THEN : on verifie que pour le mois de janvier il n'y  a pas 5 humeur avec pour libelle 22
        $this->assertFalse($tab[0]['y'] == $tabAttendu['janvier']);
    }
}

    
    

