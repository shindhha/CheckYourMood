<?php

namespace controllers;
require_once 'Test/DataBase.php';
require_once 'services/MoodService.php';
require_once 'services/VisualisationService.php';
require_once 'controllers/VisualisationController.php';
use PHPUnit\Framework\TestCase;
use services\MoodService;
use services\VisualisationService;
use function PHPUnit\Framework\assertEquals;
use DataBase;
class VisualisationControllerTest extends TestCase
{
    private $pdo;
    private MoodService $moodService;
    private VisualisationService $visualisationService;
    private VisualisationController $visualisationController;

    protected function setUp(): void
    {
        $this->pdo = DataBase::getPDOTest();
        $this->pdo->beginTransaction();
        $this->moodService = $this->createStub(MoodService::class);
        $this->visualisationService = $this->createStub(VisualisationService::class);
        $this->visualisationController = new \controllers\VisualisationController($this->moodService,$this->visualisationService);
    }

    protected function tearDown(): void
    {
        unset($_POST);
        unset($_GET);
        unset($_SESSION);
        $this->pdo->rollBack();
    }
    public function testcountMoodByDay() {
        // GIVEN Un utilisateur voulant s'avoir combien de fois il a entrer une même humeur dans la journée
        $_POST['humeur'] = 1;
        $_SESSION['util'] = 1;
        // WHEN Il valide sont action
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('fetch')->willReturn(22);
        $this->visualisationService->method('visualisationHumeurJour')->willReturn($stmt);
        $view = $this->visualisationController->countMoodByDay($this->pdo);
        // THEN La vue est retourner avec le nombre de fois ou il a entrer cette humeur
        assertEquals(22,$view->getVar('nbrHumeurs'));
    }

}
