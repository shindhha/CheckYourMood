<?php
	/**
	 * Fonctions permettant d'extraire les données de la base de donnée
	 */

	/**
	 * Fonction qui permet de creer un objet PDO pour interagir avec la base de donnée
	 */
	function getPDO(){

		// Retourne un objet connexion à la BD
		$host='127.0.0.1';	// Serveur de BD
		$db='check_your_mood';		// Nom de la BD
		$user='root';		// User 
		$pass='root';		// Mot de passe
		$charset='utf8mb4';	// charset utilisé
		
		// Constitution variable DSN
		$dsn="mysql:host=$host;dbname=$db;charset=$charset";
		
		// Réglage des options
		$options=[																				 
			PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES=>false];
		
		try{	// Bloc try bd injoignable ou si erreur SQL
			$pdo=new PDO($dsn,$user,$pass,$options);
			return $pdo ;			
		} catch(PDOException $e){
			//Il y a eu une erreur de connexion
			$infos['Statut']="KO";
			$infos['message']="Problème connexion base de données";
			sendJSON($infos, 500) ;
			die();
		}
	}
	/**
	 * Fonction qui permet de tester si une clé de l'api n'existe pas deja
	 */
	function verifApiKey($clefATester) { 
		$pdo = getPDO();
		$requeteVerifClef = "SELECT cleAPI FROM `utilisateur` WHERE cleAPI = ?";

		$stmt = $pdo->prepare($requeteVerifClef);
		$stmt->execute([$cleAPI]);

		if ($stmt->rowCount() == 0) {
			return $clefATester;
		}
	}
	/**
	 * Fonction qui permet de generer une clé aléatoire pour l'API et appele verifApiKey
	 */
	function genereRandomApiKey() {
		$length = 100;
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$apiKey = '';

		for ($i = 0; $i < $length; $i++) {
			$apiKey .= $characters[random_int(0, strlen($characters) - 1)];
		}
	
		return verifApiKey($apiKey);
	}

	/**
	 * Fonction qui permet de recuperer les 5 dernieres humeurs d'un utilisateur
	 */
	function getHumeurs($codeUtil) {
		try {
			$pdo=getPDO();

			$maRequete="SELECT libelle, dateHumeur, heure, contexte FROM `humeur` WHERE idUtil = ? ORDER BY `humeur`.`dateHumeur` DESC LIMIT 5"; 
			
			$stmt = $pdo->prepare($maRequete);
			$stmt->execute([$codeUtil]);	
				
			$humeurs=$stmt ->fetchALL();
			$nb = $stmt->rowCount();
			
			$stmt->closeCursor();
			$stmt=null;
			$pdo=null;
	
			if ($nb!=0) {
				sendJSON($humeurs, 200) ;
			} else {
				sendJSON($humeurs, 404) ;
			}
		} catch(PDOException $e){
			$infos['Statut']="KO";
			$infos['message']=$e->getMessage();
			sendJSON($infos, 500) ;
		}
	}

	/**
	 * Fonction qui permet de recuperer les emotions presentes par defaut
	 */
	function getEmotions() {
		try {
			$pdo=getPDO();

			$maRequete="SELECT libelleHumeur, emoji FROM libelle"; 
			
			$stmt = $pdo->prepare($maRequete);
			$stmt->execute([]);	
				
			$humeurs=$stmt ->fetchALL();
			$nb = $stmt->rowCount();
			
			$stmt->closeCursor();
			$stmt=null;
			$pdo=null;
	
			if ($nb!=0) {
				sendJSON($humeurs, 200) ;
			} else {
				sendJSON($humeurs, 404) ;
			}
		} catch(PDOException $e){
			$infos['Statut']="KO";
			$infos['message']=$e->getMessage();
			sendJSON($infos, 500) ;
		}
	}

	/**
	 * Fonction qui permet de recuperer les informations d'un utilisateur
	 */
	function getUser($userApiKey) {
	
		try {
			$pdo=getPDO();

			$maRequete="SELECT prenom, nom FROM `utilisateur` WHERE cleAPI = ?"; 
			
			$stmt = $pdo->prepare($maRequete);
			$stmt->execute([$userApiKey]);	
				
			$user=$stmt->fetch();
			$nb = $stmt->rowCount();
			
			$stmt->closeCursor();
			$stmt=null;
			$pdo=null;
	
			if ($nb!=0) {
				sendJSON($user, 200);
			} else {
				$infos['Statut']="KO";
				$infos['message']="Utilisateur non trouvé";
				sendJSON($infos, 404);
			}
		} catch(PDOException $e){
			$infos['Statut']="KO";
			$infos['message']=$e->getMessage();
			sendJSON($infos, 500) ;
		}
	
	}

	/**
	 * Fonction qui permet d'ajouter une humeur
	 */
	function ajoutHumeur($donnees, $cleAPI) {
		$libelle = $donnees['LIBELLE'];
		$dateHumeur = $donnees['DATE_HUMEUR'];
		$heure = $donnees['HEURE'];
		$contexte = $donnees['CONTEXTE'];

		try {
			$pdo=getPDO();

			$maRequete="INSERT INTO `humeur` (`libelle`, `dateHumeur`, `heure`, `idUtil`, `contexte`) 
						VALUES (?, ?, ?, (SELECT codeUtil FROM utilisateur WHERE cleAPI = ?), ?)"; 
			
			$stmt = $pdo->prepare($maRequete);
			$stmt->execute([$libelle, $dateHumeur, $heure, $cleAPI, $contexte]);	
	
			sendJSON(array('Statut' => "OK"), 200);
		} catch(PDOException $e){
			$infos['Statut']="KO";
			$infos['message']=$e->getMessage();
			sendJSON($infos, 500) ;
		}
	}
	
?>