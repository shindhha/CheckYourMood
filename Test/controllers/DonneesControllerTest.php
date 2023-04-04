<?php

namespace controllers;
require_once 'Test/DataBase.php';
require_once 'services/MoodService.php';
require_once 'services/DonneesService.php';
require_once 'services/VisualisationService.php';
require_once 'controllers/DonneesController.php';

use Modeles\QueryBuilder;
use PHPUnit\Framework\TestCase;
use services\DonneesService;
use services\MoodService;
use services\VisualisationService;
use DataBase;
use Yaf\Exception\LoadFailed\View;
use function PHPUnit\Framework\assertEquals;

class DonneesControllerTest extends TestCase
{

    private $pdo;
    private DonneesService $donneesService;
    private MoodService $moodService;
    private DonneesController $donneesController;
    private VisualisationService $visualisationService;
    protected function setUp(): void
    {
        $this->pdo = DataBase::getPDOTest();
        $this->pdo->beginTransaction();
        $this->moodService = $this->createStub(MoodService::class);
        $this->donneesService = $this->createStub(DonneesService::class);
        $this->visualisationService = $this->createStub(VisualisationService::class);
        $this->donneesController = new \controllers\DonneesController($this->donneesService,$this->moodService,$this->visualisationService);
    }

    protected function tearDown(): void
    {
        unset($_POST);
        unset($_GET);
        unset($_SESSION);
        $this->pdo->rollBack();
    }
    public function testgoToMood() {
        // GIVEN Un formulaire par défault
        $_POST['namepage'] = "humeur";
        $_SESSION['util'] = 1; // Identifiant de l'utilisateur
        // WHEN L'utilisateur clique sur le bouton 'voir une page'
        $view = $this->donneesController->goToMood($this->pdo);
        // THEN
        // 1. Il est rediriger vers la page des humeurs
        self::assertEquals("check-your-mood/views/humeur",$view->getRelativePath());
    }

    public function testChangementPageUpdateHumeur() {
        // GIVEN Un utilisateur sur la page n°56 des humeurs pour modifier le contexte d'une humeur.
        $_SESSION['util'] = 1; // Identifiant de l'utilisateur
        $_POST['noPage'] = 56;
        $_POST['codeHumeur'] = 1; // code de l'humeur modifier par l'utilisateur
        $_POST['contexte'] = "Contexte entrez par l'utilisateur";
        // WHEN Il valide la modification de son humeur
        $view = $this->donneesController->updateHumeur($this->pdo);
        // THEN L'humeur est modifier par le service et l'utilisateur est renvoyer sur la page ou il était.
        self::assertEquals(56,$view->getVar('noPage'));
    }

    public function testChangementPageDernierePage() {
        // GIVEN Un utilisateur sur une page des humeurs.
        $_SESSION['util'] = 1; // Identifiant de l'utilisateur
        $_POST['noPage'] = ">>";
        // WHEN Il clique sur '>>'.
        $view = $this->donneesController->updateHumeur($this->pdo);
        // THEN Il est amener sur la dernières pages des humeurs
        self::assertEquals(0,$view->getVar('noPage'));
    }

    public function testChangementPagePremierePage() {
        // GIVEN Un utilisateur sur une page des humeurs.
        $_SESSION['util'] = 1; // Identifiant de l'utilisateur
        $_POST['noPage'] = "<<";
        // WHEN Il clique sur '<<'.
        $view = $this->donneesController->updateHumeur($this->pdo);
        // THEN Il est amener sur la premières pages des humeurs
        self::assertEquals(1,$view->getVar('noPage'));
    }

    public function testInsertHumeur() {
        // GIVEN Un utilisateur sur la page n°56 des humeurs pour ajouter une humeur.
        $_POST['noPage'] = 56;
        $_POST['dateHumeur'] = date('Y-m-d');
        $_POST['heure'] = date('H');
        $_POST['humeur'] = 22;
        $_SESSION['util'] = 1;
        $_POST['contexte'] = "Contexte saisie par l'utilisateur";
        // WHEN Il valide la saisie de son humeur
        $view = $this->donneesController->insertHumeur($this->pdo);
        // THEN l'humeur est ajouter par le services et il est ramener sur la page ou il était
        assertEquals(56,$view->getVar('noPage'));

    }
}
