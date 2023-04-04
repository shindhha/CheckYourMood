<?php

namespace modeles;
require_once 'yasmf/datasource.php';
require_once 'Test/DataBase.php';
require_once 'modeles/Table.php';
require_once 'modeles/Humeur.php';
require_once 'modeles/QueryBuilder.php';

use DataBase;
use PHPUnit\Framework\TestCase;
use services\Mood;
use function PHPUnit\Framework\assertEquals;
use Modeles\Humeur;
use Modeles\QueryBuilder;

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

}