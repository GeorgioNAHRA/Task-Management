<?php

$hostname = "localhost";
$username = "root";
$password = "";
$database = "mnb_data";

$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) {
    die("Erreur de connexion à la base de données: " . mysqli_connect_error());
}
?>