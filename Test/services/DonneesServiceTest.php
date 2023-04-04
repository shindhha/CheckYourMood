<?php

namespace services;
require_once 'yasmf/datasource.php';
require_once 'services/DonneesService.php';
require_once 'Test/DataBase.php';
require_once 'modeles/User.php';
use DataBase;
use Modeles\QueryBuilder;
use Modeles\User;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;

class DonneesServiceTest extends TestCase
{
    private $pdo;
    private $services;
    private $idUserTest = 1;

    protected function setUp(): void
    {
        $this->services = DonneesService::getDefaultDonneesService();
        $this->pdo = DataBase::getPDOTest();
        $this->pdo->beginTransaction();
        QueryBuilder::setDBSource($this->pdo);
    }

    protected function tearDown(): void
    {
        $this->pdo->rollBack();
    }


    public function testsUpdateDataSucces()
    {

        // GIVEN Un utilisateur ayant saisie un nouveau mot de passe valide
        $user = new User($this->idUserTest);
        $user->motDePasse = md5('TestMotDePasseModifier');

        // WHEN Il valide la modification de son mot de passe
        $user->save();
        // THEN Son mot de passe et mis a jour dans la base de données après avoir été cryptées en md5
        $motDePasseModifier = $this->pdo->query("SELECT motDePasse FROM utilisateur WHERE identifiant = 'idTest1'");
        $motDePasseModifier = $motDePasseModifier->fetch();
        assertEquals(md5('TestMotDePasseModifier'), $motDePasseModifier['motDePasse']);


        // GIVEN Un utilisateur ayant saisie un nouvel identifiant valide
        $user = new User($this->idUserTest);
        $user->identifiant = 'guillaume';
        // WHEN Il valide la modification de son identifiant
        $user->save();
        // THEN Son identifiant et mis à jour dans la base de données
        $identifiantModifier = $this->pdo->query("SELECT identifiant FROM utilisateur WHERE codeUtil = $this->idUserTest");
        $identifiantModifier = $identifiantModifier->fetch();
        assertEquals('guillaume', $identifiantModifier['identifiant']);

        // GIVEN Un utilisateur ayant saisie plusieur nouvelles informations valides a propos de son compte
        $user = new User($this->idUserTest);
        $user->identifiant = 'guillaume';
        $user->mail = 'guillaume.medard@iut-rodez.fr';
        $user->motDePasse = md5('TestMotDePasseModifier');
        // WHEN Il valide la modification de ses informations
        $user->save();

        // THEN L'enssemble de ces informations sont mis à jour dans la base de données.
        $infosModifier = $this->pdo->query("SELECT * FROM utilisateur WHERE codeUtil  = $this->idUserTest");
        $infosModifier = $infosModifier->fetch();
        assertEquals('guillaume', $infosModifier['identifiant']);
        assertEquals('guillaume.medard@iut-rodez.fr', $infosModifier['mail']);
        assertEquals(md5('TestMotDePasseModifier'), $infosModifier['motDePasse']);

    }

    public function testsUpdateFaillure()
    {
        // GIVEN Un utilisateur ayant entrer des nouvelles valeurs vides
        $user = new User($this->idUserTest);
        $user->nom = "";
        $user->prenom = "";
        $user->mail = "";

        // WHEN Il valide la modification de ces informations
        $this->expectException(\PDOException::class);
        $user->save();
        // THEN Les valeurs de la base de données ne sont pas modifier
        $infosParDefauts = $this->pdo->query("SELECT * FROM utilisateur WHERE codeUtil = $this->idUserTest");
        $infosParDefauts = $infosParDefauts->fetch();
        assertEquals('Blanchard', $infosParDefauts['nom']);
        assertEquals('Jules', $infosParDefauts['prenom']);
        assertEquals('jules.blanchard@iut-rodez.fr', $infosParDefauts['mail']);
    }

    public function testsDonneesUserSuccess()
    {
        // GIVEN Un utilisateur enregistré dans la base de données
        $user = new User($this->idUserTest);
        $exceptedUser = [
            'prenom' => 'prenomTest1',
            'nom' => 'nomTest1',
            'identifiant' => 'idTest1',
            'mail' => 'mail.test@test.test',
        ];
        $user->fetch("prenom","nom","identifiant","mail");
        // EXCEPTED On récupère un pdo statement qui contient ses données
        $this->assertEquals($exceptedUser,$user->toArray());

    }


    public function testNombreHumeur()
    {
        $idUtil = 1;
        // Appeler la fonction à tester
        $result = $this->services->nombreHumeur($this->pdo, $idUtil);
        // Assertion pour vérifier le résultat attendu
        $this->assertTrue($result ==11);
    }

    public function testUpdateMdp()
    {
        // GIVEN Un utilisateur ayant saisie un nouveau mot de passe valide
        $user = new User($this->idUserTest);
        $user->motDePasse = md5("root2022");
        // WHEN On valide l'enregistrement du nouveau mot de passe
        $user->save();
        // THEN Le mot de passe est crypté en md5 puis enregistrer dans la base de données
        $content = $this->pdo->query("SELECT motDePasse FROM utilisateur WHERE codeUtil = $this->idUserTest");
        $mdpModifier = $content->fetchColumn();

        $this->assertEquals(md5("root2022"), $mdpModifier);
    }

    public function testViewMoodsPagination()
    {
        /* TODO */
        // GIVEN Les humeurs enregistrés par un utilisateur ainsi que ces préférances d'affichage
        $idUtil = 1;
        $indicePremièreHumeur = 0;
        $nbHumeurParPage = 10;
        // Excepted On récupère ses humeurs selon ses préférances
        $result = $this->services->viewMoodsPagination($this->pdo, $idUtil, $indicePremièreHumeur, $nbHumeurParPage);
        $this->assertEquals($nbHumeurParPage, $result->rowCount());
    }

    public function testMdpSuccess()
    {
        // GIVEN Un utilisateur avec un mot de passe

        $idUtil = 1;
        $user = new User($idUtil);

        $mdpAttendu = "9aaf575815b9cc742b568924d5bc68a5";
        // Excepted On récupère son mot de passe
        $user->fetch('motDePasse');
        $this->assertEquals($mdpAttendu, $user->motDePasse);
    }
}
  

   

 