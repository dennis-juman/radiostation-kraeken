<?php 
    require_once 'layout/head.php';

    //CONTROLEER OF DE GEBRUIKER TOEGANG HEEFT TOT DE PAGINA
    if(!isset($_SESSION['login_username'])){
        die("Je hebt geen toegang tot deze pagina.");
    }

    require_once 'layout/brand.php';
    require_once 'layout/navbar.php';



    //VERBIND MET DE DATABASE
    require 'php/class/dbconnection.php';
    $dbh = new dbconnection();


    //TITEL VAN DE PAGINA
    echo "<div class='medewerker-form-caption'><div>Medewerkers beheren</div></div>";


    //HAAL ALLE MEDEWERKERS OP VANUIT DE DATABASE
    $query = $dbh->connect()->prepare('SELECT voornaam, achternaam, id FROM medewerker');
    $query->execute();
    $items = $query->fetchAll(PDO::FETCH_ASSOC);
    if(empty($items)){
        echo "<div class='geen-medewerkers form-groep'><div>Er zijn nog geen medewerkers.</div></div>";
    }



    //MEDEWERKERS TABEL
    $html_content = "<div id='medewerker-container'><table class='table gestreepte-tabel table-striped'><tbody>";
    foreach($items as $item){
        $html_content .= "<tr><td><span id=voornaam_{$item['id']}>{$item['voornaam']}</span> <span id=achternaam_{$item['id']}>{$item['achternaam']}</span></td>
                              <td style='cursor:pointer;' class='links' id=wijzig_medewerker_{$item['id']} onclick='medewerkerWijzigen(this.id)'>wijzig</td>
                              <td style='cursor:pointer;' class='links' id=verwijder_medewerker_{$item['id']} onclick='medewerkerVerwijderen(this.id)'>verwijder</td>
                         </tr>";
    }
    echo $html_content .= "</tbody></table></div>";

?>

<!-- INVOER -->
<div class="invoer-container">
    <div class="form-group form-groep">
        <label>Voornaam</label>
        <input id="voornaam" type="text" class="form-control">
    </div>
    <div class="form-group form-groep">
        <label>Tussenvoegsel</label>
        <input id="tussenvoegsel" type="text" class="form-control">
    </div>
    <div class="form-group form-groep">
        <label>Achternaam</label>
        <input id="achternaam" type="text" class="form-control">
    </div>
    <div class="form-group form-groep">
        <label>Wachtwoord</label>
        <input id="wachtwoord" type="text" class="form-control">
        <button onclick="medewerkerToevoegen()" type="button" class="verstuur-knopje btn btn-primary">Verstuur</button>
    </div>

</div>


<script>



    function annuleerMedewerkerWijziging(medewerker_id){
        // KOSTE ME TEVEEL TIJD OM EEN 'ANNULERING FUNCTIE TE MAKEN WANT, IK HEB NIET ZOVEEL TIJD, DUS KWAM MET EEN SIMPEL ALTERNATIEF'
        // let zender = document.getElementById(medewerker_id);
        // zender.setAttribute('onclick','medewerkerWijzigen(this.id)');
        // zender.innerHTML = "wijzig";

        //   let id = medewerker_id.replace(/\D/g,'');
        //   let verstuurZenderWijziging = document.getElementById(id);
        //   verstuurZenderWijziging.setAttribute('onclick','verwijderZender(this.id)');
        //   verstuurZenderWijziging.innerHTML = "verwijder";
        location.reload();
    }




    //FUNCTIE OM EEN MEDEWERKER TE WIJZIGEN
    function medewerkerWijzigen(medewerker_id){

        //MAAK KNOP OM ZENDER WIJZIGING AANVRAAG TE ANNULEREN
        let annuleerWijziging = document.getElementById(medewerker_id);
        annuleerWijziging.setAttribute('onclick','annuleerMedewerkerWijziging(this.id)');
        annuleerWijziging.innerHTML = "annuleer";

        //KNOP OM DE WIJZIGINGEN OP TE SLAAN
        let id = medewerker_id.replace(/\D/g,''); //STRIP ALLE NON-NUMMERIEKE WAARDES
        let wijzigingOpslaan = document.getElementById(`verwijder_medewerker_${id}`);
        wijzigingOpslaan.setAttribute('onclick', `medewerkerWijzigenOpslaan(id)`);
        wijzigingOpslaan.innerHTML = "opslaan";



        //STRIP ALLE NON-NUMMERIEKE WAARDES
        let Id = medewerker_id.replace(/\D/g,'');

        //VOEG TEKSTVAKKEN TOE ZODAT DE GEBRUIKER ZIJN NIEUWE DATA KAN INVOEREN
        let voornaamId = document.getElementById("voornaam_" + Id);
        let achternaamId = document.getElementById("achternaam_" + Id);      

        //MAAK EEN TEKSTVAK AAN
        let voornaamTekstvak = document.createElement("input");
        let achternaamTekstvak = document.createElement("input");

        //CREEER ATTRIBUTEN VOOR TEKSTVAK
        voornaamTekstvak.setAttribute("id", voornaamId.getAttribute('id')); //HAAL DE NAAM VAN DE ID OP
        achternaamTekstvak.setAttribute("id", achternaamId.getAttribute('id')); //HAAL DE NAAM VAN DE ID OP
        voornaamTekstvak.setAttribute("type", "text");
        achternaamTekstvak.setAttribute("type", "text");
        voornaamTekstvak.setAttribute("placeholder", voornaamId.innerText);
        achternaamTekstvak.setAttribute("placeholder", achternaamId.innerText);

        //PLAATS DE TEKSTVAKKEN OP DE PAGINA
        voornaamId.replaceWith(voornaamTekstvak);
        achternaamId.replaceWith(achternaamTekstvak);
    }


    //FUNCTIE OM WIJZIGINGEN OP TE SLAAN
    function medewerkerWijzigenOpslaan(medewerker_id){

    //STRIP ALLE NON-NUMMERIEKE WAARDES
    let mId = medewerker_id.replace(/\D/g,'');

    let voornaamInvoer = document.getElementById("voornaam_" + mId).value;
    let achternaamInvoer = document.getElementById("achternaam_" + mId).value;

    //CREATE NEW AJAX REQUEST
    var ajaxRequest = new XMLHttpRequest();

    //SEND THE REQUEST TO THE SERVER
    ajaxRequest.open("POST", "php/ajax/medewerker_bewerken.php", true); //SENDING METHOD
    ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); //HTTP HEADER TO SEND POST DATA
    ajaxRequest.send(`voornaam=${voornaamInvoer}&achternaam=${achternaamInvoer}&id=${mId}`); //SEND REQUEST

    //CHECK IF REQUEST STATUS CHANGED
    ajaxRequest.onreadystatechange = function(){
        if (this.readyState == 4 && this.status == 200){ //CHECK IF SERVER RESPONSE IS READY
            location.reload();
            }
        };
    }




    //FUNCTIE OM MEDEWERKERS TE VERWIJDEREN
    function medewerkerVerwijderen(medewerker_id){

      //STRIP ALLE NON-NUMMERIEKE WAARDES
      let id = medewerker_id.replace(/\D/g,'');
        
            //CREATE NEW AJAX REQUEST
            var ajaxRequest = new XMLHttpRequest();

            //SEND THE REQUEST TO THE SERVER
            ajaxRequest.open("POST", "php/ajax/medewerker_verwijderen.php", true); //SENDING METHOD
            ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); //HTTP HEADER TO SEND POST DATA
            ajaxRequest.send(`id=${id}`); //SEND REQUEST

            //CHECK IF REQUEST STATUS CHANGED
            ajaxRequest.onreadystatechange = function(){
            if (this.readyState == 4 && this.status == 200){ //CHECK IF SERVER RESPONSE IS READY
                location.reload();
            }
        };
    }

    //FUNCTIE OM MEDEWERKERS TOE TE VOEGEN
    function medewerkerToevoegen(){
        //HAAL DE WAARDES OP
        let voornaam = document.getElementById("voornaam").value;
        let tussenvoegsel = document.getElementById("tussenvoegsel").value;
        let achternaam = document.getElementById("achternaam").value;
        let wachtwoord = document.getElementById("wachtwoord").value;

            //CREATE NEW AJAX REQUEST
            var ajaxRequest = new XMLHttpRequest();

            //SEND THE REQUEST TO THE SERVER
            ajaxRequest.open("POST", "php/ajax/medewerker_toevoegen.php", true); //SENDING METHOD
            ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); //HTTP HEADER TO SEND POST DATA
            ajaxRequest.send(`voornaam=${voornaam}&tussenvoegsel=${tussenvoegsel}&achternaam=${achternaam}&wachtwoord=${wachtwoord}`); //SEND REQUEST

            //CHECK IF REQUEST STATUS CHANGED
            ajaxRequest.onreadystatechange = function(){
            if (this.readyState == 4 && this.status == 200){ //CHECK IF SERVER RESPONSE IS READY
                location.reload();
            }
        };
    }
</script>

<!-- FOOTER -->
<?php
  require_once 'layout/footer.php';
?>