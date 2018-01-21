<?php

	#$_POST["id"] = 3; 

	class MyDB extends SQLite3 {
    function __construct() {
      $this->open( 'mysqlitedb.db' );
    }
	}


	/*
	 * Find every data needed to build html select element to search IQH by forest species
	 * @params { SQLiteDB }
	 * @return { array() }
	 */
	function getAll( $db ) {
		$statement = "SELECT iqh.exist as iExist, iqh.id_iqh as iId, iqh.date as iDate, region.name as rName, user.first_name as uFname, user.last_name as uLname, user.company as uCompany , espece.name as eName, espece.id_espece as eId, pfnl.name as pName, pfnl.id_pfnl as pId";  
		$table =  "FROM iqh, espece, pfnl, region, user"; 
		$condition = "WHERE iqh.id_espece = espece.id_espece AND espece.id_pfnl = pfnl.id_pfnl AND iqh.id_region = region.id_region AND iqh.id_user = user.id_user;"; 
		$request = join( " ", array( $statement, $table, $condition ) ); 
		#echo "\n".$request."\n"."\n"; 
		$row = $db -> query( $request ); 		
	
		/** Fetch results from SQLite request */
	  $results = array(); 
	  $count = 0; 
		while ( $res = $row -> fetchArray( SQLITE3_ASSOC ) ) { 
			if ( $res["iExist"] == 1 ) { 
				$results[$count]["iId"] = strval( $res["iId"] ); 
				$results[$count]["iName"] = join("", array( "iqh", strval( $res["iId"] ) ) ); 
				$results[$count]["iDate"] = $res["iDate"]; 
				$results[$count]["rName"] = $res["rName"]; 
				$results[$count]["uName"] = join(" ", array( $res["uFname"], $res["uLname"] ) ) ;
				$results[$count]["uCompany"] = $res["uCompany"]; 
				$results[$count]["eName"] = $res["eName"]; 
				$results[$count]["eId"] = strval( $res["eId"] ); 
				$results[$count]["pName"] = $res["pName"]; 
				$results[$count]["pId"] = strval( $res["pId"] ); 
			}
			$count ++; 
		}	

		return $results; 
	} /** getAll() */


	$db = new MyDB();
	$results = array(); 
	if ( isset( $_POST["id"] ) ) {
		$tmp = 0; 
	} else {
		$results = getAll( $db ); 
		#print_r( $results ); 
	}

	echo json_encode( $results ); 

?>
