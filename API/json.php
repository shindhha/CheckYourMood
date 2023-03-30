<?php
	/**
	 * Fonctions permettant les envois sous format JSON
	 */

	/**
	 * Fonction qui permet d'envoyer des données et un code de retour
	 * @param $infos les informations
	 * @param $codeRetour le code de retour
	 */
	function sendJSON($infos, $codeRetour){
		header("Access-Control-Allow-Origin: *"); // Permet que tout le monde peut y acceder (toutes les IP)
		header("Content-Type: application/json; charset=UTF-8"); // Type de données envoyées de type JSON

		header("Access-Control-Allow-Methods: POST, GET");
			
		// header("Access-Control-Max-Age: 3600"); // Durée de la requete
		http_response_code($codeRetour);
		echo json_encode($infos,JSON_UNESCAPED_UNICODE);
		die();
	}
?>