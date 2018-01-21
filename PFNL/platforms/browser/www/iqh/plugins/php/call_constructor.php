<?php
	
	#$_POST["value"] = "5"; 
	#$_POST["name"] = "igeomedia.com/~odupras/process/python/addIQH_v1.3.3"; 


	$location = str_replace( "igeomedia.com/~odupras", "/home/odupras/public_html", $_POST["name"] ); 
	$cmd = "python";
	$script = "main.py";
	$path = join( "/", array( $location, $script ) ); 
	$request = join( " ", array( $cmd, $path, $_POST["value"] ) ); 

	#echo $request; 
	exec($request);
?>