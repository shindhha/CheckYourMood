<?php
require_once 'services/homeservice.php';
require_once 'services/DonneesService.php';
require_once 'controllers/HomeController.php';
require_once 'services/MoodService.php';
require_once 'Test/DataBase.php';
use controllers\HomeController;
use Modeles\QueryBuilder;
use PHPUnit\Framework\TestCase;
use services\donneesservice;
use services\homeservice;
use services\moodservice;
use yasmf\DataSource;
use yasmf\View;


class HomeControllerTest extends TestCase
{
    private PDO $pdo;
    private homeservice $HomeService;
    private donneesservice $DonneesService;
    private moodservice $MoodService;
    private PDOStatement $PDOStatement;
    private HomeController $homeController;

    protected function setUp(): void
    {
        $this->pdo =  DataBase::getPDOTest();
        QueryBuilder::setDBSource($this->pdo);
        $this->HomeService = $this->createStub(HomeService::class);
        $this->DonneesService = $this->createStub(donneesservice::class);
        $this->PDOStatement = $this->createStub(PDOStatement::class);
        $this->MoodService = $this->createStub(moodservice::class);
        $this->homeController = new HomeController($this->DonneesService,$this->HomeService,$this->MoodService);
    }
    protected function tearDown(): void
    {
        unset($_POST);
        unset($_GET);
    }

    public function testIndex(){
        $view = $this->homeController->index();
        self::assertEquals($view->getRelativePath(), "check-your-mood/views/accueil");
    }
    public function testGoTo(){
        $_POST['namepage'] = 'humeurs';
        $expected = new View("check-your-mood/views/humeurs");
        $view = $this->homeController->goTo();
        self::assertEquals("check-your-mood/views/humeurs",$view->getRelativePath());
        self::assertEquals($expected,$view);

    }
    public function testGoTo2(){
        $_POST['namepage'] = 'connexion';
        $expected = new View("check-your-mood/views/connexion");
        $view = $this->homeController->goTo();
        self::assertEquals("check-your-mood/views/connexion",$view->getRelativePath());
        self::assertEquals($expected,$view);
    }
    public function testLoginfail(){
        $_POST['identifiant'] = 'TestControllers';
        $_POST['motdepasse'] = 'TestControllers';
        $expected = new View("check-your-mood/views/connexion");
        $this->HomeService->method('connexion')->willReturn(array('util' => 0));
        $view =  $this->homeController->login($this->pdo);
        self::assertEquals("check-your-mood/views/connexion",$view->getRelativePath());
        self::assertEquals($expected,$view);
    }

    public function testLoginsuccess(){
        $_POST['identifiant'] = 'jules22b';
        $_POST['motdepasse'] = 'root';
        $this->HomeService->method('connexion')->willReturn(array('util' => 1));
        $this->DonneesService->method('nombreHumeur')->willReturn(12);
        $this->PDOStatement->method('fetchColumn')->willReturn(1);
        $this->MoodService->method('libelles')->willReturn($this->PDOStatement);
        $view =  $this->homeController->login($this->pdo);
        self::assertEquals("check-your-mood/views/humeurs",$view->getRelativePath());
    }

}