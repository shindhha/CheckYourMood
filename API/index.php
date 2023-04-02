<?php 
	/**
	 * Switch qui permet de naviguer dans l'API et de faire les requetes
	 */

	require_once("json.php");
	require_once("donnees.php");
	require_once("authentification.php");

	$request_method = $_SERVER["REQUEST_METHOD"];  // GET / POST / DELETE / PUT
	switch($_SERVER["REQUEST_METHOD"]) {
		case "GET" :
			if (!empty($_GET['demande'])) {
				// $encode=urlencode($_GET['demande']);
				// $decode=urldecode($encode);


				// décomposition URL par les / et FILTER_SANITIZE_URL -> Supprime les caractères illégaux des URL
				$url = explode("/", filter_var($_GET['demande'],FILTER_SANITIZE_URL));
				
				switch($url[0]) {
					case 'login' :
						// Retournera une clé API si le login et password sont OK
						// La clé API sera utilisée pour les prochaines requetes.
						if (isset($_GET['login'])) {$login=$_GET['login'];} else {$login="";}
						if (isset($_GET['password'])) {$password=$_GET['password'];} else {$password="";}

						verifLoginPassword($login,$password);  // retourne l'apiKey si les logins / pwd sont ok
					break;
					case 'humeurs' :
						// Retourne un nombre des dernieres humeurs recentes
						authentification(); // Test si on est bien authenfifié pour l'API
						getHumeurs($_GET['codeUtil']);
						break;
					case 'emotions' :
						// Retourne les dernieres emotions
						authentification(); // Test si on est bien authenfifié pour l'API
						getEmotions();
						break;
					case 'user' :
						// Retourne les informations de l'utilisateur connecté
						authentification(); // Test si on est bien authenfifié pour l'API
						getUser($_SERVER["HTTP_APIKEYDEMONAPPLI"]);
						break;
					case "catalogue" :
						// authentification mise en commentaire car le catalogue est ouvert sans login password
						//authentification(); // Test si on est bien authenfifié pour l'API
						$donnees=[];
						$donnees['GET'][0]="/login/:monLogin/:monPassword -> Retourne une clé API si les logins et password sont OK";
						$donnees['GET'][1]="/humeurs -> Retourne les 5 dernieres humeurs de l'utilisateur";
						$donnees['GET'][2]="/emotions -> Retourne toutes les emotions possibles";
						$donnees['GET'][3]="/user -> Retourne les informations de l'utilisateur connecté";
						$donnees['GET'][4]="/catalogue-> Retourne le catalogue de l'API";
						$donnees['POST'][0]="/humeur";

						sendJSON($donnees, 200) ;
						break ;
				
					default : 
						$infos['Statut']="KO";
						$infos['message']="Point de terminaison '".$url[0]."' inexistant";
						sendJSON($infos, 404) ;
				}
			} else {
				$infos['Statut']="KO";
				$infos['message']="URL non valide";
				sendJSON($infos, 404) ;
			}
			break ;
		case "POST" :
			if (!empty($_GET['demande'])) {
				// Ajout d'un client / type de client
				// Récupération des données envoyées
				$url = explode("/", filter_var($_GET['demande'],FILTER_SANITIZE_URL));
				switch($url[0]) {
					case 'humeur' : 
						// Ajout d'une humeur
						authentification(); // Test si on est bien authenfifié pour l'API
						$donnees = json_decode(file_get_contents("php://input"),true);
						ajoutHumeur($donnees, $_SERVER["HTTP_APIKEYDEMONAPPLI"]);
						break ;
					default : 
						$infos['Statut']="KO";
						$infos['message']="Point de terminaison '".$url[0]."' inexistant";
						sendJSON($infos, 404) ;
				}	
			} else {
				$infos['Statut']="KO";
				$infos['message']="URL non valide";
				sendJSON($infos, 404) ;
			}
			break;
		
		default :
			$infos['Statut']="KO";
			$infos['message']="URL non valide";
			sendJSON($infos, 404) ;
	}
	
?>