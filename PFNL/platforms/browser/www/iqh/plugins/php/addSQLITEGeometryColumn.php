<?php

	class MyDB extends SQLite3 {
    function __construct() {
      $this->open('mysqlitedb.db');
    }
	}

	$geometry = array( 
		"POINT" => array( "point", 4326, "POINT", "XY", 0), 
		"LINESTRING" => array( "linestring", 4326, "LINESTRING", "XY", 0), 
		"POLYGON" => array( "polygon", 4326, "POLYGON", "XY", 0)
	); 

	$db = new MyDB();
	foreach( $geometry as $geom ) {
		$request = "SELECT AddGeometryColumn('project', ". join( ", ", $geom ) .");"; 
		$db->exec( $request );
	}

?>