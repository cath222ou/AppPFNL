<?php

	#$_POST["email"] = "olivier.dupras-tessier@usherbrooke.ca"; 
	#$_POST["type"] = "profile"; 
	#$_POST["table"] = ".table#user"; 
	#$_POST["methode"] = "share"; 
	#$_POST["status"] = "developpeur";
	#$_POST["user"] = "28"; 
	#$_POST["project"] = "41"; 
	

	/* 
	 * CLASS::SQLiteDB 
	 * Group every methods associated to SQLiteDB data management
	 */
	class SQLiteDB extends SQLite3 {

    function __construct() {
      $this->open('mysqlitedb.db');
    }

	  /*
		 * Valid if the user parameters are realy exist in our database
		 * @params { STRING }
		 * @return { STRING }
		 */
		function validUser( $user ) {
			$statement = "SELECT count(id_user) as exist"; 
			$table = "FROM user "; 
			$condition = "WHERE code_email LIKE '". $user ."'"; 
			$request = join( " ", array( $statement, $table, $condition ) ); 

			$results = $this->query( $request ); 
			$res = $results->fetchArray( SQLITE3_ASSOC );
			
			return $res['exist']; 
		} /** validUser() */ 


		/*
		 * Get the user ID 
		 * @params { STRING }
		 * @return { STRING }
		 */
		function getUserId( $user ) {
			$statement = "SELECT id_user as id"; 
			$table = "FROM user "; 
			$condition = "WHERE code_email LIKE '". $user ."'"; 
			$request = join( " ", array( $statement, $table, $condition ) ); 

			$results = $this->query( $request ); 
			$res = $results->fetchArray( SQLITE3_ASSOC );
			
			return $res['id']; 
		} /** validUser() */ 


		/*
		 * Valid if the user parameters are realy exist in our database
		 * @params { STRING }
		 * @return { STRING }
		 */
		function getUserList( $obj ) {
			if ( $_POST["type"] == "profile" ) {
				$statement = "SELECT email, status, id_user as id, last_name, first_name, gender, company, telephone, address, city, zip_code"; 
			} else {
				$statement = "SELECT email, status, id_user as id"; 
			}

			$table = "FROM user";

			if ( $obj == "admin" ) {
				$condition = "WHERE status NOT LIKE '". $obj ."'"; 
			} else {
				$condition = "WHERE id_user NOT LIKE '". $obj ."'";
			}
			 
			$request = join( " ", array( $statement, $table, $condition ) ); 

			#echo $request; 
			$results = $this->query( $request ); 
			$row = array(); 
			while ( $res = $results->fetchArray( SQLITE3_ASSOC ) ) {
				array_push( $row, $res ); 
			}
			
			return $row; 
		} /** getUserList() **/


		/* 
		 * Update a feature which already exist in PostgreSQL
		 * @params { ARRAY() }
		 * @return { STRING }
		 */
		function updateAttirbut( $table, $attribut, $value, $id ) {
			$statement = "UPDATE ".$table; 
			$expression = "SET ".$attribut." = '".$value."'"; 

			if ( $table == "user" ) {
				$condition = "WHERE id_user LIKE '".$id."'";  
			} else if ( $table == "project" ) {
				$condition = "WHERE id_project LIKE '".$id."'";  
			}
			
			$request = join( " ", array( $statement, $expression, $condition ) ); 

			#echo $request; 
			$this->exec( $request ); 
		} /* UpdateSQLITEDBProject() */ 


		/*
		 * Insert a new table in SQLITEBD 
		 * @params { STRING, ARRAY(), ARRAY() }
		 * @return { STRING }
		 */
		function setAttributs( $table, $attributs, $values ) { 
			$insert = "INSERT INTO ". $table ." ( ". join( ", ", $attributs ) ." )"; 
			$values = "VALUES ( ". join( ", ", $values ) .")"; 
			$request = join( " ", array( $insert, $values ) ); 

			$this->exec( $request ); 
		} /* CreateSQLITEDBProject() */ 


		/*
	   * Delete SQLiteDB ROW where id_project match with $_POST[] argument
	   * @params { STRING }
	   * @return { --- }
	   */
	  function removeSharing( $project ) {
	    $statement = "DELETE"; 
	    $table =  "FROM share"; 
	    $condition = "WHERE id_project LIKE '". $project ."';"; 
	    $request = join( " ", array( $statement, $table, $condition ) ); 

	    $this->exec( $request ); 
	  } /* removeSharing() */ 


	} /* CLASS::SQLiteDB */ 


	/* 
	 * CLASS::SQLiteProcess 
	 * Group every methods to create projects and notices
	 */
	class Messages {


		/*
		 * warning alert contructor 
		 * @params { --- }
		 * @return { --- }
		 */
		function warning() {
			$results = array( 
				"exist" => false,  
				"type" => "warning",  
		    "title" => "E&#769chec"
		  ); 

		  return $results; 
		} /* warning() */ 


		/*
		 * warning alert contructor 
		 * @params { --- }
		 * @return { --- }
		 */
		function success() {
			$results = array( 
				"exist" => true,  
				"type" => "success",  
		    "title" => "Succe&#768s" 
		  ); 

		  return $results; 
		} /* warning() */


		/*
		 * Update method alert constructor
		 * @params { STRING }
		 * @return { ARRAY() }
		 */
		function update( $state ) {
			if ( $state == "success" ) {
				$results = static::success(); 
				$results["text"] = "La mise a&#768 jour du projet s'est bien de&#769roule&#769"; 

			} else if ( $state == "warning" ) {
				$results = static::warning(); 
				$results["text"] = "Un proble&#768me est survenue lors de la mise a&#768 jour du projet. <br> Veuillez re&#769essayer plus tard. "; 
			}
			
			return $results; 
		} /* update() */


		/*
		 * Create method alert constructor
		 * @params { STRING }
		 * @return { ARRAY() }
		 */
		function create( $state ) {
			if ( $state == "success" ) {
				$results = static::success(); 
				$results["text"] = "La cre&#769ation du projet a bien fonctionne&#769. <br> Il est maintenant accessible dans la zone de chargement de projet. "; 

			} else if ( $state == "warning" ) {
				$results = static::warning(); 
				$results["text"] = "Un proble&#768me est survenue lors de la cre&#769 du projet. <br> Veuillez re&#769essayer plus tard. "; 
			}
			
			return $results; 
		} /* create() */ 


		/*
		 * Create method alert constructor
		 * @params { STRING }
		 * @return { ARRAY() }
		 */
		function share( $state ) {
			if ( $state == "success" ) {
				$results = static::success(); 
				$results["text"] = "Le partage du projet a bien fonctionne&#769. "; 

			} else if ( $state == "warning" ) {
				$results = static::warning(); 
				$results["text"] = "Un proble&#768me est survenue lors du partage du projet. <br> Veuillez re&#769essayer plus tard. "; 
			}
			
			return $results; 
		} /* create() */ 


		/*
		 * Create method alert constructor
		 * @params { STRING }
		 * @return { ARRAY() }
		 */
		function unshare( $state ) {
			if ( $state == "success" ) {
				$results = static::success(); 
				$results["text"] = "Le partage du projet a cesse&#769. "; 

			} else if ( $state == "warning" ) {
				$results = static::warning(); 
				$results["text"] = "Un proble&#768me est survenue lors de l&#39arre&#770t du partage de projet. <br> Veuillez re&#769essayer plus tard. "; 
			}
			
			return $results; 
		} /* create() */ 


		/*
		 * named alert contructor 
		 * @params { --- }
		 * @return { ARRAY() }
		 */
		function named() {
			$results = static::warning(); 
			$results["text"] = "Le nom de projet utilise&#769 existe de&#769ja&#768. <br> Veuillez modifier le nom de projet et re&#769essayer de nouveau."; 

			return $results; 
		} /* named() */


		/*
		 * error alert contructor 
		 * @params { --- }
		 * @return { ARRAY() }
		 */
		function error() {
			$results = static::warning(); 
			$results["text"] = "Un proble&#768me est survenue. Veuillez re&#769essayer de nouveau."; 

			return $results; 
		} /* error() */


	} /* CLASS::Messages */


	/*
	 * Builed the VALUE section in INSERT statement  
	 * @params { ARRAY() }
	 * @return { STRING }
	 */
	function setValues( $obj ) {
		$results = array(); 
		foreach( $obj as $val ) {
			$results.array_push( $results, "'". $val ."'" ); 
		} 		

		return $results; 
	} /* setValues() */


	/*
	 * Seek the good message displaying 
	 * @params { ARRAY(), STRING }
	 * @return { ARRAY() }
	 */
	function setMessages( $obj, $message ) {
		$results = $obj; 
		foreach( $message as $key => $value ) { 
			$results[$key] = $value;
		}		

		return $results; 
	} /* setMessages() */


	$sqlite = new SQLiteDB(); 
	$message = new Messages(); 
	$results = array(); 
	$results["exist"] = $sqlite->validUser( $_POST["email"] ); 
	if ( $results['exist'] > 0 ) { 

		if ( $_POST["methode"] == "get" ) {
			$results["list"] = $sqlite->getUserList( "admin" ); 

			$results["exist"] = true; 
			$results["table"] = $_POST["table"]; 
			$results["operator"] = $_POST["methode"];

		} else if ( $_POST["methode"] == "getAll" ) {
			$id = $sqlite->getUserId( $_POST["email"] ); 
			$results["list"] = $sqlite->getUserList( $id ); 

			$results["exist"] = true; 
			$results["table"] = $_POST["table"]; 
			$results["operator"] = $_POST["methode"];

		} else if ( $_POST["methode"] == "update" ) {
			$sqlite->updateAttirbut( "user", "status", $_POST["status"], $_POST["id"] ); 

			$results["exist"] = true; 
			$results["table"] = $_POST["table"];
			$results["operator"] = $_POST["methode"]; 
			$results = setMessages( $results, $message->update( "success" ) ); 

		} else if ( $_POST["methode"] == "share" ) {
			$attributs = array( "id_user", "id_project" ); 
			$values = setValues( array( $_POST["user"], $_POST["project"] ) ); 

			$sqlite->updateAttirbut( "project", "type", "share", $_POST["project"] ); 
			$sqlite->removeSharing( $_POST["project"] ); 
			$sqlite->setAttributs( "share", $attributs, $values ); 

			$results["exist"] = true; 
			$results = setMessages( $results, $message->share( "success" ) ); 

		} else if ( $_POST["methode"] == "unshare" ) {
			$sqlite->updateAttirbut( "project", "type", "ready", $_POST["project"] ); 
			$sqlite->removeSharing( $_POST["project"] ); 

			$results["exist"] = true; 
			$results = setMessages( $results, $message->unshare( "success" ) ); 

		}

	} else {
		$results["exist"] = false; 
		$results['type'] = "danger"; 
		$results['title'] = "Proble&#768me interne"; 
		$results['text'] = "Votre reque&#770te n&#39a pu e&#770tre correctement achemine&#769e. "; 
		$results['operator'] = "stay"; 
	}

	echo json_encode( $results );

?>