<?php
	$host = "localhost";
	$userMS = "garlasl1";
	$passwordMS = "9008004";
	$database = "garlasl1_IN612";
				
	//Tries to connect to mySQLi
	$connection = mysqli_connect($host, $userMS, $passwordMS, $database) or die("Couldn't connect");
?>