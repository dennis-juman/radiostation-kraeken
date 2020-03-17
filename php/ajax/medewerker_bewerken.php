<?php
    session_start();
    if(isset($_POST['id'])){
        $id = $_POST['id'];
    } else{
        die("Je hebt geen toegang tot deze pagina.");
    }

    //MAAK VERBINDING MET DE DATABASE
    require '../class/dbconnection.php';
    $dbh = new dbconnection();

    //CONTROLEER HOVEEL MEDEWERKERS ER NOG ZIJN.
    $query = $dbh->connect()->prepare("SELECT id FROM medewerker");
    $query->execute();
    if(count($query->fetchAll(PDO::FETCH_ASSOC)) == 1){
        echo '<div class="geen-medewerkers">Er zijn momenteel geen medewerkers zichbaar.</div>';
    }


    //DYNAMISCHE QUERIES
    $execute_values = []; //WAARDES DIE NAAR DE DB VERZONDEN MOETEN WORDEN
    $commas = []; //SLA OP HOEVEEL COMMA'S ER NODIG ZIJN.
    $commas[0] = ''; //ALS ER GEEN COMMA'S NODIG ZIJN, ZET DAN EEN SPATIE
    for($i = 0; $i < count($_POST) - 2; $i++){ //SLUIT DE LAATSTE 2 ITEMS IN DE QUERY BUITEN BIJ HET TELLEN VAN DE COMMA'S
        array_push($commas, ', ');
    }
    
    $dynamic_query = "UPDATE medewerker SET "; 
    $i = count($commas); //TEL HOEVEEL COMMA'S ER WERDEN OPGESLAGEN IN DE ARRAY
    foreach($_POST as $key => $value){
        if($i != 0){ //GEBRUIK DE COMMA'S TOT DAT ZE OP ZIJN
            $i--;
        }

        if($key == 'id'){ //COMMA'S VOOR 'ID' MOGEN OVERGESLAGEN WORDEN
            array_push($execute_values, $value);
            continue;
        }

        //CONTROLEER GEBRUIKERSINVOER OP LEGE WAARDES
        if(!empty($value)){
            $positional_placeholder = "?" . $commas[$i]; //VOEG DE COMMA TOE AAN DE POSITIONAL PLACEHOLDER

            //VOEG DE POSITIONAL PLACEHOLDER TOE AAN DE QUERY
            $dynamic_query .= $key . " = " . $positional_placeholder; 
            array_push($execute_values, $value);
        }
    } echo $dynamic_query .= " WHERE id = ?";

    //WIJZIG DE medewerker
    $query = $dbh->connect()->prepare($dynamic_query);
    $query->execute($execute_values);