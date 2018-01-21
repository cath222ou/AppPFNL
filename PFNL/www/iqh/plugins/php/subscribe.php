<?php

	#$_POST["first_name"]="Olivier"; 
	#$_POST["last_name"]="Dupras-Tessier"; 
	#$_POST["gender"]="male"; 
	#$_POST["company"]="Université de Sherbrooke"; 
	#$_POST["address"]="1478 avenue Bourbonnière";
	#$_POST["city"]="Montréal";
	#$_POST["state"]="Québec";
	#$_POST["zip_code"]="H1W-3N2";
	#$_POST["telephone"]="(514) 506-7427"; 
	#$_POST["email"]="fonctioli@gmail.com";
	#$_POST["code_email"]="fonctioli@gmail.com";
	#$_POST["code_email"]="olivier.dupras-tessier@usherbrooke.ca";
	#$_POST["password"]="jamais3Fois";
	#$_POST["code_password"]="jamais3Fois";
	#$_POST["country"]="Canada";


	class MyDB extends SQLite3 {
    function __construct() {
      $this->open('mysqlitedb.db');
    }
	}
	$db = new MyDB();


	/*
	 * Valid if the user parameters are already exist in our database
	 * @params {SQLITE3_DB, STRING}
	 * return {STRING}
	 */
	function validNewUser($db, $email){
		$statement = "SELECT count(id_user) as exist"; 
		$table =  "FROM user "; 
		$condition = "WHERE code_email LIKE '".$email."'"; 
		$request = join(" ", array($statement, $table, $condition)); 

		$results = $db->query($request); 
		$res = $results->fetchArray(SQLITE3_ASSOC);
		
		return $res['exist']; 
	}


	/*
	 * Insert the user parameters in our database
	 * @params { SQLITE3_DB, STRING }
	 * @return { array() }
	 */
	function insertNewUser($db, $user) {
		$insert = array(); 
		$values = array(); 
		foreach ($user as $key => $value) {
			if ($key != "password") {
				array_push($insert, $key); 
				array_push($values, "'".$value."'");
			}
		}

		$insert = join(", ", $insert); 
		$insert = "INSERT INTO user (".$insert.")"; 

		$values = join(", ", $values); 	
		$values = "VALUES (".$values.");"; 

		$request = join(" ", array($insert, $values)); 

		#echo $request; 
		$db->exec($request); 
	}


	$results = array(); 
	$results['exist'] = validNewUser($db, $_POST["code_email"]); 
	if ($results['exist'] > 0) {
		$results['type'] = "warning"; 
		$results['title'] = "Attention"; 
		$results['text'] = "L'adresse courriel que vous avez employée&#769 est de&#769ja&#768 en cours d'utilisation. <br>Veuillez en employer une autre ou communiquer avec l'administrateur du portail. "; 
	} else {
		insertNewUser($db, $_POST); 
		$results['create'] = validNewUser($db, $_POST["code_email"]);
		if ($results['create'] == 0) {
			$results['type'] = "danger"; 
			$results['title'] = "Proble&#768me interne"; 
			$results['text'] = "Un proble&#768me interne est survenue. <br>Veuillez re&#769essayer plus tard ou communiquer avec l'administrateur du portail. "; 
		} else {
			$results['type'] = "success"; 
			$results['title'] = "Fe&#769licitation"; 
			$results['text'] = "Votre profil de membre s'est comple&#769te&#769 avec succe&#768s. <br>Nous allons maintenant vous rediriger au SIG du portail.<br> Identifiez vous a&#768 l'aide du bouton de connexion de la page."; 
		}
	}

	$res = json_encode($results); 
	echo $res; 

?>