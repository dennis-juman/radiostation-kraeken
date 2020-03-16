<?php
session_start();
if(empty($_POST)){
    die("Je hebt geen toegang tot deze pagina.");
}

if(!isset($_POST['zendernaam_toevoegen']) || empty($_POST['zendernaam_toevoegen'])){
    echo "<div>Je moet een zendernaam opgeven.</div>";
    die();
}

if(!isset($_POST['zenderslogan_toevoegen']) || empty($_POST['zenderslogan_toevoegen'])){
    echo "<div>Je moet een zender slogan opgeven.</div>";
    die();
}

//SLA POST DATA OP IN VARIABELEN
$zender_toevoegen = $_POST['zendernaam_toevoegen'];
$zender_slogan = $_POST['zenderslogan_toevoegen'];

//MAAK VERBINDING MET DE DATABASE
require 'class/dbconnection.php';
$dbh = new dbconnection();

//CONTROLEER HOVEEL ZENDERS ER NOG ZIJN
$query = $dbh->connect()->prepare("INSERT INTO zender (omschrijving, slogan) VALUES (?, ?)");
$query->bindParam(1, $zender_toevoegen, PDO::PARAM_STR, 99);
$query->bindParam(2, $zender_slogan, PDO::PARAM_STR, 455);
$query->execute();
header('Location: ../zenderoverzicht.php');