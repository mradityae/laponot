<?php

	try
	{
		$server = "localhost";
		$database = "db_laporan_notaris";
		// $user = "kabayan";
		// $password = "S!pf7WuDjhW%7pb(";
    	$user = "root";
		$password = "";
		$koneksi = new PDO("mysql:host=$server;dbname=$database",$user,$password,array(PDO::MYSQL_ATTR_FOUND_ROWS=>true));

	}catch(PDOException $e)
	{

	}

	$url = "/laponot/";
	$url_root = "https://kabayanpasti.kemenkum.go.id/";
	error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);
?>