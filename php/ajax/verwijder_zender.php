<?php
    session_start();
    if(isset($_POST['zender_id'])){
        $zender_id = $_POST['zender_id'];
    }

    //MAAK VERBINDING MET DE DATABASE
    require '../class/dbconnection.php';
    $dbh = new dbconnection();

    //CONTROLEER HOVEEL ZENDERS ER NOG ZIJN.
    $query = $dbh->connect()->prepare("SELECT id FROM zender");
    $query->execute();
    if(count($query->fetchAll(PDO::FETCH_ASSOC)) == 1){
        echo '<div class="geen-zenders">Er zijn momenteel geen zenders zichbaar.</div>';
    }

    //VERWIJDER DE ZENDER
    $query = $dbh->connect()->prepare("DELETE FROM zender WHERE id = ?");
    $query->bindParam(1, $zender_id, PDO::PARAM_INT);
    $query->execute();

    //UPDATE DE ZENDEROVERZICHT
    $query = $dbh->connect()->prepare("SELECT id AS zender_id, omschrijving AS zender_omschrijving, slogan AS zender_slogan FROM zender");
    $query->execute();
    $zenders = $query->fetchAll(PDO::FETCH_ASSOC);
    $html = '';
    for($i = 0; $i < count($zenders); $i++){
        //CREATE A <TR> FOR EACH 3RD ELEMENT
        if($i % 3 == 0){
            $html .= "<tr>";
        }
        //APPEND <TR> TO TBODY
        $html .= "<td class='zender'>" . $zenders[$i]['zender_omschrijving'] . '<br/>' 
                . $zenders[$i]['zender_slogan'] . '<br/><br/>'
                . "<a href='programmaoverzicht.php?{$zenders[$i]['zender_omschrijving']}'>programmaoverzicht</a>" 
                . (isset($_SESSION['login_username']) ? '<br/><span class="weizig-zender">wijzig</span> | ' : null) 
                . (isset($_SESSION['login_username']) ? '<span id="' . $zenders[$i]['zender_id'] . '"' . 'onclick="verwijderZender(this.id)" class="verwijder-zender">verwijder</span>' : null) . "</td>";
    } echo $html .= "</tr>";
