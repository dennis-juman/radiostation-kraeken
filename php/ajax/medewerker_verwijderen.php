<?php
    //CONTROLEER OF DE GEBRUIKER TOEGANG HEEFT TOT DE PAGINA
    if(!isset($_POST['id'])){
        die("<div class='error'>Je hebt geen toegang tot deze pagina.</div>");
    }

    $id = $_POST['id'];


    //MAAK VERBINDING MET DE DATABASE
    require '../class/dbconnection.php';
    $dbh = new dbconnection();

    //WIJZIG DE ZENDER
    $query = $dbh->connect()->prepare("DELETE FROM medewerker WHERE id = ?");
    $query->bindParam(1, $id, PDO::PARAM_INT);
    $query->execute();