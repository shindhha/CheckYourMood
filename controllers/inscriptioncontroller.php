<?php
namespace controllers;
require_once 'modeles/User.php';
use Modeles\QueryBuilder;
use yasmf\HttpHelper;
use yasmf\HttpHelper\getParam;
use yasmf\View;
use Modeles\User;
class InscriptionController {

    public function signin($pdo,$userTest = null) {

        $user = $userTest === null ? new User() : $userTest;
        $view = new View("check-your-mood/views/inscription");
        $data['identifiant'] = htmlspecialchars(HttpHelper::getParam('identifiant'));
        $data['motDePasse'] = htmlspecialchars(HttpHelper::getParam('motdepasse'));
        $data['mail'] = htmlspecialchars(HttpHelper::getParam('mail'));
        $data['nom'] = htmlspecialchars(HttpHelper::getParam('nom'));
        $data['prenom'] = htmlspecialchars(HttpHelper::getParam('prenom'));

        if ($data['identifiant'] == "" || $data['motDePasse'] == ""
            || $data['mail'] == ""     || $data['nom'] == ""  || $data['prenom'] == "") {
            return $view;
        }
        $user->fill($data);
        $user->motDePasse = md5($data['motDePasse']);

        QueryBuilder::setDBSource($pdo);
        try {
            $user->save();
        } catch (\PDOException $e) {
            return $view;
        }

        $view = new View("check-your-mood/views/connexion");
        return $view;
    }



}
