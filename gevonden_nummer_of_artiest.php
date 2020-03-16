<?php
//HEADER, CSS FILES ETC.
require_once 'layout/head.php';



//CONTROLEER OF DE GEBRUIKER TOEGANG HEEFT TOT DE PAGINA
if(!isset($_GET['data_verstuurd'])){
    die("<div>Je hebt geen toegang tot deze pagina.</div>");
}

//CONTROLEER OF DE GEBRUIKER VOOR IETS HEEFT GEZOCHT.
if(empty($_GET['zoek_nummer']) && empty($_GET['zoek_artiest'])){
    die("<div class='invoer-is-leeg'><div>Je invoer is leeg.</div></div>");
}

//LOAD BRAND & NAVBAR
require_once 'layout/brand.php';
require_once 'layout/navbar.php';






require 'php/class/dbconnection.php';
$dbh = new dbconnection();
$search_results = [];



// //WAT ER MOET GEBEUREN ALS DE GEBRUIKER ZOEKT VOOR EEN NUMMER
if(isset($_GET['zoek_nummer']) && !empty($_GET['zoek_nummer'])){
    $zoek_nummer = strip_tags($_GET['zoek_nummer']) . '%';

    //SEND QUERY TO THE DB
    $query = $dbh->connect()->prepare("SELECT song.id AS nummer_id,
                                              artiest.artiestennaam AS artiest_naam, 
                                              song.titel AS song_titel, 
                                              song.duur AS song_duur
                                              FROM artiest
                                              JOIN artiest_has_song ON artiest_has_song.artiest_id = artiest.id
                                              JOIN song ON song.id = artiest_has_song.song_id
                                              WHERE song.titel
                                              LIKE ?");
    $query->bindParam(1, $zoek_nummer, PDO::PARAM_STR, 455);
    $query->execute();
    $search_results['zoekresultaat_nummer'] = $query->fetchAll(PDO::FETCH_ASSOC);

    //CONTROLEER OF ER ZOEKRESULTATEN ZIJN.
    if(empty($search_results['zoekresultaat_nummer'])){
        echo "<div class='geen-zoekresultaat-gevonden'><div>Geen nummers gevonden.</div></div>";
    } else {
        //PLAATS DE PROGRAMMAOVERZICHT DATA IN DE TABEL CELLEN
        $html_content = "<div class='container'>
                            <div class='playlist-header'><b>Playlist</b></div>
                                <table class='table table-striped table-container'>
                                    <tbody>
                                        <thead>
                                            <tr>
                                                <th scope='col'>Artiest</th>
                                                    <th scope='col'>Nummer</th>
                                                <th scope='col'>Duur in minuten</th>
                                            </tr>
                                        </thead>";

        foreach($search_results['zoekresultaat_nummer'] as $search_result){
                        //PROGRAMMA TABEL TUPLES AANMAKEN
                        $html_content .= "<tr>
                                            <td>" . $search_result['artiest_naam'] . "</td>
                                            <td>" . $search_result['song_titel'] . "</td>
                                            <td>" . substr($search_result['song_duur'], 0, 5)  . "</td>
                                            <td id='{$search_result['nummer_id']}' onclick='verwijderNummer(this.id)' class='verwijder-zender'>verwijder</td>
                                        </tr>";         

        }   echo $html_content .= "</tbody>
                                </table>
                            </div>";
    }
} //EINDE VAN 'IF-STATEMENT'


if(!empty($_GET['zoek_nummer']) && !empty($_GET['zoek_artiest'])){
    echo "<div class='dropdown-divider content-devider'></div>";
}

//----------------------------------------------------------------------------------------------------



//WAT ER MOET GEBEUREN ALS DE GEBRUIKER ZOEKT VOOR EEN ARTIEST
if(isset($_GET['zoek_artiest']) && !empty($_GET['zoek_artiest'])){
    $zoek_artiest = '%' . strip_tags($_GET['zoek_artiest']) . '%';

    //SEND QUERY TO THE DB
    $query = $dbh->connect()->prepare("SELECT artiest.artiestennaam AS artiest_naam, 
                                              song.titel AS song_titel, 
                                              song.duur AS song_duur
                                              FROM artiest
                                              JOIN artiest_has_song ON artiest_has_song.artiest_id = artiest.id
                                              JOIN song ON song.id = artiest_has_song.song_id
                                              WHERE artiest.artiestennaam 
                                              LIKE ?");
    $query->bindParam(1, $zoek_artiest, PDO::PARAM_STR, 455);
    $query->execute();
    $search_results['zoekresultaat_artiest'] = $query->fetchAll(PDO::FETCH_ASSOC);

    //CONTROLEER OF ER ZOEKRESULTATEN ZIJN.
    if(count($search_results['zoekresultaat_artiest']) == 0){
        echo "<div class='geen-zoekresultaat-gevonden'>Geen artiesten gevonden.</div>";
    } else{
        //PLAATS DE PROGRAMMAOVERZICHT DATA IN DE TABEL CELLEN
        $html_content = "<div class='container'>
                            <div class='artiest-header'><b>Artiesten</b></div>
                                <table class='table table-striped table-container'>
                                    <tbody>
                                        <thead>
                                            <tr>
                                                <th scope='col'>Artiest</th>
                                                    <th scope='col'>Nummer</th>
                                                <th scope='col'>Duur in minuten</th>
                                            </tr>
                                        </thead>";

        foreach($search_results['zoekresultaat_artiest'] as $search_result){
                        $html_content .= "<tr>
                                            <td>" . $search_result['artiest_naam'] . "</td>
                                            <td>" . $search_result['song_titel'] . "</td>
                                            <td>" . substr($search_result['song_duur'], 0, 5)  . "</td>
                                        </tr>";         

        }   echo $html_content .= "</tbody>
                                </table>
                            </div>";
    }
} 
?>
<!-- EEN LIJNTJE OM DE CONTENT VAN ELKAAR TE SCHEIDEN -->
<div class="dropdown-divider content-devider"></div>

<div class="nummer-form-container">
      <form method="POST" action="php/nummer_toevoegen.php">
      <div class="nummer-form-caption">Nummmer toevoegen</div>
          <div class="form-group">
              <label for="exampleInputEmail1">Artiest</label>
              <input name="artiestnaam_toevoegen" type="text" class="form-control nummer-input">
          </div>
          <br/>
          <div class="form-group">
              <label for="exampleInputPassword1">Titel</label>
              <input name="nummer_toevoegen" type="text" class="form-control nummer-input">
          </div>
          <button name="nummer_toevoegen_verstuurd" type="submit" class="btn btn-primary nummer-submit-button">Verstuur</button>
      </form>
  </div>

<script>
    function verwijderNummer(nummer_id){
            //CREATE NEW AJAX REQUEST
            var ajaxRequest = new XMLHttpRequest();

            //SEND THE REQUEST TO THE SERVER
            ajaxRequest.open("POST", "php/ajax/verwijder_nummer.php", true); //SENDING METHOD
            ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); //HTTP HEADER TO SEND POST DATA
            ajaxRequest.send(`nummer_id=${nummer_id}`); //SEND REQUEST

            //CHECK IF REQUEST STATUS CHANGED
            ajaxRequest.onreadystatechange = function(){
            if (this.readyState == 4 && this.status == 200){ //CHECK IF SERVER RESPONSE IS READY
                location.reload();
            }
        };
    }
</script>

<?php
  require_once 'layout/footer.php';
?>