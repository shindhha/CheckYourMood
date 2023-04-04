<?php
namespace controllers;
require_once 'services/DonneesService.php';
require_once 'services/HomeService.php';

use Modeles\QueryBuilder;
use services\HomeService;
use services\MoodService;
use services\DonneesService;
use yasmf\HttpHelper;
use yasmf\View;


class HomeController {

    private $homeService;
    private $donneesService;
    private $moodService;

    public function __construct($donneesService = null, $homeService = null, $moodService = null)
    {
        if ($donneesService == null){
            $this->donneesService = DonneesService::getDefaultDonneesService();
            $this->homeService = HomeService::getDefaultHomeService();
            $this->moodService = MoodService::getDefaultMoodService();
        } else {
            $this->donneesService = $donneesService;
            $this->homeService = $homeService;
            $this->moodService = $moodService;
        }

    }


    //Fonction de connection
    public function login($pdo){
        QueryBuilder::setDBSource($pdo);
        $id = htmlspecialchars(HttpHelper::getParam('identifiant'));
        $mdp = htmlspecialchars(HttpHelper::getParam('motdepasse'));
        $infos = $this->homeService->connexion($pdo,$id,$mdp);

        if($infos['util'] == 0){
            $view = new View("check-your-mood/views/connexion");
            return $view;
        }
		// Stockage dans la session //
        foreach($infos as $key => $value){
            $_SESSION["$key"] = $value;
        }

		$_SESSION['id'] = $infos["util"];
		$_SESSION['numeroSession']=session_id();// Stockage numéro de session pour éviter les piratages.

        //Libelle disponible
        $libelles = $this->moodService->libelles($pdo);

        // Nombre d'humeurs global
        $nbHumeur = $this->donneesService->nombreHumeur($pdo, $infos['util'])['nbHumeur'];

        // On détermine le nombre d'humeurs par page
        $parPage = 9;

        // On calcule le nombre de pages total
        $pages = ceil($nbHumeur / $parPage);

        // Page actuelle
        $currentPage = 1;

        // Calcul du 1er article de la page
        $premier = ($currentPage * $parPage) - $parPage;

        // On récupère les humeurs à afficher sur la page no 1
        $humeurs = $this->donneesService->viewMoodsPagination($pdo, $infos['util'], $premier, $parPage);

        //Création de la vue et set vraiable
        $view = new View("check-your-mood/views/humeurs");
        $view->setVar('humeurs',$humeurs);
        $view->setVar('libelles',$libelles);
        $view->setVar('updateOk',true);
        $view->setVar('pages',$pages);
        $view->setVar('noPage',$currentPage);
        return $view;
    }

    //goTo pour ce déplacer entre la page de connexion et celle d'inscription
    public function goTo(){
        $namepage = HttpHelper::getParam('namepage');
        $view = new View("check-your-mood/views/".$namepage);
        return $view;
    }

    public function index() {

        $view = new View("check-your-mood/views/accueil");
        return $view;
    }

}
