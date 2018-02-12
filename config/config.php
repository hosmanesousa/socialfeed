<?php

ob_start(); // Turns on output buffering
session_start();

$timezone = date_default_timezone_set('America/Florida');

$connect = mysqli_connect("localhost", "root","root", "social");  // Connection variable

if ( mysqli_connect_error()){
	echo "Failed to connect: " . mysqli_connect_error();
}

?>