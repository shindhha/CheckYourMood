<?php

namespace controllers;
require_once 'yasmf/datasource.php';
require_once 'services/DonneesService.php';
require_once 'Test/DataBase.php';
require_once 'services/MoodService.php';
require_once 'controllers/inscriptioncontroller.php';

use DataBase;
use Modeles;
use yasmf\View;
use PDOException;

class InscriptionControllerTest extends \PHPUnit\Framework\TestCase
{
    private $pdo;
    private $inscriptioncontroller;
    private $user;

    protected function setUp(): void
    {
        $this->pdo = DataBase::getPDOTest();
        $this->pdo->beginTransaction();
        $this->user = $this->createStub(Modeles\User::class);
        $this->inscriptioncontroller = new \controllers\InscriptionController($this->inscriptionService);
    }

    protected function tearDown(): void
    {
        unset($_POST);
        unset($_GET);
        $this->pdo->rollBack();
    }

    public function testInscriptionSuccess()
    {

        // GIVEN des valeurs entrer par l'utilisateur valides
        $_POST['identifiant'] = "test";
        $_POST['motdepasse'] = "test";
        $_POST['mail'] = "test";
        $_POST['nom'] = "test";
        $_POST['prenom'] = "test";
        // Et un objet user qui s'enregistre correctement dans la base de données.
        // WHEN On appelle la fonction siging du controller d'inscription
        $returnedView = $this->inscriptioncontroller->signin($this->pdo,$this->user);
        // THEN L'inscription est valider et on arrive sur la page de connexion
        $expectedView = new View("check-your-mood/views/connexion");
        self::assertEquals($expectedView, $returnedView);
    }

    public function testEmptyUsersValues()
    {
        // GIVEN des valeurs entrer par l'utilisateur valides sauf une vide
        $_POST['identifiant'] = "test";
        $_POST['motdepasse'] = "";
        $_POST['mail'] = "test";
        $_POST['nom'] = "test";
        $_POST['prenom'] = "test";
        // Et un objet user qui s'enregistre correctement dans la base de données.
        // WHEN On appelle la fonction siging du controller d'inscription
        $returnedView = $this->inscriptioncontroller->signin($this->pdo, $this->user);
        // THEN L'inscription est n'est pas lancer et on revien sur la page d'inscription
        $expectedView = new View("check-your-mood/views/inscription");
        self::assertEquals($expectedView, $returnedView);
    }

    public function testErrorFromService()
    {
        // GIVEN des valeurs entrer par l'utilisateur valides
        $_POST['identifiant'] = "test";
        $_POST['motdepasse'] = "test";
        $_POST['mail'] = "test";
        $_POST['nom'] = "test";
        $_POST['prenom'] = "test";
        // Et un objet user qui propage une exception en essayant de s'enregistrer dans la base de données.
        $this->user->method("save")->willThrowException(new PDOException());
        // WHEN On appelle la fonction siging du controller d'inscription
        $returnedView = $this->inscriptioncontroller->signin($this->pdo, $this->user);
        // THEN L'inscription est n'est pas lancer et on revien sur la page d'inscription
        $expectedView = new View("check-your-mood/views/inscription");
        self::assertEquals($expectedView, $returnedView);
    }


}