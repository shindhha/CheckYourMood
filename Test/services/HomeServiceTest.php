<?php

namespace services;
require_once 'services/HomeService.php';
require_once 'yasmf/datasource.php';
require_once 'Test/DataBase.php';
require_once 'modeles/QueryBuilder.php';

use DataBase;
use Modeles\QueryBuilder;
use PHPUnit\Framework\TestCase;
use PDOStatement;
class HomeServiceTest extends TestCase
{
    private $pdo;
    private $service;

    protected function setUp(): void
    {
        $this->pdo = DataBase::getPDOTest();
        $this->pdo->beginTransaction();
        $this->service = HomeService::getDefaultHomeService();
        QueryBuilder::setDBSource($this->pdo);
    }

    protected function tearDown(): void
    {
        $this->pdo->rollBack();
    }

    public function testConnexionSucces()
    {


        $idUtil = 'idTest1';
        $mdpUtil = 'TestMotDePasse';
        $expectedResult = [
            "nom" => "nomTest1",
            "prenom" => "prenomTest1",
            "mail" => "mail.test@test.test",
            "util" => 1

        ];
        $result = $this->service->connexion($this->pdo, $idUtil, $mdpUtil);

        $this->assertEquals($expectedResult, $result);
    }


}

