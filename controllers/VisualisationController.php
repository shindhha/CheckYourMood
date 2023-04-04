<?php
namespace controllers;

use yasmf\HttpHelper;
use yasmf\View;
use services\VisualisationService;
use services\MoodService;


class VisualisationController {

    private VisualisationService $visualisationService;
    private MoodService $moodService;
    
    public function __construct($moodService = null, $visualisationService = null)
    {
        if ($moodService === null) {
            $this->visualisationService = VisualisationService::getDefaultVisualisationService();
            $this->moodService = MoodService:: getDefaultMoodService();
        } else {
            $this->visualisationService = $visualisationService;
            $this->moodService = $moodService;
        }
    }

    public function countMoodByDay($pdo){
        $code = (int) HttpHelper::getParam('humeur');
        $idUtil = $_SESSION['util'];

        $humeurs = $this->moodService->viewMoods($pdo,$_SESSION['util']);
        $libelles = $this->moodService->libelles($pdo);

        $nbr = $this->visualisationService->visualisationHumeurJour($pdo, $idUtil, $code)->fetch();

        $view = new View("check-your-mood/views/visualisation");
        $view->setVar('humeurs',$humeurs);
        $view->setVar('libelles',$libelles);
        $view->setVar('nbrHumeurs',$nbr);
        return $view;
    }
}