<?php
	function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
	$path="/home/odupras/public_html/";
	$process=$_POST['process'];
	$dir="process/python/"."$process"."/";
	$file=generateRandomString();
	$file="$path"."$dir"."tmp/"."$file";
	$myfile=fopen($file, "w");
	$jsondata=$_POST['user'];
	fwrite($myfile,  $jsondata);
	fclose($myfile);
	$cmd="python ";
	$script="main.py ";
	$request="$cmd"."$path"."$dir"."$script"."$file";
	system($request);
?>