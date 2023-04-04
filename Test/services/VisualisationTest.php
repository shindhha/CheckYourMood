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


        $tabSemaine = [
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 1,
            5 => 0,
            6 => 0,
            7 => 0
        ];
        $idUtil = 1;
        $code = 22;
        $week = 1;
        $anneeAComparer = '2022';

        $result = $this->services->visualisationRadar($this->pdo, $idUtil, $code, $week, $anneeAComparer);

        $this->assertEquals($tabSemaine, $result);
    }

    public function testVisualisationRadarFailled()
    {


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
        $week = 1;
        $anneeAComparer = '2022';

        $result = $this->services->visualisationRadar($this->pdo, $idUtil, $code, $week, $anneeAComparer);

        $this->assertFalse($tabSemaine == $result);
    }

    public function testVisualisationDoughnutSuccess()
    {
        $date = '2022-01-01';
        $idUtil = 1;
        $tab = [
            'Peur' => 1,
            'Soulagement' => 1
        ];
        $result = $this->services->visualisationDoughnut($this->pdo, $idUtil, $date);

        $this->assertEquals($result, $tab);
    }

    public function testVisualisationDoughnutFailled()
    {
        $date = '2022-01-01';
        $idUtil = 1;
        $tab = [
            'Peur' => 2,
            'Soulagement' => 1
        ];
        $result = $this->services->visualisationDoughnut($this->pdo, $idUtil, $date);
        $this->assertFalse($result == $tab);
    }

    public function testVisualisationTableauSuccess()
    {

        $week = 4;
        $idUtil = 1;
        $anneeAComparer = '2022';

        $result = $this->services->visualisationTableau($this->pdo, $idUtil, $week, $anneeAComparer);
        // vérification du nombre ligne identique
        $this->assertTrue($result->rowCount() == 25);
    }

    public function testVisualisationTableauFailled()
    {

        $week = 4;
        $idUtil = 1;
        $anneeAComparer = '2022';

        $result = $this->services->visualisationTableau($this->pdo, $idUtil, $week, $anneeAComparer);
        // vérification du nombre ligne identique
        $this->assertFalse($result->rowCount() == 0);
    }

    public function testVisualisationHumeurAnneeLaPlusSuccess()
    {
        $idUtil = 1;
        $year = '2022';

        $result = $this->services->visualisationHumeurAnneeLaPlus($this->pdo, $idUtil, $year);

        $this->assertTrue($result->fetchAll()[0]['libelle'] == 'Adoration');
    }

    public function testVisualisationHumeurAnneeLaPlus()
    {

        $idUtil = 1;
        $year = '2022';

        $result = $this->services->visualisationHumeurAnneeLaPlus($this->pdo, $idUtil, $year);

        $this->assertFalse($result->fetchAll()[0]['libelle'] == 'Soulagement');
    }


    public function testVisualisationHumeurJourSuccess()
    {

        $idUtil = 1;
        $dateJour = '2022-01-01';
        $result = $this->services->visualisationHumeurJour($this->pdo, $idUtil, $dateJour);
        $resultatAttendu = 'Peur';
        $this->assertEquals($result->fetchAll()[0]['libelle'], $resultatAttendu);
    }


    public function testVisualisationHumeurJourFailled()
    {
        $idUtil = 1;
        // date avec aucun ajout d'humeur
        $dateJour = '2010-01-01';
        $result = $this->services->visualisationHumeurJour($this->pdo, $idUtil, $dateJour);
        $this->assertEquals($result->rowCount(), 0);
    }


    public function testVisualisationHumeurSemaineSuccess()
    {
        $idUtil = 1;
        $week = 1;
        $result = $this->services->VisualisationHumeurSemaine($this->pdo, $idUtil, $week);
        $resultatAttendu = 'Peur';

        $this->assertTrue($result->fetchAll()[0]['libelle'] == $resultatAttendu);
    }

    public function testVisualisationHumeurSemaineFailled()
    {
        $idUtil = 1;
        $week = 1;
        $result = $this->services->VisualisationHumeurSemaine($this->pdo, $idUtil, $week);
        $resultatAttendu = 'Joie';// mauvais résultat

        $this->assertFalse($result->fetchAll()[0]['libelle'] == $resultatAttendu);
    }

    public function testVisualisationHumeurAnneeSuccess()
    {
        $idUtil = 1;
        $annee = '2022';
        $libelle = 22;
        $tabAttendu = ['janvier' => 7,
        ];

        $tab = $this->services->visualisationHumeurAnnee($this->pdo, $idUtil, $annee, $libelle);

        $this->assertEquals($tab[0], $tab[0]);
        $this->assertEquals($tab[0]['y'], $tabAttendu['janvier']);
    }

    public function testVisualisationHumeurAnneeFailled()
    {
        $idUtil = 1;
        $annee = '2022';
        $libelle = 22;
        $tabAttendu = ['janvier' => 5,//nombre incorrecte d'humeur pour le mois de janvier
        ];

        $tab = $this->services->visualisationHumeurAnnee($this->pdo, $idUtil, $annee, $libelle);


        $this->assertFalse($tab[0]['y'] == $tabAttendu['janvier']);
    }
}

    
    

