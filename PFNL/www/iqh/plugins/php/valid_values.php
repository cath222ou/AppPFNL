<?php

	#$_POST["data"] = '{"value":[{"value":"3","essence":"BGBP","couvert":"R","etat":"C"},{"value":"3","essence":"CFBJCG","couvert":"R","etat":"C"},{"value":"2","essence":"CFCCCG","couvert":"R","etat":"C"}],"ponderation":"24"}'; 
	#$_POST["name"] = 'essence'; 

	#$_POST["data"] = '{"value":{"Élevé":["81","100"],"Moyen":["71","80"],"Faible":["41","60"],"Absent":["0","40"]}}';
	#$_POST["name"] = "echelle"; 

	#$_POST["value"] = "Cèpe"; 
	#$_POST["name"] = "espece";  

	#$_POST["value"] = "Bas-Saint-Laurent"; 
	#$_POST["name"] = "region";  

	#$_POST["value"] = "olivier.dupras-tessier@usherbrooke.ca"; 
	#$_POST["name"] = "user";  

	#$_POST["data"] = '{"value":{"essence":100}}';
	#$_POST["name"] = "ponderation"; 

	#$_POST["data"] = '{"value":{"id_espece":1,"id_region":2,"id_echelle":11,"id_essence":13,"id_user":1,"id_ponderation":2,"date":"1/2/2016"}}';
	#$_POST["name"] = "iqh"; 



	class MyDB extends SQLite3 {
    function __construct() {
      $this->open('mysqlitedb.db');
    }
	}


	/* 
	 * Assemble values to be conform white oder data from client
	 * @params { array() }
 	 * @return { array() }
	 */
	function joinValues( $obj ) { 
		$results = array();  
		foreach( $obj as $value ) {
			$code = join( "_", array( $value["essence"], $value["etat"], $value["couvert"] ) ); 
			$results[$code] = $value["value"]; 
		}

		return $results; 
	} /** joinValues() */


	/* 
	 * Regroup values from client to a simple array() base on there factor value
	 * @params { array() }
 	 * @return { array() }
	 */
	function regroupValues( $obj ) {
		$results = array( "1" => array(), "2" => array(), "3" => array() ); 
		foreach( $obj as $key => $value ) {
			array_push( $results[$value], $key ); 
		}

		return $results; 
	} /** regroupValues() */


	/* 
	 * Convert array() values to STRING values that SQLiteDB can work with
	 * @params { array() }
 	 * @return { array() }
	 */
	function array2str( $obj ) {
		$results = array();  
		foreach( $obj as $key => $value ) {
			$results[$key] = "[".join(",", $value)."]"; 
		}

		return $results; 
	} /** array2str() */


	function setCondition( $obj, $list ) {
		$res = array(); 
		foreach( $list as $value ) {
			( isset( $obj[$value] ) ) ? $val = strval( $obj[$value] ) : $val = '0'; 
			$val = join( " LIKE ", array( $value, $val ) ); 
			array_push( $res, $val ); 
		}
		$res = join( " AND ", $res );
		$results = "WHERE ".$res.";";

		return $results; 
	}


	/* 
	 * Condition Query constructor
	 * @params { array(), STRING }
	 * @return { STRING }
	 */
	function getQueryCondition( $obj, $method ) {
		if ( $method == "variable" ) {
			$results = "WHERE classe_1 LIKE '".$obj['1']."' AND classe_2 LIKE '".$obj['2']."' AND classe_3 LIKE '".$obj['3']."';"; 
		} else if ( $method == "identity" ) {
			$results = "WHERE name LIKE '".$obj."';";
		} else if ( $method == "echelle" ) {
			$results = "WHERE Absent LIKE '".$obj['Absent']."' AND Faible LIKE '".$obj["Faible"]."' AND Moyen LIKE '".$obj["Moyen"]."' AND Eleve LIKE '".$obj["Eleve"]."';"; 
		} else if ( $method == "user" ) {
			$results = "WHERE id_user LIKE (SELECT id_user FROM user WHERE code_email LIKE '".$obj."');"; 
		} else if ( $method == "iqh" ) {
			/** List of specific "iqh" table attributs */
			$list = array( "exist", "id_espece", "id_age", "id_densite", "id_drainage", "id_essence", "id_origine", "id_depot", "id_pente", "id_age_feux", "id_age_intervention", "id_intervention", "id_echelle", "id_ponderation" );  
			$results = setCondition( $obj, $list ); 
		} else if ( $method == "ponderation" ) {
			/** List of specific "ponderation" table attributs */
			$list = array( "age", "densite", "drainage", "essence", "origine", "depot", "pente", "age_feux", "age_intervention", "intervention" );  
			$results = setCondition( $obj, $list ); 
		}

		#echo $results; 
		return $results; 
	} /** getQueryCondition() */


	/* 
	 * Attributs Query constructor
	 * @params { array(), STRING }
	 * @return { STRING }
	 */
	function getQueryAttributs( $obj, $method ) {
		if ( $method == "variable" ) {
			$results = "classe_1, classe_2, classe_3"; 
		} else if ( $method == "identity" ) {
			$results = "name";
		} else if ( $method == "echelle" ) {
			$results = "Eleve, Moyen, Faible, Absent"; 
		} else if ( $method == "iqh" ) {
			/** List of specific NOT NULL iqh table attributs require to INSERT VALUES */
			$res = array( "exist", "date", "id_espece", "id_region", "id_user", "id_age", "id_densite", "id_drainage", "id_essence", "id_origine", "id_depot", "id_pente", "id_age_feux", "id_age_intervention", "id_intervention", "id_echelle", "id_ponderation" );  
			$results = join( ", ", $res );
		} else if ( $method == "ponderation" ) {
			$res = array( "age", "densite", "drainage", "essence", "origine", "depot", "pente", "age_feux", "age_intervention", "intervention" );  
			$results = join( ", ", $res );
		}

		#echo $results; 
		return $results; 
	} /** getQueryAttributs() */


	/* 
	 * SELECT SQLiteDB QUERY
	 * @params { database, array(), STRING }
	 * @return { STRING }
	 */
	function getValueID( $db, $obj, $attributs, $table_name, $method ) {
		$statement = "SELECT ".$attributs." as id";
		$table = "FROM ".$table_name; 
		$condition = getQueryCondition( $obj, $method ); 
		$request = join( " ", array($statement, $table, $condition) ); 

		#echo $request; 
		$results = $db->query($request); 
		$res = $results->fetchArray(SQLITE3_ASSOC);
		
		return $res['id'];  
	} /** getValueId() */ 


	/* 
	 * SELECT COUNT() SQLiteDB QUERY
	 * @params { database, array(), STRING }
	 */
	function validValues( $db, $obj, $table_name, $method ) {
		$statement = "SELECT COUNT(*) as exist";
		$table = "FROM ".$table_name; 
		$condition = getQueryCondition( $obj, $method );  
		$request = join( " ", array($statement, $table, $condition) ); 

		#echo $request; 
		$results = $db->query($request); 
		$res = $results->fetchArray(SQLITE3_ASSOC);
		
		return $res['exist'];  
	} /** validValues() */ 


	function setValues( $obj, $list ) {
		$results = array(); 
		foreach( $list as $id ) {
			( isset( $obj[$id] ) ) ? $val = "'".strval ( $obj[$id] )."'" : $val = "'0'"; 
			array_push( $results, $val ); 
		} 		

		return $results; 
	}


	/* 
	 * INSERT INTO SQLiteDB QUERY
	 * @params { database, array(), STRING }
	 */
	function insertValues( $db, $obj, $table_name, $method ) {
		$attributs = getQueryAttributs( $obj, $method ); 
		$insert = "INSERT INTO ".$table_name." (".$attributs.")"; 

		if ( is_array($obj) ) {
			$values = array(); 

			if ( $method == "iqh" ) {
				$list = array( "exist", "date", "id_espece", "id_region", "id_user", "id_age", "id_densite", "id_drainage", "id_essence", "id_origine", "id_depot", "id_pente", "id_age_feux", "id_age_intervention", "id_intervention", "id_echelle", "id_ponderation" );  
				$values = setValues( $obj, $list ); 
			} else if ( $method == "ponderation" ) {
				$list = array( "age", "densite", "drainage", "essence", "origine", "depot", "pente", "age_feux", "age_intervention", "intervention" );  
				$values = setValues( $obj, $list ); 
			} else { 
				foreach( $obj as $key => $value ) $values[$key] = "'".$value."'"; 
			}

			$values = join( ", ", $values ); 	
			$values = "VALUES (".$values.");"; 

		} else {
			$values = "VALUES ('".$obj."');"; 
		}

		$request = join( " ", array( $insert, $values ) ); 

		#echo $request; 
		$db->exec($request); 
	} /** insertValues() */ 


	/* 
	 * Recursive function() - Call itself only if count and data['exist'] are less than 1
	 * @params { database, array(), STING, INT }
	 * @echo { JSON }
	 */
	function confirmProcess( $db, $bool, $obj, $name, $method, $count ) {
		$res = array(); 
		if ( $bool > 0 ) { /** Only if the values exist int the database */ 
			$res["exist"] = true; 
			$res["name"] = "id_".$name; 
			$res["value"] = getValueId( $db, $obj, $res["name"], $name, $method ); 

			if ( $method == "iqh" ) {
				if ( $count == 0 ) {
					$res["type"] = "warning"; 
					$res["title"] = "L'IQH demande&#769 existe de&#769 dans notre base de donn&#769es"; 
					$res["text"] = "Veuillez consulter la liste d'IQH pre&#769sent sur le portail pour plus de de&#769tails."; 
				} else {
					$res["type"] = "success"; 
					$res["title"] = "La cre&#769ation de votre IQH est en cours"; 
					$res["text"] = "Vous recevrez une notification a&#768 la fin du processus.<br>Nous vous redirigeons de&#769sormais au SIG."; 
				}
			}

			echo json_encode($res); 

		} else { /** Only if the values doesn't exist int the database */ 
			if ( $count > 0 ) { /** Only if the procedure is at his second (or more : its impossible) iteration */
				if ( $method != "identity" ) { /** Only if the method is not identity */
					$res["exist"] = false; 
					$res["type"] = "danger"; 
					$res["title"] = "Connexion e&#769choue&#769e"; 
					$res["text"] = "Une erreur interne s'est poduites.<br>Veuillez re&#769essayer plus tard."; 

					echo json_encode($res); 
				}
			} else { /** Only if the procedure is at his first iteration */
				insertValues( $db, $obj, $_POST["name"], $method ); 
				$res['exist'] = validValues( $db, $obj, $_POST["name"], $method ); 
				#echo $res['exist']; 
				$count++; 

				confirmProcess( $db, $res['exist'], $obj, $name, $method, $count ); 
			}
		}
	} /** confirmProcess() */


	/*
	 * Define witch method it have to be use during the process
	 * @params { array() }
	 * @return { STRING }
	 */
	function evalMethod( $obj ) {
		if ( in_array( $obj, array( "espece", "region" ), true ) ) {
			$results = "identity"; 
		} else if ( in_array( $obj, array( "echelle", "user", "ponderation", "iqh" ) ) ) {
			$results = $obj; 
		} else {
			$results = "variable"; 
		}
		
		return $results; 
	} /** evalMethod() */



	$db = new MyDB();
	$method = evalMethod( $_POST["name"] ); 
	
	// Only if method is Variable
	if ( $method == "variable" ) { 
		/** Convert $_POST["data"] to a valid php array() */
		$data = json_decode($_POST["data"], true); 
		#print_r($data); 

		if ( $_POST["name"] == "essence" ) { // Only if name is Essence
			$data["value"] = joinValues( $data["value"] ); 
			#print_r($data["value"]); 
		}

		$data["group"] = regroupValues( $data["value"] ); 
		#print_r($data["group"]); 
		$data["string"] = array2str( $data["group"] ); 
		#print_r($data["string"]); 
		$data["exist"] = validValues( $db, $data["string"], $_POST["name"], $method ); 
		#echo $data["exist"]; 

		confirmProcess( $db, $data["exist"], $data["string"], $_POST["name"], $method, 0 ); 
	}

	// Only if method is Identity 
	else if ( in_array( $method, array( "identity", "user" ) ) ) {
		$data = array(); 
		$data["exist"] = validValues( $db, $_POST["value"], $_POST["name"], $method ); 
		#echo $data["exist"]; 

		confirmProcess( $db, $data["exist"], $_POST["value"], $_POST["name"], $method, 0 ); 
	} 
	
	// Only if method is echelle
	else if ( $method == "echelle" ) {  
		/** Convert $_POST["data"] to a valid php array() */
		$data = json_decode($_POST["data"], true); 
		#print_r($data); 

		/** Change "Élevé" key to "Eleve" */
		$res = array(); 
		foreach ( $data["value"] as $key => $value ) {
			( in_array( $key, array("Absent", "Faible", "Moyen") ) ) ? $res[$key] = $value : $res["Eleve"] = $value; 
		}

		$data["value"] = $res;
		#print_r($data["value"]); 
		$data["string"] = array2str( $data["value"] ); 
		#print_r($data["string"]); 
		$data["exist"] = validValues( $db, $data["string"], $_POST["name"], $method ); 
		#echo $data["exist"]; 

		confirmProcess( $db, $data["exist"], $data["string"], $_POST["name"], $method, 0 ); 
	}

	else if ( in_array( $method, array( "ponderation", "iqh" ) ) ) {
		/** Convert $_POST["data"] to a valid php array() */
		$data = json_decode( $_POST["data"], true ); 
		#print_r($data); 

		$data["exist"] = validValues( $db, $data["value"], $_POST["name"], $method ); 
		#echo $data["exist"]; 

		confirmProcess( $db, $data["exist"], $data["value"], $_POST["name"], $method, 0 ); 
	}

?>