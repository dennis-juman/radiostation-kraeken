<?php
session_start();
if(empty($_POST)){
    die("Je hebt geen toegang tot deze pagina.");
}

if(!isset($_POST['artiestnaam_toevoegen']) || empty($_POST['artiestnaam_toevoegen'])){
    echo "<div>Je moet een artiestennaam opgeven.</div>";
    die();
}

if(!isset($_POST['nummer_toevoegen']) || empty($_POST['nummer_toevoegen'])){
    echo "<div>Je moet een nummer opgeven.</div>";
    die();
}

//SLA POST DATA OP IN VARIABELEN
$artiestnaam_toevoegen = $_POST['artiestnaam_toevoegen'];
$nummer_toevoegen = $_POST['nummer_toevoegen'];

//MAAK VERBINDING MET DE DATABASE
require 'class/dbconnection.php';
$dbh = new dbconnection();

try{
    //CONTROLEER HOVEEL ZENDERS ER NOG ZIJN
    $query = $dbh->connect()->prepare("INSERT INTO artiest (artiestennaam) VALUES (?)");
    $query->bindParam(1, $artiestnaam_toevoegen, PDO::PARAM_STR, 99);
    $query->execute();
    $artiestnaam_id = $dbh->connect()->lastInsertId();
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}


try{
    $query = $dbh->connect()->prepare("INSERT INTO song (titel) VALUES (?)");
    $query->bindParam(1, $nummer_toevoegen, PDO::PARAM_STR, 99);
    $query->execute();
    $nummer_id = $dbh->connect()->lastInsertId();
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

$query = $dbh->connect()->prepare("INSERT INTO artiest_has_song (artiest_id, song_id) VALUES (?, ?)");
$query->bindParam(1, $artiestnaam_id, PDO::PARAM_INT);
$query->bindParam(2, $nummer_toevoegen, PDO::PARAM_INT);
$query->execute();

echo "<a href=\"javascript:history.go(-1)\">Nummer is toegevoegd.<br/>Druk hier om terug te gaan.</a>";