<?php
if(!isset($_POST['programma_id'])){
    die("Je hebt geen toegang tot deze pagina.");
}

//DB CONNECTION
require '../class/dbconnection.php';
$dbh = new dbconnection();

$programma_id = $_POST['programma_id'];

//SEND QUERY TO THE DB
$query = $dbh->connect()->prepare("SELECT programma.omschrijving AS programma_omschrijving, 
                                          uitzending.datum AS uitzending_datum, 
                                          uitzending.begintijd AS uitzending_begintijd, 
                                          uitzending.eindtijd AS uitzending_eindtijd, 
                                          medewerker.nickname AS medewerker_nickname, 
                                          zender.omschrijving AS zender_omschrijving,
                                          artiest.artiestennaam AS artiest_artiestennaam, 
                                          song.titel AS song_titel, 
                                          song.duur AS song_duur,
                                          TIME_TO_SEC(duur) AS song_totale_duur
                                          FROM artiest
                                          JOIN artiest_has_song ON artiest_has_song.artiest_id = artiest.id
                                          JOIN song ON song.id = artiest_has_song.song_id
                                          JOIN uitzending_has_song ON uitzending_has_song.song_id = song.id
                                          JOIN uitzending ON uitzending.datum = uitzending_has_song.uitzending_datum
                                          JOIN programma ON programma.id = uitzending.programma_id
                                          JOIN medewerker ON medewerker.id = uitzending.medewerker_id
                                          JOIN zender ON zender.id = uitzending.zender_id
                                          WHERE programma_id = ?");
$query->bindParam(1, $programma_id, PDO::PARAM_INT);
$query->execute();
$search_results = $query->fetchAll(PDO::FETCH_ASSOC);




// PROGRAMMAOVERZICHT TABEL
$html_content = "<div id='programma_{$programma_id}' class='programma-container'>
                        <ul class='list-group detailoverzicht'>
                        <div class='clicked-programma-header'><b>Programma</b></div>
                            <li class='list-group-item'>Naam programma: ‘{$search_results[0]['programma_omschrijving']}’</li>
                            <li class='list-group-item'>Datum: {$search_results[0]['uitzending_datum']}</li>
                            <br/>
                            <li class='list-group-item'>Begintijd: " . date('G:i', strtotime($search_results[0]['uitzending_begintijd'])) . "</li>
                            <li class='list-group-item'>Eindtijd: " . date('G:i', strtotime($search_results[0]['uitzending_eindtijd'])) . "</li>
                            <br/>
                            <li class='list-group-item'>Presentatie: {$search_results[0]['medewerker_nickname']}</li>
                            <br/>
                            <li class='list-group-item'>Zender: <b>" . strtoupper($search_results[0]['zender_omschrijving']) . "</b></li>
                        </ul>
                        <div class='container-detailoverzicht'>
                        <div class='playlist-header'><b>Playlist</b></div>
                                <table class='table table-striped'>
                                    <tbody id='ajax-detailoverzicht'>";

                //PLAATS DE PROGRAMMAOVERZICHT DATA IN DE TABEL CELLEN
                $song_totale_duur = 0;
                foreach($search_results as $search_result){
                    //PROGRAMMA TABEL TUPLES AANMAKEN
                    $html_content .= "<tr>
                                        <td>{$search_result['song_titel']}</td>
                                        <td>{$search_result['artiest_artiestennaam']}</td>
                                        <td class='song_totale_duur'>" . substr($search_result['song_duur'], 0, 5) ."</td>
                                    </tr>";
                    $song_totale_duur += $search_result['song_totale_duur'];
                } $html_content .= "</tr>
                                        <td></td>
                                        <td class='song_totale_duur'>Totaal</td>
                                        <td class='song_duur'>" . gmdate("H:i", $song_totale_duur) . "</td>
                                    <tr>";
        echo $html_content .= "</tbody>
                            </table>
                        </div>
                    </div>";