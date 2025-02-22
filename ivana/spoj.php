<?php
// Datoteka: spoj.php

$host = "localhost";
$user = "root";
$password = "";
$database = "ivana";

$spoj = new mysqli($host, $user, $password, $database);

if ($spoj->connect_error) {
    die("Greška pri spajanju na bazu: " . $spoj->connect_error);
}
?>
