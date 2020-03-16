<?php //DE HEADER, DE LOGO, DE NAVBAR
  require_once 'layout/head.php';
  require_once 'layout/brand.php';
  require_once 'layout/navbar.php';
?>


<!-- HET ONDERDEEL OM ZENDERS TOE TE VOEGEN -->
<?php if(isset($_SESSION['login_username'])){ ?>
  <div class="zender-form-container">
      <form method="POST" action="php/zender_toevoegen.php">
      <div class="zender-form-caption">Zender toevoegen</div>
          <div class="form-group">
              <label for="exampleInputEmail1">Zender</label>
              <input name="zendernaam_toevoegen" type="text" class="form-control zender-input">
          </div>
          <br/>
          <div class="form-group">
              <label for="exampleInputPassword1">Omschrijving</label>
              <input name="zenderslogan_toevoegen" type="text" class="form-control zender-input">
          </div>
          <button name="zender_toevoegen_verstuurd" type="submit" class="btn btn-primary zender-submit-button">Verstuur</button>
      </form>
  </div>

<!-- EEN LIJNTJE OM DE CONTENT VAN ELKAAR TE SCHEIDEN -->
<div class="dropdown-divider content-devider"></div>
<?php } ?>







<!-- ONDERDEEL VAN HET ZENDEROVERZICHT -->
<div class="zenderoverzicht">
  <div class="zenderoverzicht-titel">Zenderoverzicht</div>
    <table class="table table-striped">
      <tbody id="ajax-zenderoverzicht">
          <?php
            //FETCH ZENDEROVERZICHT DATA
            require 'php/class/dbconnection.php';
            $dbh = new dbconnection();

            $query = $dbh->connect()->prepare("SELECT id AS zender_id, omschrijving AS zender_omschrijving, slogan AS zender_slogan FROM zender");
            $query->execute();
            $zenders = $query->fetchAll(PDO::FETCH_ASSOC);

            //KIJK OF ER UBERHAUPT ZENDERS ZIJN EN GEEF ANDERS EEN MELDING.
            if(empty($zenders)){
              echo '<div class="geen-zenders">Er zijn momenteel geen zenders zichbaar.</div>';
            }

            //ZENDEROVERZICHT TABEL / TABLE
            $html = '';
            for($i = 0; $i < count($zenders); $i++){
                //CREATE A <TR> FOR EACH 3RD ELEMENT
                if($i % 3 == 0){
                  $html .= "<tr>";
                }
                //APPEND <TR> TO TBODY
                $html .= "<td class='zender'><span id='zender_omschrijving_" . $zenders[$i]['zender_id'] . "'>" . $zenders[$i]['zender_omschrijving'] ."</span><br/>"
                      .  "<span id='zender_slogan_" . $zenders[$i]['zender_id'] . "'>" . $zenders[$i]['zender_slogan'] . "</span><br/><br/>"
                      . "<a href='programmaoverzicht.php?{$zenders[$i]['zender_omschrijving']}'>programmaoverzicht</a>" 
                      . (isset($_SESSION['login_username']) ? '<br/><span id="wijzig_zender_' . $zenders[$i]['zender_id'] . '" class="weizig-zender" onclick="wijzigZender(this.id)">wijzig</span> | ' : null) 
                      . (isset($_SESSION['login_username']) ? '<span id="' . $zenders[$i]['zender_id'] . '"' . 'onclick="verwijderZender(this.id)" class="verwijder-zender">verwijder</span>' : null) . "</td>";
            } echo $html;
          ?>
      </tbody>
    </table>
</div>



<!-- DETAILOVERZICHT / PROGRAMMA'S ZOEKEN -->
<?php if(isset($_SESSION['login_username'])){ ?>
  <!-- EEN LIJNTJE OM DE CONTENT VAN ELKAAR TE SCHEIDEN -->
  <div class="dropdown-divider content-devider"></div>

  <!-- DETAILOVERZICHT / PROGRAMMA'S ZOEK FORM-->
  <div class="programma-container">
    <div class="programma-header">Programma zoeken</div>
    <form method="GET" action="gevonden_programma.php">
      <div class="input-group mb-3 zoek-programma">
        <input name="zoek-programma-invoer" type="text" class="form-control programma-zoek-invoer" placeholder="Zoek je programma">
        <div class="input-group-append">
          <button name="zoek-programma-knop" class="btn btn-outline-secondary" type="submit">Zoek</button>
        </div>
      </div>
    </form>
  </div>

  <!-- EEN LIJNTJE OM DE CONTENT VAN ELKAAR TE SCHEIDEN -->
  <div class="dropdown-divider content-devider"></div>

  <!-- DETAILOVERZICHT / PROGRAMMA'S ZOEK FORM-->
  <div class="zoek-nummer-of-artiest-container">
    <form method="GET" action="gevonden_nummer_of_artiest.php">
      <div class="input-group mb-3 zoek-programma">
        <!-- ZOEK NUMMER -->
        <div class="programma-header">Zoek bij nummer of artiest</div>
        <input name="zoek_nummer" type="text" class="form-control zoek-inputs" placeholder="Zoek je nummer">

        <!-- ZOEK ARTIEST -->
        <input name="zoek_artiest" type="text" class="form-control zoek-inputs" placeholder="Zoek je artiest">
         
        <!-- SUBMIT BUTTON -->
        <div class="input-group-append"><button name="data_verstuurd" class="btn btn-outline-secondary" type="submit">Zoek</button></div>
      </div>
    </form>
  </div>

<?php } ?>



<!-- DE CLICKABLE LINK/JS FUNCTIE OM ZENDERS TE VERWIJDEREN -->
<script>
  function verstuurZenderWijziging(current_id){

    let zenderOmschrijvingInvoer = document.getElementById("zender_omschrijving_" + current_id).value;
    let zenderSloganInvoer = document.getElementById("zender_slogan_" + current_id).value;

      //CREATE NEW AJAX REQUEST
      var ajaxRequest = new XMLHttpRequest();

      //SEND THE REQUEST TO THE SERVER
      ajaxRequest.open("POST", "php/ajax/wijzig_zender.php", true); //SENDING METHOD
      ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); //HTTP HEADER TO SEND POST DATA
      ajaxRequest.send(`omschrijving=${zenderOmschrijvingInvoer}&slogan=${zenderSloganInvoer}&id=${current_id}`); //SEND REQUEST

      //CHECK IF REQUEST STATUS CHANGED
      ajaxRequest.onreadystatechange = function(){
      if (this.readyState == 4 && this.status == 200){ //CHECK IF SERVER RESPONSE IS READY
          location.reload();
        }
      };
    }
  


  function annuleerZenderWijziging(current_id){
    // KOSTE ME TEVEEL TIJD OM EEN 'ANNULERING FUNCTIE TE MAKEN WANT, IK HEB NIET ZOVEEL TIJD, DUS KWAM MET EEN SIMPEL ALTERNATIEF'
    let zender = document.getElementById(current_id);
    zender.setAttribute('onclick','wijzigZender(this.id)');
    zender.innerHTML = "wijzig";

    //   let zenderId = current_id.replace(/\D/g,'');
    //   let verstuurZenderWijziging = document.getElementById(zenderId);
    //   verstuurZenderWijziging.setAttribute('onclick','verwijderZender(this.id)');
    //   verstuurZenderWijziging.innerHTML = "verwijder";
    location.reload();
  }

  function wijzigZender(current_id){
      //ANNULEER ZENDER WIJZIGING AANVRAAG
      let annuleerZender = document.getElementById(current_id);
      annuleerZender.setAttribute('onclick','annuleerZenderWijziging(this.id)');
      annuleerZender.innerHTML = "annuleer";

      //HAAL DE ZENDER ID OP
      let zenderId = current_id.replace(/\D/g,'');
      let verstuurZenderWijziging = document.getElementById(zenderId);

      //PLAATS EEN KNOP ZODAT DE GEBRUIKER ZIJN DATA KAN OPSTUREN
      verstuurZenderWijziging.setAttribute('onclick', `verstuurZenderWijziging(${zenderId})`);
      verstuurZenderWijziging.innerHTML = "verstuur";

      //VOEG TEKSTVAKKEN TOE ZODAT DE GEBRUIKER ZIJN NIEUWE DATA KAN INVOEREN
      let zenderOmschrijving = document.getElementById("zender_omschrijving_" + zenderId);
      let zenderSlogan = document.getElementById("zender_slogan_" + zenderId);      

      //MAAK EEN TEKSTVAK AAN
      let zenderOmschrijvingTekstvak = document.createElement("input");
      let zenderSloganTekstvak = document.createElement("input");

      //CREEER ATTRIBUTEN VOOR TEKSTVAK
      zenderOmschrijvingTekstvak.setAttribute("id", zenderOmschrijving.getAttribute('id'));
      zenderSloganTekstvak.setAttribute("id", zenderSlogan.getAttribute('id'));
      zenderOmschrijvingTekstvak.setAttribute("type", "text");
      zenderSloganTekstvak.setAttribute("type", "text");
      zenderOmschrijvingTekstvak.setAttribute("placeholder", zenderOmschrijving.innerText);
      zenderSloganTekstvak.setAttribute("placeholder", zenderSlogan.innerText);

      //PLAATS DE TEKSTVAKKEN OP DE PAGINA
      zenderOmschrijving.replaceWith(zenderOmschrijvingTekstvak);
      zenderSlogan.replaceWith(zenderSloganTekstvak);
  }


  function verwijderZender(current_id){
    //CREATE NEW AJAX REQUEST
    var ajaxRequest = new XMLHttpRequest();

    //SEND THE REQUEST TO THE SERVER
    ajaxRequest.open("POST", "php/ajax/verwijder_zender.php", true); //SENDING METHOD
    ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); //HTTP HEADER TO SEND POST DATA
    ajaxRequest.send(`zender_id=${current_id}`); //SEND REQUEST

    //CHECK IF REQUEST STATUS CHANGED
    ajaxRequest.onreadystatechange = function(){
    if (this.readyState == 4 && this.status == 200){ //CHECK IF SERVER RESPONSE IS READY
        document.getElementById("ajax-zenderoverzicht").innerHTML = this.responseText;
      }
    };
  }
</script>



<!-- FOOTER -->
<?php
  require_once 'layout/footer.php';
?>