<?php
namespace controllers;
require_once 'modeles/QueryBuilder.php';
require_once 'modeles/Humeur.php';
require_once 'services/DonneesService.php';
require_once 'services/VisualisationService.php';
use Modeles\Humeur;
use Modeles\QueryBuilder;
use Modeles\User;
use yasmf\HttpHelper;
use yasmf\View;
use services\VisualisationService;
use services\MoodService;
use services\DonneesService;

class DonneesController {

    private DonneesService $donneesService;
    private MoodService $moodService;
    private VisualisationService $visualisationService;

    public function __construct($donneesService = null, $moodService = null, $visualisationService = null)
    {
        if($donneesService == null){
            $this->donneesService = DonneesService::getDefaultDonneesService();
            $this->moodService = MoodService::getDefaultMoodService();
            $this->visualisationService = VisualisationService::getDefaultVisualisationService();
        }else{
            $this->donneesService = $donneesService;
            $this->moodService = $moodService;
            $this->visualisationService = $visualisationService;
        }

    }


    public function goToMood($pdo){
        $currentDay = date("Y-m-d");
        $currentWeek = date("W");
        $anneeActuelle = date("Y");
        $idUtil = $_SESSION['util'];

        $code = (int) HttpHelper::getParam('humeur') ?: 1;
        $anneeAComparer = (int) HttpHelper::getParam('anneeChoisi') ?: 2023;
        $typeDeRpresentation = (int) HttpHelper::getParam('typeDeRpresentation') ?: 1;
        $codeDigrammeBatton = (int) HttpHelper::getParam('humeurDigrammeBatton') ?: 1;
        $anneeComparaison = (int) htmlspecialchars(HttpHelper::getParam('anneeAComparer')) ?: $anneeActuelle;
        $namepage = htmlspecialchars(HttpHelper::getParam('namepage'));
        $weekGeneral = (int) htmlspecialchars(HttpHelper::getParam('weekGeneral')) ?: $currentWeek;
        $dateDonught = htmlspecialchars(HttpHelper::getParam('dateChoisiDonught')) ?: $currentDay;
        $tabAnneeActuelle = $this->visualisationService->visualisationHumeurAnnee($pdo, $idUtil, $anneeAComparer,$codeDigrammeBatton);
        $tabAnneeComparaison = $this->visualisationService->visualisationHumeurAnnee($pdo, $idUtil, $anneeComparaison,$codeDigrammeBatton);
		$anneHumeurMax = (int) htmlspecialchars(HttpHelper::getParam('anneeAComparerHumeur')) ?: $anneeActuelle;
        $humeursRadar = $this->moodService->viewMoods($pdo,$_SESSION['util']);
        $libellesRadar = $this->moodService->libelles($pdo);
        $libellesTableau = $libellesRadar;
        $visualisationRadar = $this->visualisationService->visualisationRadar($pdo, $idUtil, $code, $weekGeneral, $anneeAComparer);
        $visualisationTableau = $this->visualisationService->visualisationTableau($pdo, $idUtil, $weekGeneral, $anneeAComparer);
        $visualisationDonught = $this->visualisationService->visualisationDoughnut($pdo, $idUtil, $dateDonught);
        $tableauLibelleDonught[] = [];
        $tableauCountDonught[] = [];
        foreach($visualisationDonught as $key => $value){
            $tableauLibelleDonught[] = $key; 
            $tableauCountDonught[] = $value; 
        }
        $humeursLaPlusFrequente = $this->visualisationService->visualisationHumeurSemaine($pdo, $idUtil, $weekGeneral);
		$humeursLaPlusFrequenteAnnee = $this->visualisationService->visualisationHumeurAnneeLaPlus($pdo, $idUtil, $anneeAComparer);
		$humeursLaPlusFrequenteJour = $this->visualisationService->visualisationHumeurJour($pdo, $idUtil, $dateDonught);
        $view = new View("check-your-mood/views/".$namepage);
        $view->setVar('humeursRadar',$humeursRadar);
        $view->setVar('libellesRadar',$libellesRadar);
        $view->setVar('libellesTableau',$libellesTableau);
        $view->setVar('currentWeek',$currentWeek);
        $view->setVar('visualisationRadar',$visualisationRadar);
        $view->setVar('visualisationTableau',$visualisationTableau);
        $view->setVar('visualisationDonught',$visualisationDonught);
        $view->setVar('humeursLaPlusFrequente',$humeursLaPlusFrequente);
		$view->setVar('humeursLaPlusFrequenteAnnee',$humeursLaPlusFrequenteAnnee);
        $view->setVar('dataPoints2',$tabAnneeComparaison);
        $view->setVar('dataPoints1',$tabAnneeActuelle);
        $view->setVar('anneeActuelle',$anneeActuelle);
		$view->setVar('currentDay',$currentDay);
        $view->setVar('anneeComparaison',$anneeComparaison);
        $view->setVar('anneeChoisi',$anneeAComparer);
        $view->setVar('weekGeneral',$weekGeneral);
        $view->setVar('typeDeRpresentation',$typeDeRpresentation);
		$view->setVar('codeDigrammeBatton',$codeDigrammeBatton);
		$view->setVar('dateDonught',$dateDonught);
        $view->setVar('tableauLibelleDonught',$tableauLibelleDonught);
        $view->setVar('tableauCountDonught',$tableauCountDonught);
		$view->setVar('humeursLaPlusFrequenteJour',$humeursLaPlusFrequenteJour);
        return $view;
    }
	
	/**
	 * Permet la modification du contexte d'une humeur
	 */
	public function updateHumeur($pdo) {
        QueryBuilder::setDBSource($pdo);
        $tab['contexte'] = htmlspecialchars(HttpHelper::getParam('contexte'));
        $tab['codeHumeur'] = (int) htmlspecialchars(HttpHelper::getParam('codeHumeur'));
        $tab['id'] = $_SESSION['util'];
		$humeur = new Humeur($tab['codeHumeur']);
        $humeur->contexte = $tab['contexte'];

        try {
            $humeur->save();
        } catch (\PDOException $e) {
        }
        $humeurs = $this->moodService->viewMoods($pdo,$_SESSION['util']);
        $libelles = $this->moodService->libelles($pdo);
		
		return $this->changementPage($pdo);
    }

    //Insertion d'une humeur
    public function insertHumeur($pdo){
        QueryBuilder::setDBSource($pdo);
        $code = (int) HttpHelper::getParam('humeur');
        $date  = HttpHelper::getParam('dateHumeur');
        $heure  = HttpHelper::getParam('heure');
        $contexte = htmlspecialchars(HttpHelper::getParam('contexte'));
        $util = $_SESSION['util'];
        $humeur = new Humeur();
        $humeur->libelle = $code;
        $humeur->dateHumeur = $date;
        $humeur->heure = $heure;
        $humeur->idUtil = $util;
        $humeur->contexte = $contexte;
        try {
            $humeur->save();
        } catch (\PDOException $e) {
        }

        $humeurs = $this->moodService->viewMoods($pdo,$util);
        $libelles = $this->moodService->libelles($pdo);
        /* pour ne pas que quand on actualise la page, la requête se re éxécute et remette une nouvelle humeur */
        $noPage = HttpHelper::getParam('noPage');
        unset($_POST);
        unset($_GET);
        $_POST['noPage'] = $noPage;
        return $this->changementPage($pdo);
    }

    /**
     * Permet la pagination de la page de visualisation des humeurs
     * @param $pdo 
     * @return View 
     */
    public function changementPage($pdo)
    {
        $libelles = $this->moodService->libelles($pdo);
        $nbHumeurGlobale = $this->donneesService->nombreHumeur($pdo, $_SESSION['util']);
        $nbHumeurParPage = 9;
        $nbTotalPages = ceil($nbHumeurGlobale / $nbHumeurParPage);

        $currentPage = HttpHelper::getParam('noPage') ?: 1;

        if ($currentPage == "<<") {
            $currentPage = 1;
        } else if ($currentPage == ">>") {
            $currentPage = $nbTotalPages;
        }
        
        // Calcul du 1er article de la page
        $premier = ($currentPage * $nbHumeurParPage) - $nbHumeurParPage;

        // On récupère les humeurs à afficher sur la page no 1
        $humeurs = $this->donneesService->viewMoodsPagination($pdo, $_SESSION['util'], $premier, $nbHumeurParPage);

        //Création de la vue et set vraiable
        $view = new View("check-your-mood/views/humeurs");
        $view->setVar('humeurs',$humeurs);
        $view->setVar('libelles',$libelles);
        $view->setVar('pages',$nbTotalPages);
        $view->setVar('noPage',$currentPage);
        return $view;
    }

    // Modification des données personnelles
    public function updateData($pdo){
        QueryBuilder::setDBSource($pdo);
        $tab['identifiant'] = htmlspecialchars(HttpHelper::getParam('identifiant'));
        $tab['nom'] = htmlspecialchars(HttpHelper::getParam('nom'));
        $tab['prenom'] = htmlspecialchars(HttpHelper::getParam('prenom'));
        $tab['mail'] = htmlspecialchars(HttpHelper::getParam('mail'));
        $util = $_SESSION['util'];
        $user = new User($util);
        $user->fill($tab);
        try {
            $user->save();
            $modificationInformationOk = true;
        } catch (\PDOException $e) {
            $modificationInformationOk = false;
        }
        $view = $this->viewModification($pdo);
        // Modification des Informations (hors mot de passe) 
        $view->setVar('tentativeModificationInformation',true);
        $view->setVar('modificationInformationOk',$modificationInformationOk);

		return $view;
    }
    /**
     * 
     * @param $pdo 
     * @return View 
     */
    public function viewModification($pdo) {
        QueryBuilder::setDBSource($pdo);
        $view = new View("check-your-mood/views/modification");

        $user = new User($_SESSION['util']);
        $user->fetch("prenom", "nom", "identifiant", "mail");

        //Création de la vue et set vraiable
        // Informations de l'utilisateur
        $view->setVar('prenom',$user->prenom);
        $view->setVar('nom',$user->nom);
        $view->setVar('identifiant',$user->identifiant);
        $view->setVar('courriel',$user->mail);
        // Modification des Informations (hors mot de passe) 
        $view->setVar('tentativeModificationInformation',false);
        $view->setVar('modificationInformationOk',false);
        // Modification du mot de passe
        $view->setVar('tentativeModificationMDP',false);
        $view->setVar('mdpOk','');
        $view->setVar('mdpNouveauOk','');
        $view->setVar('modificationMDPOk',false);
        return $view;
    }
        
    /**
     * Permet la modification du mdp
     * @param $pdo 
     * @return View 
     */
    public function updateMDP($pdo) {

        $MDP = htmlspecialchars(HttpHelper::getParam('ancienMDP'));
        $nouveauMDP = htmlspecialchars(HttpHelper::getParam('nouveauMDP'));
        $confirmationNouveauMDP = htmlspecialchars(HttpHelper::getParam('confirmationNouveauMDP'));


        $user = new User($_SESSION['util']);
        $user->fetch("motDePasse");
        /* Controle que le mot de passe est bon */
        if ($user->motDePasse == md5($MDP)) {
            $mdpOk = true;
            /* Controle que le nouveau mot est valide */
            if (strlen($nouveauMDP) != 0 && $nouveauMDP == $confirmationNouveauMDP) {
                $user->motDePasse = md5($nouveauMDP);
                try {
                    $user->save();
                } catch (\PDOException $e) {
                }
                $mdpNouveauOk = true;
                $modificationMDPOk = true;
            } else {
                $mdpNouveauOk = false;
                $modificationMDPOk = false;
            }
        } else {
            // Mauvais mot de passe
            $mdpOk = false;
            $modificationMDPOk = false;
            $mdpNouveauOk = false;
        }
        //Création de la vue et set vraiable
        $view = $this->viewModification($pdo);
        $view->setVar('tentativeModificationMDP',true);
        $view->setVar('mdpOk',$mdpOk);
        $view->setVar('modificationMDPOk',$modificationMDPOk);
        $view->setVar('mdpNouveauOk',$mdpNouveauOk);
        return $view;
    }
	
	
	public function supprimerCompte($pdo) {
		
		$this->donneesService->supprimerCompte($pdo, $_SESSION['id']);
		$view = new View("check-your-mood/views/connexion");
		$view->setVar("errData",true);
		return $view ; 

	}

}
