<?php

namespace modeles;
require_once 'yasmf/datasource.php';

use DataBase;
use PHPUnit\Framework\TestCase;
use services\Mood;
use function PHPUnit\Framework\assertEquals;
use modeles\Humeur;
require_once 'Test/DataBase.php';
require_once 'modeles/Table.php';
require_once 'modeles/Humeur.php';
require_once 'modeles/QueryBuilder.php';
class HumeurTest extends TestCase
{
    private $pdo;
    protected function setUp(): void
    {
        $this->pdo = DataBase::getPDOTest();
        $this->pdo->beginTransaction();
        QueryBuilder::setDBSource($this->pdo);
        date_default_timezone_set('Europe/Paris');
    }

    protected function tearDown(): void
    {
        $this->pdo->rollBack();
    }

    public function testUpdateHumeurSuccess()
    {
        $humeur = new Humeur(1);
        $tab = [
            'libelle' => '12',
            'idUtil' => 1,
            'contexte' => "Happy",
            'heure' => date('H:i:s'),
            'dateHumeur' => date("Y-m-d")
        ];
        // WHEN On valide les changements
        $humeur->fill($tab);
        $humeur->save();
        // THEN La nouvelle description est enregistrer dans la base de données
        $humeurModifier = $this->pdo->query("SELECT contexte FROM humeur WHERE codeHumeur = 1");
        $humeurModifier = $humeurModifier->fetchColumn(0);
        assertEquals($tab['contexte'], $humeurModifier);
    }

    public function testUpdateHumeurFailureHeure()
    {
        // GIVEN Une humeur dont on veut modifier les informations par les suivantes
        $humeur = new Humeur(56);
        $tab = [
            'libelle' => '12',
            'idUtil' => 1,
            'contexte' => "Happy",
            'heure' => date('H:i:s', strtotime('+1 hour')),     // Heure invalide, supérieure à l'heure actuelle
            'dateHumeur' => date("Y-m-d")
        ];
        $humeur->fill($tab);
        assertEquals("dqs",$tab['heure']);
        $this->expectException(\PDOException::class);
        // EXCEPTED Une erreur est transmise par le trigger de la base de données
        $humeur->save();

    }
    public function testUpdateHumeurFailureDate()
    {
        // GIVEN Une humeur dont on veut modifier les informations par les suivantes
        $humeur = new \Modeles\Humeur(56);
        $tab = [
            'libelle' => '12',
            'idUtil' => 1,
            'contexte' => "Happy",
            'heure' => date("H:i:s"),
            'dateHumeur' => date('Y-m-d', strtotime('+1 day')) // Jour invalide , supérieur au jour actuel
        ];
        $humeur->fill($tab);
        // EXCEPTED Une erreur est transmise par le trigger de la base de données
        $this->expectException(\PDOException::class);
        $humeur->save();

    }

    public function testUpdateHumeurFailureLibelleDontExist()
    {
        // GIVEN Une humeur dont on veut modifier les informations par les suivantes
        $humeur = new \Modeles\Humeur(56);
        $tab = [
            'libelle' => '999', // Libelle n'existant pas
            'idUtil' => 1,
            'contexte' => "Happy",
            'heure' => date("H:i:s"),
            'dateHumeur' => date("Y-m-d")
        ];
        $humeur->fill($tab);
        // EXCEPTED Une erreur est transmise par le trigger de la base de données
        $this->expectException(\PDOException::class);
        $humeur->save();

    }

    public function testInsertMoodSuccess()
    {
        $humeur = new \Modeles\Humeur();
        $data = [
            "libelle" => 22,
            "dateHumeur" => date("Y-m-d"),
            "heure" => date("H:i:s"),
            "idUtil" => 10,
            "contexte" => "Test success"
        ];
        $humeur->fill($data);
        $humeur->save();
        $insertedHumeur = $this->pdo->query("SELECT libelle,dateHumeur,heure,idUtil,contexte from humeur where libelle = 22 AND idUtil = 10")->fetch();
        self::assertEquals($insertedHumeur,$data);

    }

    public function testInsertMoodFailed()
    {
        $heure = new Humeur();
        $data = [
            "libelle" => 22,
            "dateHumeur" => date('Y-m-d', strtotime('+1 day')), // Jour invalide , supérieur au jour actuel
            "heure" => date("H:i:s"),
            "idUtil" => 10,
            "contexte" => "Test success"
        ];
        $heure->fill($data);
        $this->expectException(\PDOException::class);
        $heure->save();

    }
    public function testInsertMoodFailedHour()
    {
        $heure = new \Modeles\Humeur();
        $data = [
            "libelle" => 22,
            "dateHumeur" => date('Y-m-d'), // Jour invalide , supérieur au jour actuel
            "heure" => date('H:i:s', strtotime('+1 hour')),
            "idUtil" => 10,
            "contexte" => "Test success"
        ];
        $heure->fill($data);
        $this->expectException(\PDOException::class);
        $heure->save();

    }
}