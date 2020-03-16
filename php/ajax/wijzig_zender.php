<?php
    session_start();
    if(isset($_POST['omschrijving']) || isset($_POST['slogan'])){
        $zender_id = $_POST['id'];
    } else{
        die("Je hebt geen toegang om deze pagina te bekijken.");
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


    //DYNAMISCHE QUERIES
    $execute_values = []; //WAARDES DIE NAAR DE DB VERZONDEN MOETEN WORDEN
    $commas = []; //SLA OP HOEVEEL COMMA'S ER NODIG ZIJN.
    $commas[0] = ' '; //ALS ER GEEN COMMA'S NODIG ZIJN, ZET DAN EEN SPATIE
    for($i = 0; $i < count($_POST) - 2; $i++){ //SLUIT DE LAATSTE 2 ITEMS IN DE QUERY BUITEN BIJ HET TELLEN VAN DE COMMA'S
        array_push($commas, ', ');
    }
    
    $dynamic_query = "UPDATE zender SET "; 
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

    PRINT_R($execute_values);
    
    //WIJZIG DE ZENDER
    $query = $dbh->connect()->prepare($dynamic_query);
    $query->execute($execute_values);

    // //UPDATE DE ZENDEROVERZICHT
    // $query = $dbh->connect()->prepare("SELECT id AS zender_id, omschrijving AS zender_omschrijving, slogan AS zender_slogan FROM zender");
    // $query->execute();
    // $zenders = $query->fetchAll(PDO::FETCH_ASSOC);
    // $html = '';
    // for($i = 0; $i < count($zenders); $i++){
    //     //CREATE A <TR> FOR EACH 3RD ELEMENT
    //     if($i % 3 == 0){
    //         $html .= "<tr>";
    //     }
    //     //APPEND <TR> TO TBODY
    //     $html .= "<td class='zender'>" . $zenders[$i]['zender_omschrijving'] . '<br/>' 
    //             . $zenders[$i]['zender_slogan'] . '<br/><br/>'
    //             . "<a href='programmaoverzicht.php?{$zenders[$i]['zender_omschrijving']}'>programmaoverzicht</a>" 
    //             . (isset($_SESSION['login_username']) ? '<br/><span class="weizig-zender">wijzig</span> | ' : null) 
    //             . (isset($_SESSION['login_username']) ? '<span id="' . $zenders[$i]['zender_id'] . '"' . 'onclick="verwijderZender(this.id)" class="verwijder-zender">verwijder</span>' : null) . "</td>";
    // } echo $html .= "</tr>";
