<?php 

	#$_POST["data"] = '{"type":"FeatureCollection","features":[{"type":"Feature","id":1,"geometry":{"type":"Point","coordinates":[-8093288.442884747,6237745.3154010475]}},{"type":"Feature","id":2,"geometry":{"type":"Point","coordinates":[-8094358.5612807395,6226279.761158272]}},{"type":"Feature","id":3,"geometry":{"type":"Point","coordinates":[-8068828.593833491,6235910.826722204]}},{"type":"Feature","id":4,"geometry":{"type":"Point","coordinates":[-8051859.5735541815,6242178.663041588]}},{"type":"Feature","id":6,"geometry":{"type":"LineString","coordinates":[[-8105365.4933538055,6237592.441344477],[-8103531.004674961,6221234.917291449],[-8074179.185813453,6220470.547008598],[-8051706.699497611,6230254.4866291005]]}}]}';
	#$_POST["format"] = "GeoJSON"; 
	#$_POST["link"] = "http://igeomedia.com/~odupras/data/tmp/export"; 
	$_POST["file"] = "allo"; 
	$_POST["method"] = "load"; 
	#$_POST["type"] = "personal"; 
	#$_POST["status"] = "ready"; 
	$_POST["user"] = "olivier.dupras-tessier@usherbrooke.ca"; 
	#$_POST["date"] = "12/2/2016"; 

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
		 * Get the user's id
		 * @params { STRING }
		 * @return { STRING }
		 */
		function getUserId( $user ) {
			$statement = "SELECT id_user as id"; 
			$table =  "FROM user "; 
			$condition = "WHERE code_email LIKE '". $user ."'"; 
			$request = join( " ", array( $statement, $table, $condition ) ); 

			$results = $this->query($request); 
			$res = $results->fetchArray(SQLITE3_ASSOC);
			
			return $res['id']; 
		} /** validUser() */ 


		/*
		 * Get the notice's id by project's id 
		 * @params { STRING, STRING }
		 * @return { STRING }
		 */
		function getNotice( $proj, $state ) {
			if ( $state == "count" ) {
				$statement = "SELECT COUNT(id_notice) as res"; 
			} else if ( $state == "id" ) {
				$statement = "SELECT id_notice as res"; 
			}

			$table =  "FROM notice "; 
			$condition = "WHERE type LIKE 'project' AND text LIKE '". $proj ."'"; 
			$request = join( " ", array( $statement, $table, $condition ) ); 

			$results = $this->query( $request ); 
			$res = $results->fetchArray(SQLITE3_ASSOC);
			
			return $res['res']; 
		} /** getProjectId() */ 


		/*
		 * Get the projects's id by project's name and user's id 
		 * @params { STRING, STRING }
		 * @return { STRING }
		 */
		function getProject( $file, $user, $state ) {
			if ( $state == "count" ) {
				$statement = "SELECT COUNT(id_project) as res"; 
			} else if ( $state == "id" ) {
				$statement = "SELECT id_project as res"; 
			} 

			$table =  "FROM project "; 
			$condition = "WHERE name LIKE '". $file ."' AND id_user LIKE '". $user ."'"; 
			$request = join( " ", array( $statement, $table, $condition ) ); 

			$results = $this->query( $request ); 
			$res = $results->fetchArray( SQLITE3_ASSOC );
			
			return $res['res']; 
		} /** getProject() */ 	


		/*
		 * Get every projects's attributs required required to create a new layer
		 * @params { STRING, STRING }
		 * @return { STRING }
		 */
		function getProjectProperties( $file, $user ) {
			$statement = "SELECT id_project as id, name, type"; 
			$table =  "FROM project "; 
			$condition = "WHERE name LIKE '". $file ."' AND id_user LIKE '". $user ."'"; 
			$request = join( " ", array( $statement, $table, $condition ) ); 

			$res = $this->query( $request ); 
			$results = $res->fetchArray( SQLITE3_ASSOC );
			
			return $results; 
		} /** getProjectProperties() */ 	


		/*
		 * List every user's projects access 
		 * @params { STRING }
		 * @return { ARRAY() }
		 */
		function getProjectList( $user ) {
			$statement = "SELECT id_project as id, name, type, status"; 
			$table = "FROM project"; 
			$condition = "WHERE id_user LIKE ". $user; 

			$request = join( " ", array( $statement, $table, $condition ) ); 

			$results = $this->query( $request ); 
			$row = array(); 
			while ( $res = $results->fetchArray( SQLITE3_ASSOC ) ) {
				array_push( $row, $res ); 
			}
			
			return $row; 
		} /* getProjectList() */


		/*
		 * Insert a new table in SQLITEBD 
		 * @params { STRING, ARRAY(), ARRAY() }
		 * @return { STRING }
		 */
		function setAttributs ( $table, $attributs, $values ) { 
			$insert = "INSERT INTO ". $table ." ( ". join( ", ", $attributs ) ." )"; 
			$values = "VALUES ( ". setValues( $values ) .")"; 
			$request = join( " ", array( $insert, $values ) ); 

			$this->exec( $request ); 
		} /* CreateSQLITEDBProject() */ 


	} /* CLASS::SQLiteDB */  


	/*
	 * CLASS::PostgreSQL
	 * Group every methods to create PostgreSQL-PostGIS projects
	 */ 
	class PostgreSQL {


		/* 
		 * Get PostgreSQL connection set by connection's params
		 * @params { --- }
		 * @return { pg_connect() }
		 */
		function getConnection() {
			// Connexion, sélection de la base de données
			$results = pg_connect( "host=184.107.180.162 port=5432 dbname=odupras user=odupras password=odupras$2016" )
			    or die( 'Connexion impossible : ' . pg_last_error() );

			return $results; 
		} /* getConnection() */ 


		/* 
		 * Create a new PostgreSQL table by project's id 
		 * @params { STRING }
		 * @return { --- }
		 */
		function createProject( $id ) {
			$conn = static::getConnection(); 
			$table = join( "_", array( "project", $id ) ); 
			$query = "CREATE TABLE ". $table ." ( gid SERIAL PRIMARY KEY, the_geom GEOMETRY )";  

			pg_query( $query ) or die( 'Échec de la requête : ' . pg_last_error() ); 
			pg_close( $conn );
		} /* createTable() */ 


		/* 
		 * Get table's content  
		 * @params { STRING }
		 * @return { STRING }
		 */
		function loadProject( $id ) {
			$conn = static::getConnection(); 
			$statement = "SELECT gid, ST_AsGeoJSON( the_geom ) as geometry"; 
			$table = "FROM ".join( "_", array( "project", $id ) ); 
			$query = join( " ", array( $statement, $table ) );  

			$res = pg_query( $query ) or die( 'Échec de la requête : ' . pg_last_error() );
			$results = array(); 
			while ( $row = pg_fetch_array( $res, null, PGSQL_ASSOC ) ) {
				$row["geometry"] = json_decode( $row["geometry"], true ); 
				array_push( $results, $row ); 
			}

			pg_free_result( $res ); 
			pg_close( $conn );

			return $results; 
		} /* loadTable() */ 


		/* 
		 * Create a new PostGIS POINT GEOMETRY as Text 
		 * @params { ARRAY() }
		 * @return { STRING }
		 */
		function makePoint( $obj ) {
			$coord = join( ", ", $obj ); 
			$results = join( "", array( "ST_MakePoint(", $coord, ")" ) ); 

			return $results; 
		} /* makePoint() */


		/* 
		 * Create a new PostGIS LINE GEOMETRY as Text 
		 * @params { ARRAY() }
		 * @return { STRING }
		 */
		function makeLine( $obj ) {
			$res = array(); 
			foreach ( $obj as $point ) {
				array_push( $res, static::makePoint( $point ) ); 
			}
			$results = join( "", array( "ST_MakeLine( ARRAY[ ", join( ", ", $res ), " ] )" ) ); 

			return $results; 
		} /* makeLine() */ 


		/* 
		 * Create a new PostGIS POLYGON GEOMETRY as Text 
		 * @params { ARRAY() }
		 * @return { STRING }
		 */
		function makePolygon( $obj ) {
			$res = array(); 
			foreach ( $obj as $line ) {
				array_push( $res, static::makeLine( $line ) ); 
			}
			$results = join( "", array( "ST_MakePolygon(", join( ", ", $res ), ")" ) ); 

			return $results; 
		} /* makePolygon() */


		/* 
		 * Define the PostGIS method  
		 * @params { STRING, ARRAY() }
		 * @return { STRING }
		 */
		function geomAsText( $type, $geom ) {
			if ( $type == "Point" ) {
				$results = static::makePoint( $geom ); 
			} else if ( $type == "LineString" ) {
				$results = static::makeLine( $geom ); 
			} else if ( $type == "Polygon" ) {
				$results = static::makePolygon( $geom ); 
			}

			return $results; 
		} /* geomAsText() */


		/*
		 * Insert a new table in PostgreSQL  
		 * @params { STRING, ARRAY(), ARRAY() }
		 * @return { STRING }
		 */
		function setAttributs( $table, $attributs, $values ) {
			$conn = static::getConnection(); 
			$insert = "INSERT INTO ". $table ." ( ". join( ", ", $attributs ) ." )"; 
			$values = "VALUES ( ". $values .")"; 
			$query = join( " ", array( $insert, $values ) ); 

			pg_query( $query ) or die( 'Échec de la requête : ' . pg_last_error() );
			pg_close( $conn );
		} /* setAttributs() */ 


		/* 
		 * Convert GeoJSON feature to PostGIS feature and had it to the rigth project's table
		 * @params { ARRAY() }
		 * @return { STRING }
		 */
		function feature2PostGIS( $id, $layer ) {
			$table = join( "_", array( "project", $id ) ); 
			$attributs = array( "the_geom" ); 
			foreach ( $layer["features"] as $feature ) {
				$type = $feature["geometry"]["type"]; 
				$geom = join( "", array( "ST_SetSRID(", static::geomAsText( $type, $feature["geometry"]["coordinates"] ), ", 4326)" ) ); 

				static::setAttributs( $table, $attributs, $geom ); 
			}
		} /* feature2PostGIS() */


	} /* CLASS::PostgreSQL */


	/* 
	 * CLASS::SQLiteProcess 
	 * Group every methods to create projects and notices
	 */
	class SQLiteProcess extends SQLiteDB {


		/*
		 * Set a new project  
		 * @params { --- }
		 * @return { --- }
		 */
		function setPersonalProject( $filename, $user, $operator, $status ) {
			$attributs = array( "name", "type", "status", "id_user" );
			$values = array( $filename, $operator, $status, $user ); 

			parent::setAttributs( "project", $attributs, $values ); 
		} /* setPersonalProject() */


		/*
		 * Set a new unique notice 
		 * @params { --- }
		 * @return { --- }
		 */
		function setPersonalNotice( $filename, $user, $date ) {
			$project = parent::getProject( $filename, $user, "id" ); 
			$attributs = array( "status", "type", "date", "text" ); 
			$values = array( "created", "project", $date, $project );  

			$notice = parent::getNotice( $project, "count" ); 
			if ( $notice == 0 ) { 
				parent::setAttributs( "notice", $attributs, $values ); 
				$notice = parent::getNotice( $project, "id" ); 

				parent::setAttributs( "user_notice", array( "id_notice", "id_user" ), array( $notice, $user ) ); 
			}
		} /* setPersonalNotice() */

	
	} /* CLASS::SQLiteProcess */ 


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
		    "title" => "L'export a e&#769choue&#769e"
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
		    "title" => "L'export est re&#769ussit", 
		    "text" => "La cre&#769ation du projet a bien fonctionne&#769. "
		  ); 

		  return $results; 
		} /* warning() */


		/*
		 * failed alert contructor 
		 * @params { --- }
		 * @return { ARRAY() }
		 */
		function failed() {
			$results = static::warning(); 
			$results["text"] = "Votre projet n'a pu e&#770tre cre&#769e&#769. <br> Veuillez re&#769essayer de nouveau."; 

			return $results; 
		} /* failed() */


		/*
		 * named alert contructor 
		 * @params { --- }
		 * @return { ARRAY() }
		 */
		function named() {
			$results = static::warning(); 
			$results["text"] = "Le nom de projet utilise&#769 existe de&#769ja&#768. <br> Veuillez modifier le nom de projet et re&#7679essayer de nouveau."; 

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

		$results = join( ", ", $results ); 
		return $results; 
	} /* setValues() */


	/*
	 * delet unconviniant filename's characters
	 * @params { STRING }
	 * @return { STRING }
	 */
	function setFilename( $file ) {
		$res = explode( " ", $file ); 
		$results = join( "_", $res ); 

		return $results; 
	} /* setFilename() */


	/*
	 * Create file's location
	 * @params { STRING }
	 * @return { STRING }
	 */
 	function setHomeDirectory( $link ) {
		$results = str_replace( "http://igeomedia.com/~odupras", "/home/odupras/public_html", $link ); 
		return $results; 
	} /* setHomeDirectory() */


	/*
	 * Create file's name
	 * @params { STRING }
	 * @return { STRING }
	 */
	function generateRandomString( $length = 10 ) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ( $i = 0; $i < $length; $i++ ) {
			$randomString .= $characters[ rand( 0, strlen( $characters ) - 1 ) ];
		}
		return $randomString;
	} /* generateRandomString() */


	/*
	 * Create file's name and location
	 * @params { STRING }
	 * @return { STRING }
	 */
	function generateFile( $link, $format ) {
		$results["file"] = generateRandomString(); 
		$results["location"] = join( "/", array( setHomeDirectory( $link ), join( ".", array( $results["file"], strtolower( $format ) ) ) ) ); 
		$results["link"] = join( "/", array( $link, join( ".", array( $results["file"], strtolower( $format ) ) ) ) );

		return $results; 
	} /* generateFile() */


	/*
	 * Open file to write data in and close it
	 * @params { STRING, STRING }
	 * @return { --- }
	 */
	function writingFile( $file, $data ) {
		$myfile = fopen( $file, "w" );
		fwrite( $myfile,  $data );
		fclose( $myfile ); 
	} /* writingFile() */ 


	/*
	 * Seek the good message displaying 
	 * @params { ARRAY(), STRING }
	 * @return { ARRAY() }
	 */
	function setMessages( $obj, $type ) {
		$message = new Messages(); 
		$results = array(); 

		if ( $type == "failed" ) {
			$res = $message->failed(); 
		} else if ( $type == "named" ) {
			$res = $message->named(); 
		} else if ( $type == "error" ) {
			$res = $message->error();
		} else if ( $type == "success" ) {
			$res = $message->success();
		}

		foreach( $res as $key => $value ) { 
			$results[$key] = $value;
		}		

		return $results; 
	} /* setMessages() */ 


	/*
	 * Organize feature's properties 
	 * @params { ARRAY() }
	 * @return { ARRAY() }
	 */
	function setFeatureProperties( $obj ) { 

		$results = $obj; 
		$results["type"] = "feature"; 
		$results["properties"] = array(); 
		foreach( $obj as $key => $value ) {
			if ( $key != "geometry" ) {
				$results["properties"][$key] = $value; 
			}
		}

		return $results; 
	} /* setFeatureProperties() */


	/*
	 * Convert features into a GeoJSON layer's features 
	 * @params { ARRAY() }
	 * @return { STRING }
	 */
	function setLayerFeatures( $obj ) { 
		$results = array( 
			"type" => "FeatureCollection", 
			"features" => array()
		); 

		foreach( $obj as $feature ) {
			$feature = setFeatureProperties( $feature ); 
			array_push( $results["features"], $feature ); 
		}

		return $results; 
	} /* setLayerFeatures() */


	$sqlite = new SQLiteDB(); 
	$results = array(); 
	$results["exist"] = $sqlite->validUser( $_POST["user"] ); 
	if ( $results['exist'] > 0 ) { 
		$user = $sqlite->getUserId( $_POST["user"] ); 
		$process = array( 
			"sqlite" => new SQLiteProcess(), 
			"postgresql" => new PostgreSQL() 
		); 

		$results["exist"] = true; 
		$results["method"] = $_POST["method"]; 
		if ( isset( $_POST["file"] ) ) {
			$filename = setFilename( $_POST["file"] ); 
		}

		if ( isset( $_POST["type"] ) ) {	
			$results["operator"] = $_POST["type"]; 
		}

		if ( $results["method"] == "export" ) {

			if ( $results["operator"] == "local" ) { 
				$file = generateFile( $_POST["link"], $_POST["format"] );
				$results["download"] = join( ".", array( $filename, strtolower( $_POST["format"] ) ) ); 
				$results["href"] = $file["link"]; 

				writingFile( $file["location"], $_POST["data"] ); 

			} else {

				if ( $results["operator"] == "personal" ) {

					if ( $sqlite->getProject( $filename, $user, "count" ) == 0 ) { 
						// SQLite section
						$process["sqlite"]->setPersonalProject( $filename, $user, $results["operator"], $_POST["status"] ); 

						if ( $sqlite->getProject( $filename, $user, "count" ) == 1 ) {

							// PostgreSQL section
							$project = $sqlite->getProject( $filename, $user, "id" ); 
							$process["postgresql"]->createProject( $project ); 

							$layer = json_decode( $_POST["data"], true ); 
							$process["postgresql"]->feature2PostGIS( $project, $layer ); 

							// SQLite section
							$process["sqlite"]->setPersonalNotice( $filename, $user, $_POST["date"] ); 

							$results = setMessages( $results, "success" ); 
						} else {
							$results = setMessages( $results, "failed" ); 
						}
					} else {
						$results = setMessages( $results, "named" ); 
					} 
				} else if ( $results["operator"] == "group" ) {
					$results = setMessages( $results, "failed" ); 
				} else if ( $results["operator"] == "public" ) {
					$results = setMessages( $results, "failed" ); 
				} 

			}
		} else if ( $results["method"] == "load" ) { 
			$project = $sqlite->getProject( $filename, $user, "id" ); 
			$features = $process["postgresql"]->loadProject( $project ); 
			$results["layer"] = setLayerFeatures( $features ); 

			$properties = $sqlite->getProjectProperties( $filename, $user ); 
			foreach( $properties as $key=>$value ) {
				$results[$key] = $value; 
			}

		} else if ( $results["method"] == "list" ) {
			$results["list"] = $sqlite->getProjectList( $user ); 
		}
	} else {
		$results = setMessages( $results, "error" ); 
	} 

	echo json_encode( $results ); 

?>