<?php 
	require_once("donnees.php");

	/**
	 * Fonction qui permet de verifier la clé de l'API renseignée par l'utilisateur est valide
	 */
	function authentification() {
		//On verifie si l'utilisateur en bien renseigné le header
		if (isset($_SERVER["HTTP_APIKEYDEMONAPPLI"])) {
			$cleAPI=$_SERVER["HTTP_APIKEYDEMONAPPLI"];

			$pdo = getPDO(); //on recupere un objet PDO

			$requeteVerifClef = "SELECT codeUtil, cleAPI FROM `utilisateur` WHERE cleAPI = ?";

			$stmt = $pdo->prepare($requeteVerifClef);
			$stmt->execute([$cleAPI]);

			$result = $stmt->fetch();
			$cleAComparer = $result['cleAPI'];

			//On rentre l'id de l'utilisateur dans une variable get
			$_GET['codeUtil'] = $result['codeUtil'];

			// Test de la clé API
			if ($cleAPI != $cleAComparer) {
				$infos['Statut']="KO";
				$infos['message']="APIKEY invalide.";
				sendJSON($infos, 403) ;
				die();
			}
		}else {
			// Pas de clé API envoyée, pas d'accès à l'Api
			$infos['Statut']="KO";
			$infos['message']="Authentification necessaire par APIKEY.";
			sendJSON($infos, 401) ;
			die();
		}
	}	

	/**
	 * Fonction permettant de retourner une clé d'authentification a l'api si le nom d'utilisateur et le mot de passe existent
	 * on génère une clé API si l'utilisateur n'en a pas sinon on retourne celle existante
	 * @param $login le login de l'utilisateur dans le header
	 * @param $password le mot de passe de l'utilisateur dans le header
	 */
	function verifLoginPassword($login, $password) {
		$requete = "SELECT cleAPI FROM utilisateur WHERE identifiant = ? and motDePasse = ?";

		$pdo = getPDO(); //on recupere un objet PDO

		$stmt = $pdo->prepare($requete);
		$stmt->execute([$login, md5($password)]);

		$user = $stmt->fetch();

        if ($user != null) {
			// Genération de la clé, ou récupération de l'existante
			if ($user['cleAPI'] != null) {
				$infos['APIKEYDEMONAPPLI'] = $user['cleAPI'];
				sendJSON($infos, 200);
			} else {
				$clefGenere = genereRandomApiKey();
				$requeteAjoutClefApi = "UPDATE utilisateur SET cleAPI = ? WHERE codeUtil = ? ";
				$idUtilisateur = $user['codeUtil'];
				
				$pdo->beginTransaction();

				try {
					$stmt = $pdo->prepare($requeteAjoutClefApi);
					$stmt->execute([$clefGenere, $idUtilisateur]);
					$infos['APIKEYDEMONAPPLI'] = $clefGenere;
					$pdo->commit();
					sendJSON($infos, 200);
				} catch (Exception $e) {
					$e->getMessage();
					$pdo->rollBack();
					$infos['message'] = "Erreur interne.";
					sendJSON($infos, 400);
				}
			}

		} else {
			// Login incorrect
			$infos['Statut']="KO";
			$infos['message']="Identifiant / mot de passe incorrects.";
			sendJSON($infos, 401) ;
			die();
		}
	}
	?>