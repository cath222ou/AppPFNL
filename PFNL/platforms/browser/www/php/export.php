<?php 

	#$_POST["data"] = '{"type":"FeatureCollection","features":[{"type":"Feature","id":1,"geometry":{"type":"Point","coordinates":[-7976034.041495287,6250280.988039817]},"properties":{"status":"validation","projectcolor":"#ffcc33","name":"Validation","blueberry-no-hsi":"","forest-stand":"Allo","bluerry_cover":"10","blueberry_fruits":"moyen","blueberry_potential":"moyen","herbaceous_name_1":"sddvc","herbaceous_cover_1":"10","herbaceous_name_2":"","herbaceous_cover_2":"","herbaceous_name_3":"","herbaceous_cover_3":"","herbaceous_name_4":"","herbaceous_cover_4":"","shrub_name_1":"asdascvds","shrub_cover_1":"25","shrub_height_1":"1","shrub_name_2":"","shrub-cover-2":"","shrub_height_2":"","shrub_name_3":"","shrub_cover_3":"","shrub_height_3":"","shrub_name_4":"","shrub_cover_4":"","shrub_heigh_4":"","cover_density":"A","cover_height":"1"}}]}'; 
	#$_POST["format"] = "GeoJSON"; 
	#$_POST["link"] = "http://igeomedia.com/~odupras/data/tmp/export"; 
	#$_POST["file"] = "1er_essaie"; 
	#$_POST["id"] = "42"; 
	#$_POST["method"] = "load"; 
	#$_POST["type"] = "share"; 
	#$_POST["status"] = "ready"; 
	#$_POST["user"] = "olivier.dupras-tessier@usherbrooke.ca"; 
	#$_POST["date"] = "14/3/2016"; 
	#$_POST["comments"] = '{"value":"d\'outils d\'export de données. "}'; 

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
		 * Get the user's id
		 * @params { STRING }
		 * @return { STRING }
		 */
		function getProjectOwner( $project ) {
			$statement = "SELECT id_user as id"; 
			$table =  "FROM project "; 
			$condition = "WHERE id_project LIKE '". $project ."'"; 
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

			if ( $_POST["type"] == "personal" ) {
				$condition = "WHERE name LIKE '". $file ."' AND id_user LIKE '". $user ."'"; 
			} else if ( $_POST["type"] == "share" ) {
				$condition = "WHERE name LIKE '". $file ."'
					AND id_project LIKE ( 
						SELECT id_project 
						FROM share 
						WHERE id_user LIKE '". $user ."' 
						AND id_project LIKE '". $_POST["id"] ."' 
					)";
			} else if ( $_POST["type"] == "public" ) {
				$condition = "WHERE name LIKE '". $file ."'
					AND type LIKE 'public'"; 
			}
			
			$request = join( " ", array( $statement, $table, $condition ) ); 

			$results = $this->query( $request ); 
			$res = $results->fetchArray( SQLITE3_ASSOC );
			
			return $res['res']; 
		} /** getProject() */ 	


		/*
		 * Get every projects's attributs required to create a new layer
		 * @params { STRING, STRING }
		 * @return { STRING }
		 */
		function getProjectProperties( $file, $user ) {
			$statement = "SELECT id_project as id, name, type, comments, status, id_user"; 
			$table =  "FROM project "; 

			if ( $_POST["type"] == "personal" ) {
				$condition = "WHERE name LIKE '". $file ."' AND id_user LIKE '". $user ."'"; 
			} else if ( $_POST["type"] == "share" ) {
				$condition = "WHERE name LIKE '". $file ."'
					AND id_project LIKE ( 
						SELECT id_project 
						FROM share 
						WHERE id_user LIKE '". $user ."' 
						AND id_project LIKE '". $_POST["id"] ."' 
					)";
			} else if ( $_POST["type"] == "public" ) {
				$condition = "WHERE name LIKE '". $file ."'
					AND type LIKE 'public'"; 
			}

			$request = join( " ", array( $statement, $table, $condition ) ); 

			$res = $this->query( $request ); 
			$results = $res->fetchArray( SQLITE3_ASSOC ); 

			if ( $results["id_user"] == $user ) {
				$results["type"] = "personal"; 
			} 

			$row = array(); 
			foreach ($results as $key => $value) {
				if ( $key != "id_user" ) {
					$row[$key] = $value; 
				}
			}

			return $row; 
		} /** getProjectProperties() */ 	


		/*
		 * List every user's projects access 
		 * @params { STRING }
		 * @return { ARRAY() }
		 */
		function getProjectList( $user ) {
			$statement = "SELECT id_project as id, name, type as optgroup, status, id_user"; 
			$table = "FROM project"; 
			$condition = "WHERE ( id_user LIKE ". $user .") 
				OR ( type LIKE 'public' 
					AND id_user NOT LIKE '". $user ."' ) 
				OR ( type LIKE 'share' 
					AND id_project LIKE ( 
						SELECT id_project FROM share WHERE id_user LIKE '". $user ."'
					)
				);"; 

			$request = join( " ", array( $statement, $table, $condition ) ); 

			$results = $this->query( $request ); 
			$row = array(); 
			$i = 0; 
			while ( $res = $results->fetchArray( SQLITE3_ASSOC ) ) { 
				
				if ( $res["id_user"] == $user ) { 
					$res["optgroup"] = "personal"; 
				}

				foreach( $res as $key=>$value ) { 
					if ( $key != "id_user" ) {
						$row[$i][$key] = $value; 
					}
				}

				$i++; 
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


		/* 
		 * Update a feature which already exist in PostgreSQL
		 * @params { ARRAY() }
		 * @return { STRING }
		 */
		function updateAttirbut( $table, $attribut, $value, $id ) {
			$statement = "UPDATE ".$table; 
			$expression = "SET ".$attribut." = ".$value; 
			$condition = "WHERE id_project = ".$id;  
			$request = join( " ", array( $statement, $expression, $condition ) ); 

			$this->exec( $request ); 
		} /* UpdateSQLITEDBProject() */ 


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
			// have to be update manually - Contain every attributs that feature can support 
			$attributs = array( 
				"gid SERIAL PRIMARY KEY", 
				"the_geom GEOMETRY", 
				"status VARCHAR(25)", 
				"name VARCHAR(125)", 
				"projectcolor VARCHAR(25)", 
				"featurecolor VARCHAR(25)", 
				"no_hsi VARCHAR(25)", 
				"forest_stand VARCHAR(25)", 
				"bluerry_cover VARCHAR(25)", 
				"blueberry_fruits VARCHAR(25)", 
				"blueberry_potential VARCHAR(25)", 
				"herbaceous_name_1 VARCHAR(25)", 
				"herbaceous_cover_1 VARCHAR(25)", 
				"herbaceous_name_2 VARCHAR(25)", 
				"herbaceous_cover_2 VARCHAR(25)", 
				"herbaceous_name_3 VARCHAR(25)", 
				"herbaceous_cover_3 VARCHAR(25)", 
				"herbaceous_name_4 VARCHAR(25)", 
				"herbaceous_cover_4 VARCHAR(25)", 
				"shrub_name_1 VARCHAR(25)", 
				"shrub_cover_1 VARCHAR(25)", 
				"shrub_height_1 VARCHAR(25)", 
				"shrub_name_2 VARCHAR(25)", 
				"shrub_cover_2 VARCHAR(25)", 
				"shrub_height_2 VARCHAR(25)", 
				"shrub_name_3 VARCHAR(25)", 
				"shrub_cover_3 VARCHAR(25)", 
				"shrub_height_3 VARCHAR(25)", 
				"shrub_name_4 VARCHAR(25)", 
				"shrub_cover_4 VARCHAR(25)", 
				"shrub_height_4 VARCHAR(25)", 
				"cover_density VARCHAR(25)", 
				"cover_height VARCHAR(25)", 
				"slope_situation VARCHAR(25)", 
				"slope_exposure VARCHAR(25)", 
				"slope_shape VARCHAR(25)", 
				"slope_tilt VARCHAR(25)", 
				"soil_drain VARCHAR(25)", 
				"soil_texture VARCHAR(125)", 
				"disturbance_ground VARCHAR(25)", 
				"disruption_source VARCHAR(25)", 
				"disturbance_age VARCHAR(25)", 
				"forest_gei VARCHAR(25)", 
				"forest_ecological_type VARCHAR(25)", 
				"first_physical_character VARCHAR(25)", 
				"second_physical_character VARCHAR(25)", 
				"blueberry_comments VARCHAR(200)", 
				"forest_comments VARCHAR(200)"
			); 
			$query = "CREATE TABLE ". $table ." ( ". join( ", ", $attributs ) ." )";  

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
			// have to be update manually - Contain every attributs that feature can support 
			$attributs = array( 
				"gid as id", 
				"ST_AsGeoJSON( the_geom ) as geometry", 
				"status", 
				"name", 
				"projectcolor", 
				"featurecolor", 
				"no_hsi", 
				"forest_stand", 
				"bluerry_cover", 
				"blueberry_fruits", 
				"blueberry_potential", 
				"herbaceous_name_1", 
				"herbaceous_cover_1", 
				"herbaceous_name_2", 
				"herbaceous_cover_2", 
				"herbaceous_name_3", 
				"herbaceous_cover_3", 
				"herbaceous_name_4", 
				"herbaceous_cover_4", 
				"shrub_name_1", 
				"shrub_cover_1", 
				"shrub_height_1", 
				"shrub_name_2", 
				"shrub_cover_2", 
				"shrub_height_2", 
				"shrub_name_3", 
				"shrub_cover_3", 
				"shrub_height_3", 
				"shrub_name_4", 
				"shrub_cover_4", 
				"shrub_height_4", 
				"cover_density", 
				"cover_height", 
				"slope_situation", 
				"slope_exposure", 
				"slope_shape", 
				"slope_tilt", 
				"soil_drain", 
				"soil_texture", 
				"disturbance_ground", 
				"disruption_source", 
				"disturbance_age", 
				"forest_gei", 
				"forest_ecological_type", 
				"first_physical_character", 
				"second_physical_character", 
				"blueberry_comments", 
				"forest_comments"
			); 
			$statement = "SELECT ".join( ", ", $attributs ); 
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
			$values = "VALUES ( ". join( ", ", $values ) .")"; 
			$query = join( " ", array( $insert, $values ) ); 

			#echo $query; 
			pg_query( $query ) or die( 'Échec de la requête : ' . pg_last_error() );
			pg_close( $conn );
		} /* setAttributs() */ 


		/* 
		 * Update a feature which already exist in PostgreSQL
		 * @params { ARRAY() }
		 * @return { STRING }
		 */
		function updateFeature ( $table, $attributs, $values, $gid ) {
			$conn = static::getConnection(); 
			$statement = "UPDATE ".$table; 
			$expression = "SET (".join( ", ", $attributs ).") = (".join( ", ", $values ).")"; 
			$condition = "WHERE gid = ".$gid;  
			$query = join( " ", array( $statement, $expression, $condition ) ); 

			pg_query( $query ) or die( 'Échec de la requête : ' . pg_last_error() );
			pg_close( $conn ); 
		} /* updateFeature() */


		/* 
		 * Convert GeoJSON feature to PostGIS feature and had it to the rigth project's table
		 * @params { ARRAY() }
		 * @return { STRING }
		 */
		function feature2PostGIS( $id, $feature, $state ) {
			$table = join( "_", array( "project", $id ) ); 
			$type = $feature["geometry"]["type"]; 
			$geom = join( "", array( "ST_SetSRID(", static::geomAsText( $type, $feature["geometry"]["coordinates"] ), ", 4326)" ) ); 

			// List of attributs and values contain in feature.properties
			$properties = array( "attributs"=>array(), "values"=>array() ); 
			foreach( $feature["properties"] as $key=>$value ) {
				if ( $key != "id" ) {
					array_push( $properties["attributs"], $key ); 
					array_push( $properties["values"], "'".str_replace( "'", "''", $value )."'" ); 
				}
			}

			if ( $state == "new" ) {
				$attributs = array( "gid", join( ", ", $properties["attributs"] ), "the_geom" ); 
				$values = array( $feature["id"], join( ", ", $properties["values"] ), $geom ); 
				static::setAttributs( $table, $attributs, $values ); 
			} else if ( $state == "exist" ) { 
				$attributs = array( join( ", ", $properties["attributs"] ), "the_geom" ); 
				$values = array( join( ", ", $properties["values"] ), $geom ); 
				static::updateFeature( $table, $attributs, $values, $feature["id"] ); 
			} 
					
		} /* feature2PostGIS() */ 


		/*
		 * Delete a row in a PostgreSQL table  
		 * @params { STRING, STRING }
		 * @return { --- }
		 */
		function deleteRow ( $id, $gid ) {
			$conn = static::getConnection(); 
			$statement = "DELETE"; 
			$table = "FROM ".join( "_", array( "project", $id ) ); 
			$condition = "WHERE gid = ".$gid; 

			$query = join( " ", array( $statement, $table, $condition ) ); 

			pg_query( $query ) or die( 'Échec de la requête : ' . pg_last_error() );
			pg_close( $conn );
		} /* deleteRow() */


		/* 
		 * Get every feature avaible in a table
		 * @params { STRING }
		 * @return { ARRAY() }
		 */
		function getFeatures( $id, $state ) { 
			$conn = static::getConnection(); 

			if ( $state == "id" ) {
				$statement = "SELECT gid as id"; 
			} else if ( $state == "max" ) {
				$statement = "SELECT max( gid ) as max"; 
			}

			$table = "FROM ".join( "_", array( "project", $id ) ); 
			$query = join( " ", array( $statement, $table ) );  

			$res = pg_query( $query ) or die( 'Échec de la requête : ' . pg_last_error() );

			if ( $state == "id" ) {
				$results = array(); 
				$row = pg_fetch_all( $res ); 
				foreach ( $row as $feature ){
					array_push( $results, $feature["id"] ); 
				}			

			} else if ( $state == "max" ) {
				$results = pg_fetch_result( $res, 0, 0); 

			}

			pg_close( $conn );

			return $results; 
		} /* getFeatures() */


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
		function setPersonalProject( $filename, $user, $operator, $status, $comments ) {
			$attributs = array( "name", "type", "status", "comments", "id_user" ); 
			$values = array( $filename, $operator, $status, str_replace( "'", "''", $comments), $user ); 

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
		function danger() {
			$results = array( 
				"exist" => false,  
				"type" => "warning",  
		    "title" => "L&#39export n&#39est pas autoris&#769e"
		  ); 

		  return $results; 
		} /* warning() */ 


		/*
		 * warning alert contructor 
		 * @params { --- }
		 * @return { --- }
		 */
		function warning() {
			$results = array( 
				"exist" => false,  
				"type" => "warning",  
		    "title" => "L&#39export a e&#769choue&#769e"
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
		    "title" => "L&#39export est re&#769ussit" 
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
				$results["text"] = "La mise a&#768 jour du projet s&#39est bien de&#769roule&#769"; 

			} else if ( $state == "warning" ) {
				$results = static::warning(); 
				$results["text"] = "Un proble&#768me est survenue lors de la mise a&#768 jour du projet. <br> Veuillez re&#769essayer plus tard. "; 
			} else if ( $state == "danger" ) {
				$results = static::danger(); 
				$results["text"] = "Vous n&#39e&#770tes pas autoris&#769 a&#768 modifier le contenu de ce projet. "; 
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
		$results = str_replace( "http://localhost", "C:\ms4w\Apache\htdocs", $link ); 
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
	function setMessages( $obj, $message ) {
		$results = $obj; 
		foreach( $message as $key => $value ) { 
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
				if ( $key == "id" ) {
					$value = intval( $value ); 
				}
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


	/*
	 * Get the update method of each feature receiving from client 
	 * @params { ARRAY(), INT, ARRAY() }
	 * @return { ARRAY() }
	 */
	function feature2update( $postgres, $max, $client, $process, $id ) {
		$results = array( "exist" => array(), "delete" => array(), "new" => array() ); 

		// Valid the existing fid and the new fid
		foreach ( $client["features"] as $feature ) {
			if ( $feature["id"] <= $max ) { // If client's feature is already in the DB
				if ( in_array( $feature["id"], $postgres ) ) { // If client's feature still exist in the DB
					array_push( $results["exist"], $feature["id"] ); 
					$process->feature2PostGIS( $id, $feature, "exist" ); 
				} 
			} else { // if client's feature is new
				array_push( $results["new"], $feature["id"] ); 
				$process->feature2PostGIS( $id, $feature, "new" ); 
			}
		}

		// Get the difference between features which already exist in PostgreSQL and those from client. It will reject, of course, those who are news
		$results["delete"] = array_diff( $postgres, $results["exist"] );
		foreach ( $results["delete"] as $feature ) { // If the feature is been deleted from client
			$process->deleteRow( $id, $feature["id"] );
		}

		return $results; 
	}	/* feature2update() */


	$message = new Messages(); 
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

		if ( isset( $_POST["comments"] ) ) {
			$comments = json_decode( $_POST["comments"], true ); 
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
						$process["sqlite"]->setPersonalProject( $filename, $user, $results["operator"], $_POST["status"], $comments["value"] ); 

						if ( $sqlite->getProject( $filename, $user, "count" ) == 1 ) {

							// PostgreSQL section
							$project = $sqlite->getProject( $filename, $user, "id" ); 
							$process["postgresql"]->createProject( $project ); 

							$layer = json_decode( $_POST["data"], true );
                            
							foreach ( $layer["features"] as $feature ) {
								$process["postgresql"]->feature2PostGIS( $project, $feature, "new" ); 
							}

							// SQLite section
							$process["sqlite"]->setPersonalNotice( $filename, $user, $_POST["date"] ); 

							$results = setMessages( $results, $message->create( "success" ) ); 
						} else {
							$results = setMessages( $results, $message->create( "failed" ) ); 
						}
					} else {
						$results = setMessages( $results, $message->named() ); 
					} 
				} else if ( $results["operator"] == "group" ) {
					$results = setMessages( $results, $message->create( "failed" ) ); 
				} else if ( $results["operator"] == "public" ) {
					$results = setMessages( $results, $message->create( "failed" ) ); 
				} 
			}
		} else if ( $results["method"] == "load" ) { 
			$project = $sqlite->getProject( $filename, $user, "id" ); 
			$features = $process["postgresql"]->loadProject( $project ); 
			$results["pid"] = $project; 
			$results["layer"] = setLayerFeatures( $features ); 

			$properties = $sqlite->getProjectProperties( $filename, $user ); 
			foreach( $properties as $key=>$value ) {
				$results[$key] = $value; 
			}

		} else if ( $results["method"] == "list" ) {
			$results["list"] = $sqlite->getProjectList( $user ); 

		} else if ( $results["method"] == "update" ) {
			
			$owner = $sqlite->getProjectOwner( $_POST["id"] ); 
			if ( ( $_POST["type"] != "public" ) || ( $_POST["type"] != "public" && $owner == $user ) ) {

				// Update SQLITEDB
				$project = $sqlite->getProject( $filename, $user, "id" ); 
				$sqlite->updateAttirbut( "project", "comments", "'".str_replace( "'", "''", $comments["value"] )."'", $project ); 

				// Update PostgreSQL 
				$features = $process["postgresql"]->getFeatures( $_POST["id"], "id" );
				$max = $process["postgresql"]->getFeatures( $_POST["id"], "max" );
				$layer = json_decode( $_POST["data"], true ); 
				$update = feature2update( $features, $max, $layer, $process["postgresql"], $_POST["id"] ); 
				#print_r( $update ); 

				$results = setMessages( $results, $message->update( "success" ) );
			} else {
				$results = setMessages( $results, $message->update( "danger" ) );
			} 
		}	
	} else {
		$results = setMessages( $results, $message->error() ); 
	} 

	echo json_encode( $results ); 

?>