<?php
    //CONTROLEER OF DE GEBRUIKER TOEGANG HEEFT TOT DE PAGINA
    if(!isset($_POST['wachtwoord'])){
        die("<div class='error'>Je hebt geen toegang tot deze pagina.</div>");
    }

    //ENCRYPT PASSWORD /W BCRYPT
    $_POST['wachtwoord'] = password_hash($_POST['wachtwoord'], PASSWORD_DEFAULT); 

    //CONTROLEER WAT DE GEBRUIKER HEEFT INGEVULD.
    $input_data = [];
    foreach($_POST as $key => $value){
        if( isset($_POST[$key]) && !empty($_POST[$key]) ){
            $input_data[$key] = $value;
        }
    }


    //CONTROLEER OF DE GEBRUIKER DE BEINODIGE VELDEN HEEFT INGEVULD
    if(count($input_data) < 2){
        die("Je hebt niet alle verplichte velden ingevuld.");
    }

    //MAAK VERBINDING MET DE DATABASE
    require '../class/dbconnection.php';
    $dbh = new dbconnection();




    //DYNAMISCHE QUERY
    $execute_values = []; //WAARDES DIE NAAR DE DB VERZONDEN MOETEN WORDEN
    $commas = []; //SLA OP HOEVEEL COMMA'S ER NODIG ZIJN.
    $commas[0] = null; //ALS ER GEEN COMMA'S NODIG ZIJN, ZET DAN EEN SPATIE
    for($i = 0; $i < count($input_data) - 1; $i++){ //SLUIT DE LAATSTE 2 ITEMS IN DE QUERY BUITEN BIJ HET TELLEN VAN DE COMMA'S
        array_push($commas, ', ');
    }
    

    $dynamic_query = "INSERT INTO medewerker ("; 
    $i = count($commas); //TEL HOEVEEL COMMA'S ER WERDEN OPGESLAGEN IN DE ARRAY
    foreach($input_data as $key => $value){
        if($i != 0){ //GEBRUIK DE COMMA'S TOT DAT ZE OP ZIJN
            $i--;
        }

        if($key == 'id'){ //COMMA'S VOOR 'ID' MOGEN OVERGESLAGEN WORDEN
            continue;
        }

        //VOEG DE POSITIONAL PLACEHOLDER TOE AAN DE QUERY
        $dynamic_query .= $key . $commas[$i];
    }

    $dynamic_query .= ") VALUES ("; 
    $i = count($commas); //TEL HOEVEEL COMMA'S ER WERDEN OPGESLAGEN IN DE ARRAY
    foreach($input_data as $key => $value){
        if($i != 0){ //GEBRUIK DE COMMA'S TOT DAT ZE OP ZIJN
            $i--;
        }

        if($key == 'id'){ //COMMA'S VOOR 'ID' MOGEN OVERGESLAGEN WORDEN
            array_push($execute_values, $value);
            continue;
        }

        //VOEG DE POSITIONAL PLACEHOLDER TOE AAN DE QUERY
        $dynamic_query .= "?" . $commas[$i];
        array_push($execute_values, $value);

    } $dynamic_query .= ")";


    //WIJZIG DE ZENDER
    $query = $dbh->connect()->prepare($dynamic_query);
    $query->execute($execute_values);