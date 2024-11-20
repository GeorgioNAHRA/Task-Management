<?php

$hostname = "localhost";
$username = "root";
$password = "";
$database = "gate_data";

$connection = mysqli_connect($hostname, $username, $password, $database);

if (!$connection) {
    die("Erreur de connexion à la base de données: " . mysqli_connect_error());
}
?>