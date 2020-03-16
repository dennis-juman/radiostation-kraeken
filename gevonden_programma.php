<?php //DE HEADER, DE LOGO, DE NAVBAR
  require_once 'layout/head.php';
?>


<?php

if(!isset($_GET['zoek-programma-knop'])){
    header('Location: zenderoverzicht.php');
    die();
}

if(isset($_GET['zoek-programma-invoer'])){
    if(empty($_GET['zoek-programma-invoer'])){
        die("<div class='geen-zoekterm-container'><div>Je invoer is leeg.</div></div>");
    }
}

    //LOAD NAVBAR AND BRAND
    require_once 'layout/brand.php';
    require_once 'layout/navbar.php';


//DB CONNECTION
require 'php/class/dbconnection.php';
$dbh = new dbconnection();


//REMOVE TAGS FROM USER INPUT AND PREPARE LIKE WILDCARD
$programma_zoekopdracht = '%' . strip_tags($_GET['zoek-programma-invoer']) . '%';

//SEND QUERY TO THE DB
$query = $dbh->connect()->prepare("SELECT programma.id AS programma_id,
                                          programma.omschrijving AS programma_omschrijving, 
                                          zender.omschrijving AS zender_omschrijving 
                                   FROM programma 
                                   JOIN uitzending ON uitzending.programma_id = programma.id
                                   JOIN zender ON zender.id = uitzending.zender_id
                                   WHERE programma.omschrijving 
                                   LIKE ?");
$query->bindParam(1, $programma_zoekopdracht, PDO::PARAM_STR, 455);
$query->execute();
$search_results = $query->fetchAll(PDO::FETCH_ASSOC);

//CONTROLEER OF ER PROGRAMMA'S GEVONDEN ZIJN
if(empty($search_results)){
    die("<div class='geen-zoekterm-container'><div>Geen programma's gevonden.</div></div>");
}

// PROGRAMMAOVERZICHT TABEL
$html_content = "<div class='programma-zoekopdracht-container'>
                    <div class='programma-zoekopdracht-header'>Gevonden programma’s</div>
                        <table class='table table-striped'>
                            <tbody id='ajax-programmaoverzicht'>
                                <thead>
                                    <tr>
                                        <th scope='col'>Zender</th>
                                        <th scope='col'>Programma</th>
                                    </tr>
                                </thead>";
        //PLAATS DE PROGRAMMAOVERZICHT DATA IN DE TABEL CELLEN
        foreach($search_results as $search_result){
            //PROGRAMMA TABEL TUPLES AANMAKEN
            $html_content .= "<tr>
                                <td>{$search_result['zender_omschrijving']}</td>
                                <td>{$search_result['programma_omschrijving']}</td>
                                <td>" . (isset($_SESSION['login_username']) ? '<span id="' . $search_result['programma_id'] . '"' . 'onclick="openDetailoverzicht(this.id)" style="color:orange;cursor:pointer">detailoverzicht</span>' : null) . "</td>
                            </tr>";
        } 
echo $html_content .= "</tbody>
                    </table>
                </div>";
?>

<script>
    function openDetailoverzicht(programma_id){

    //CONTROLEER OF ER AL EEN DETAILOVERZICHT TABEL IS INGELADEN
    if(document.getElementsByClassName("programma-container")[0]){
        document.getElementsByClassName("programma-container")[0].remove();
    } else

    //CHECK OF HET DETAILOVERZICHT VAN HET GEKLIKTE PROGRAMMA AL INGELADEN IS
    if(document.getElementById("programma_" + programma_id)){
        document.getElementById("programma_" + programma_id).remove();
    } else

    //CREATE NEW AJAX REQUEST
    var ajaxRequest = new XMLHttpRequest();

    //SEND THE REQUEST TO THE SERVER
    ajaxRequest.open("POST", "php/ajax/detailoverzicht.php", true); //SENDING METHOD
    ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); //HTTP HEADER TO SEND POST DATA
    ajaxRequest.send(`programma_id=${programma_id}`); //SEND REQUEST

        //CHECK IF REQUEST STATUS CHANGED
        ajaxRequest.onreadystatechange = function(){
            if (this.readyState == 4 && this.status == 200){ //CHECK IF SERVER RESPONSE IS READY
                // document.body.innerHTML = this.responseText;
                document.body.insertAdjacentHTML('beforeend', this.responseText);
            }
        };
    }
</script>

<!-- FOOTER -->
<?php
  require_once 'layout/footer.php';
?>