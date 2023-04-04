<?php

namespace modeles;
use DataBase;
use Models\Table;
use Models\User;
use PHPUnit\Framework\TestCase;
use services\Mood;
use function PHPUnit\Framework\assertEquals;
require_once 'Test/DataBase.php';
require_once 'modeles/Table.php';
require_once 'modeles/QueryBuilder.php';
require_once 'yasmf/datasource.php';
use UnexpectedValueException;
class QueryBuilderTest extends TestCase
{
    private $pdo;
    private $services;

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


    public function testUpdate1Argument()
    {
        // GIVEN Un objet QueryBuilder sur la table 'utilisateur' sur lequel
        // on demande de construire une requete de type UPDATE
        $query = QueryBuilder::Table('utilisateur')
            ->update(['nom' => 'Medard']);
        // EXCEPTED On obtient la requete attendue
        $qStmt = $query->getQuery();
        assertEquals("UPDATE utilisateur SET nom = :nom", $qStmt);
    }

    public function testUpdateMultipleArgument()
    {
        // GIVEN Un objet QueryBuilder sur la table 'utilisateur' sur lequel
        // on demande de construire une requete de type UPDATE
        $query = QueryBuilder::Table('utilisateur')
            ->update([
                'nom' => 'Medard',
                'prenom' => 'Guillaume',
                'identifiant' => 'guigui'
            ]);
        // EXCEPTED On obtient la requete attendue
        assertEquals("UPDATE utilisateur SET nom = :nom,prenom = :prenom,identifiant = :identifiant", $query->getQuery());
    }

    public function testInsert()
    {
        // GIVEN Un objet QureyBuilder sur la table 'utilisateur' sur lequel
        // on demande de construire une requete de type INSERT
        $query = QueryBuilder::Table('utilisateur')->insert(['nom' => 'Medard']);
        // EXCEPTED On obtient la requete attendue
        $qStmt = $query->getQuery();
        assertEquals("INSERT INTO utilisateur (nom) VALUES (:nom)", $qStmt);
    }


    public function testSelectWithNoArgument()
    {
        // GIVEN Un objet QueryBuilder sur la table 'utilisateur' sans methode select appeler
        $queryWithoutSelectMethod = QueryBuilder::Table('utilisateur')->getQuery();
        // et un objet QueryBuilder sur la table 'utilisateur' avec la methode select sans argument.
        $queryWithSelectWithoutArguments = QueryBuilder::Table('utilisateur')->select()->getQuery();
        // EXCEPTED Les requetes des deux objet sont égales
        assertEquals($queryWithSelectWithoutArguments, $queryWithoutSelectMethod);
        // Et correspondent a la requete attendu
        assertEquals("SELECT * FROM utilisateur", $queryWithoutSelectMethod);
    }

    public function testSelectWithArguments()
    {
        // GIVEN Un objet QueryBuilder sur la table 'utilisateur' en ayant spécifier
        // plusieurs colonnes avec la méthode select
        $query = QueryBuilder::Table('utilisateur')->select('nom', 'prenom', 'pseudo');
        // WHEN  On appeles la méthode getQuery
        $qStmt = $query->getQuery();
        // THEN  On obtient la requête sql correspondante
        assertEquals("SELECT nom,prenom,pseudo FROM utilisateur", $qStmt);
    }

    public function testWhere()
    {
        // GIVEN Un objet QueryBuilder sur lequel on appele la méthode where
        $query = QueryBuilder::Table('utilisateur')->where("prenom", "Guillaume");
        // WHEN  on appeles la méthode getParams
        $params = $query->getParams();
        // THEN  on récupère les paramètres précédement donner
        assertEquals(['prenom' => 'Guillaume'], $params);
        // Et on obtient la requete correspondante
        assertEquals("SELECT * FROM utilisateur WHERE prenom = :prenom", $query->getQuery());
    }

    public function testWhereUpdate()
    {
        // GIVEN Un objet QueryBuilder sur lequel on appele la méthode where puis la methode update
        $query = QueryBuilder::Table('utilisateur')
            ->where("prenom", "Guillaume")->update(['nom' => 'Medard']);
        // WHEN  on appeles la méthode getParams
        $params = $query->getParams();
        // THEN  on récupère les paramètres précédement donner
        assertEquals(['prenom' => 'Guillaume', 'nom' => 'Medard'], $params);
        // Et on obtient la requete correspondante
        assertEquals("UPDATE utilisateur SET nom = :nom WHERE prenom = :prenom", $query->getQuery());
    }

    // TODO Corriger le QueryBuilder
    public function test2WhereWithSameName()
    {
        // GIVEN Un objet QueryBuilder sur lequel on appele
        // la méthode where deux fois en spécifiant la même colonne
        $query = QueryBuilder::Table('utilisateur')
            ->where("prenom", "Guillaume")
            ->where("prenom", "Clement");
        // WHEN  on appeles la méthode getParams
        $params = $query->getParams();
        // THEN  on récupère les paramètres précédement donner
        assertEquals(['prenom' => 'Guillaume', 'prenom' => 'Clement'], $params);
        // Et on obtient la requete correspondante en différentient les clés
        assertEquals("SELECT * FROM utilisateur WHERE prenom = :prenom1 AND prenom = :prenom2", $query->getQuery());
    }

    public function testInsertExecute()
    {
        // GIVEN Un objet QureyBuilder sur la table 'utilisateur' sur lequel
        $tab = [
            'nom' => 'nomInsert',
            'prenom' => 'nomInsert',
            'identifiant' => 'nomInsert',
            'mail' => 'nomInsert@nomInsert.nomInsert',
            'motDePasse' => md5('nomInsert')
        ];
        // On demande de construire une requete de type INSERT
        $stmt = QueryBuilder::Table('utilisateur')
            ->insert($tab)->execute();
        /* EXCEPTED On obtient la requete attendue */
        $result = $this->pdo->query("SELECT nom,prenom,identifiant,mail,motDePasse FROM utilisateur where identifiant = 'nomInsert'" );
        assertEquals($result->fetch(), $tab);
    }

    public function testPdoNotSetUp() {
        // GIVEN La connexion a la base de données n'est pas donné
        QueryBuilder::setDBSource(null);
        // EXCEPTED Une exception est levée au moment ou on tente d'éxécuter la requete
        $this->expectException(UnexpectedValueException::class);
        QueryBuilder::Table('utilisateur')->execute();

    }
}