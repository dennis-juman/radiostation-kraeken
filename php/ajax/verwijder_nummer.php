<?php
session_start();
if(isset($_POST['nummer_id'])){
    $nummer_id = $_POST['nummer_id'];
}

//MAAK VERBINDING MET DE DATABASE
require '../class/dbconnection.php';
$dbh = new dbconnection();

//VERWIJDER PROGRAMMA
$query = $dbh->connect()->prepare("DELETE FROM song WHERE id = ?");
$query->bindParam(1, $nummer_id, PDO::PARAM_INT);
$query->execute();

//UPDATE PROGRAMMAOVERZICHT
$query = $dbh->connect()->prepare("SELECT id AS nummer_id, omschrijving AS programma_omschrijving, slogan AS programma_slogan FROM programma");
$query->execute();
$uitzending_informatie = $query->fetchAll(PDO::FETCH_ASSOC);