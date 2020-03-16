<?php
session_start();
if(isset($_POST['programma_id'])){
    $programma_id = $_POST['programma_id'];
}

//MAAK VERBINDING MET DE DATABASE
require '../class/dbconnection.php';
$dbh = new dbconnection();

//VERWIJDER PROGRAMMA
$query = $dbh->connect()->prepare("DELETE FROM programma WHERE id = ?");
$query->bindParam(1, $programma_id, PDO::PARAM_INT);
$query->execute();

//UPDATE PROGRAMMAOVERZICHT
$query = $dbh->connect()->prepare("SELECT id AS programma_id, omschrijving AS programma_omschrijving, slogan AS programma_slogan FROM programma");
$query->execute();
$uitzending_informatie = $query->fetchAll(PDO::FETCH_ASSOC);
?>