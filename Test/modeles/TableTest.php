<?php

namespace modeles;
require_once 'modeles/Table.php';
require_once 'Test/Modeles/TableDeTest.php';
require_once 'Test/DataBase.php';
require_once 'modeles\QueryBuilder.php';

use InvalidArgumentException;
use PDOException;
use UnexpectedValueException;
use DataBase;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;
use Modeles\QueryBuilder;
class TableTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = DataBase::getPDOTest();
        $this->pdo->beginTransaction();
        QueryBuilder::setDBSource($this->pdo);
    }

    protected function tearDown(): void
    {
        $this->pdo->rollBack();
    }

    public function testFillOnValidData()
    {
        // GIVEN Une table avec les colonnes ['nomTest','dateTest']
        $tableTest = new TableDeTest();
        // WHEN On appele la methode fill avec des valeurs dont les clés sont reconnues
        $tableTest->fill(['nomTest' => 'Fill sur des valeurs valides']);
        // THEN La table enregistre les valeurs sous forme d'attributs.
        assertEquals('Fill sur des valeurs valides', $tableTest->nomTest);
    }

    public function testFillOnUnidentifiedData()
    {
        // GIVEN Une table avec les colonnes ['nomTest','dateTest']
        $tableTest = new TableDeTest();
        // WHEN On appele la methode fill avec des valeurs dont les clés ne sont pas reconnues
        $tableTest->fill(['age' => '12']);
        // THEN Les valeurs ne sont pas enregistrer.
        self::assertNull($tableTest->age);
    }

    public function testFill2TimeNullValues()
    {
        // GIVEN Une table avec les colonnes ['nomTest','dateTest'] avec des valeurs non nulle.
        $tableTest = new TableDeTest();
        $tableTest->fill(['nomTest' => 'Fill sur des valeurs valides']);
        // WHEN On appele la methode fill avec un enssemble vide
        $tableTest->fill([]);
        // THEN Les attributs sont inchanger
        assertEquals('Fill sur des valeurs valides', $tableTest->nomTest);
    }

    public function testFill2Time()
    {
        // GIVEN Une table avec les colonnes ['nomTest','dateTest'] avec des valeurs non nulle.
        $tableTest = new TableDeTest();
        $tableTest->fill(['nomTest' => 'Fill sur des valeurs valides']);
        // WHEN On appele la methode fill avec un enssemble vide
        $tableTest->fill(['nomTest' => 'Fill sur des valeurs déjà initialiser']);
        // THEN Les attributs ont leur nouvelle valeur.
        assertEquals("Fill sur des valeurs déjà initialiser", $tableTest->nomTest);
    }

    public function testFillWithouOverride2TimeNullValues()
    {
        // GIVEN Une table avec les colonnes ['nomTest','dateTest'] avec des valeurs non nulle.
        $tableTest = new TableDeTest();
        $tableTest->fill(['nomTest' => 'Fill sur des valeurs valides']);
        // WHEN On appele la methode fill avec un enssemble vide
        $tableTest->fillWithoutOverride([]);
        // THEN Les attributs sont inchangé.
        assertEquals("Fill sur des valeurs valides", $tableTest->nomTest);
    }

    public function testFillWithouOverride2Time()
    {
        // GIVEN Une table avec les colonnes ['nomTest','dateTest'] avec des valeurs non nulle.
        $tableTest = new TableDeTest();
        $tableTest->fill(['nomTest' => 'Fill sur des valeurs valides']);
        // WHEN On appele la methode fill avec un enssemble vide
        $tableTest->fillWithoutOverride(['nomTest' => 'FillWithoutOverride sur des valeurs déjà initialiser']);
        // THEN Les attributs sont inchangé.
        assertEquals("Fill sur des valeurs valides", $tableTest->nomTest);
    }

    public function testToArray()
    {
        // GIVEN Une table avec les colonnes ['nomTest','dateTest'] ainsi que de multiples attributs
        $tableTest = new TableDeTest();
        $tableTest->fill(['nomTest' => 'Test toArray']);                   // Valeur valide
        $description = "Test de la transformation d'un objet en tableau";
        $tableTest->description = $description;                            // Valeur valide
        $tableTest->fillWithoutOverride(['test' => 'test']);               // Valeur invalide
        $tableTest->age = 12;                                              // Valeur invalide
        // EXCEPTED La fonction toArray() ne renvoie que les attributs
        // dont la clé est stocker dans 'fillable' sous la forme d'un tableau 'cle' => 'valeur'
        assertEquals(['nomTest' => 'Test toArray', 'description' => $description], $tableTest->toArray());
    }

    public function testFillWithAttributId()
    {
        // GIVEN Un objet TableDeTest()
        $tableTest = new TableDeTest();
        $this->expectException(\UnexpectedValueException::class);
        $tableTest->fill(['tableIdWithComplexeNameToPreventFromUnExceptedAssignmentByMethodFill' => 'test']);
    }

    public function testsaveOnNoExist()
    {
        // GIVEN Un utilisateur n'ayant pas encore été enregistrer dans la base de données avec des données pré enregistrer
        $test = new TableDeTest();
        $test->fill([
            "nomTest" => "test",
            "description" => "test",
            "id" => 2
        ]);
        // WHEN On appelle la méthode save
        try {
            $test->save();
            assertTrue(true);
        } catch (PDOException $e) {
            assertTrue(false);
        }

        // THEN L'utilisateur est enregistrer dans la base de données
        $insertedTest = $this->pdo->query("SELECT nomTest,description,id FROM tests WHERE id = 2");
        $tab = $insertedTest->fetch();
        assertEquals($test->toArray(), $tab);
    }


    public function testSaveOnExist()
    {
        // GIVEN Un utilisateur ayant déjà été enregistrer dans la base de données dans un état.
        // Et avec des attributs d'objet différent.
        $test = new TableDeTest(1);
        $test->nomTest = 'testSaveOnExist';
        // WHEN On appelle la méthode save
        try {
            $test->save();
        } catch (\PDOException $e) {
        }
        // THEN Les données de l'utilisateur sont mises a jour.
        $insertedTest = $this->pdo->query("SELECT nomTest FROM tests WHERE nomTest = 'testSaveOnExist'");
        assertEquals($test->toArray(), $insertedTest->fetch());
    }

    public function testFetchIdEqualTo0() {
        // GIVEN Un objet avec une id égale a 0
        $test = new TableDeTest();
        // EXCEPTED La méthode fetch déclenche une exception
        $this->expectException(UnexpectedValueException::class);
        $test->fetch();
    }

    public function testConstructorWithIdInferiorTo0() {
        $this->expectException(InvalidArgumentException::class);
        $test = new TableDeTest(-1);
    }

}