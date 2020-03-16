<?php
    if(count($_GET) > 1){
        exit("Je hebt geen toestemming om deze pagina te bekijken.");
    }
    if(empty($_GET) || count($_GET) == 0){
        exit("Oei, er ging iets mis! Probeer het later nog eens.");
    }
?>

<?php 
    require_once 'layout/head.php';
    require_once 'layout/brand.php';
    require_once 'layout/navbar.php';
?>


<?php
    $programma = key($_GET);

    //FETCH ZENDEROVERZICHT DATA
    require 'php/class/dbconnection.php';
    $dbh = new dbconnection();
    $query = $dbh->connect()->prepare("SELECT programma.id AS programma_id, programma.omschrijving, uitzending.datum, uitzending.begintijd, uitzending.eindtijd, medewerker.nickname FROM uitzending 
                                        JOIN programma ON programma.id = uitzending.programma_id
                                        JOIN medewerker ON medewerker.id = uitzending.medewerker_id
                                        JOIN zender ON zender.id = uitzending.zender_id
                                        WHERE zender.omschrijving = ?");
    $query->bindParam(1, $programma, PDO::PARAM_STR, 99);
    $query->execute();
    $uitzending_informatie = $query->fetchAll(PDO::FETCH_ASSOC);

    //CONTROLEER OF ER EEN PROGRAMMAOVERZICHT IS
    if(empty($uitzending_informatie)){
        echo "<div class='geen-programma'><span>Er is momenteel geen programmaoverzicht voor {$programma}.</span></div>";
    } else { //ALS ER EEN PROGRAMMAOVERZICHT IS, GENEREER DE TABEL
?>
                        <!-- PROGRAMMAOVERZICHT TABEL -->
                        <div class='programmaoverzicht'>
                            <div class='programmaoverzicht-titel'>Programma info</div>
                                <table class='table table-striped'>
                                    <tbody id='ajax-programmaoverzicht'>
                                        <thead>
                                            <tr>
                                                <th scope='col'>Programma</th>
                                                    <th scope='col'>Datum</th>
                                                    <th scope='col'>Tijd</th>
                                                    <th scope='col'>Duur in minuten</th>
                                                <th scope='col'>Presentator</th>
                                            </tr>
                                        </thead>
            <?php
                //PLAATS DE PROGRAMMAOVERZICHT DATA IN DE TABEL CELLEN
                $html_content = '';
                for($i = 0; $i < count($uitzending_informatie); ++$i){
                    //BEREKEN HET AANTAL MINUTEN TUSSEN TWEE TIJDSTIPPEN IN
                    $duratie_minuten = 60 * (floor($uitzending_informatie[$i]['eindtijd']) - floor($uitzending_informatie[$i]['begintijd']));
                    $uitzending_informatie[$i]['duratie_minuten'] = $duratie_minuten;

                    //PROGRAMMA TABEL TUPLES AANMAKEN
                    $html_content .= "<tr>
                                        <td>{$uitzending_informatie[$i]['omschrijving']}</td>
                                        <td>{$uitzending_informatie[$i]['datum']}</td>
                                        <td>" . date('g:i', strtotime($uitzending_informatie[$i]['begintijd'])) . " - " . date('g:i', strtotime($uitzending_informatie[$i]['eindtijd'])) . "</td>
                                        <td>{$uitzending_informatie[$i]['duratie_minuten']}</td>
                                        <td>{$uitzending_informatie[$i]['nickname']}</td>
                                        <td>" . (isset($_SESSION['login_username']) ? '<span id="' . $uitzending_informatie[$i]['programma_id'] . '"' . 'onclick="weizigProgramma(this.id)" class="weizig-programma">weizig</span>' : null) . "</td>
                                        <td>" . (isset($_SESSION['login_username']) ? '<span id="' . $uitzending_informatie[$i]['programma_id'] . '"' . 'onclick="verwijderProgramma(this.id)" class="verwijder-programma">verwijder</span>' : null) . "</td>
                                    </tr>";
                }   echo $html_content;
    } //EINDE VAN 'ELSE'-STATEMENT
?>
        </tbody>
    </table>
</div>


<!-- DE KNOP OM PROGRAMMA'S TE VERWIJDEREN -->
<script>
  function verwijderProgramma(current_id){
    //CREATE NEW AJAX REQUEST
    var ajaxRequest = new XMLHttpRequest();

    //SEND THE REQUEST TO THE SERVER
    ajaxRequest.open("POST", "php/ajax/verwijder_programma.php", true); //SENDING METHOD
    ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); //HTTP HEADER TO SEND POST DATA
    ajaxRequest.send(`programma_id=${current_id}`); //SEND REQUEST

    //CHECK IF REQUEST STATUS CHANGED
    ajaxRequest.onreadystatechange = function(){
    if(this.readyState == 4 && this.status == 200){ //CHECK IF SERVER RESPONSE IS READY
        location.reload();
      }
    };
  }
</script>



<!-- FOOTER -->
<?php
  require_once 'layout/footer.php';
?>