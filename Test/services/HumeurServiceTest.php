<?php

namespace services;

use DataBase;
use Modeles\Humeur;
use Modeles\QueryBuilder;
use PHPUnit\Framework\TestCase;

require_once 'yasmf/datasource.php';
require_once 'modeles/Humeur.php';
require_once 'modeles/Table.php';
require_once 'services/MoodService.php';

require_once 'Test/DataBase.php';
use PDOStatement;

class HumeurServiceTest extends TestCase
{
    private $pdo;
    private $services;

    protected function setUp(): void
    {
        $this->services = MoodService::getDefaultMoodService();
        $this->pdo = DataBase::getPDOTest();
        $this->pdo->beginTransaction();
        QueryBuilder::setDBSource($this->pdo);
    }

    protected function tearDown(): void
    {
        $this->pdo->rollBack();
    }

    public function testLibelles()
    {
        // Appeler la fonction à tester
        $result = $this->services->libelles($this->pdo);
        // Assertion
        $this->assertEquals($result->rowCount(), 27);
    }


    public function testViewMoods()
    {
        // Préparer les données de TestControllers
        $idUtil = 1;

        // Appeler la fonction à tester
        $result = $this->services->viewMoods($this->pdo, $idUtil);

        // Assertions
        $this->assertInstanceOf(PDOStatement::class, $result);
        $this->assertTrue($result->fetchAll() >= 1);
    }

    public function testViewMoodsWithNoMoods()
    {
        // Préparer les données de TestControllers

        $idUtil = 2;
        // Appeler la fonction à tester
        $result = $this->services->viewMoods($this->pdo, $idUtil);

        // Assertions
        $this->assertInstanceOf(PDOStatement::class, $result);
        $this->assertEmpty($result->fetchAll());
    }
}    