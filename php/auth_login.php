<?php
  if(session_status() == PHP_SESSION_ACTIVE) {
      session_unset();
      session_destroy();
  }

  //START A NEW SESSION
  session_start();

  //CHECK IF USER HAS PERMISSION
  if(!isset($_POST['login_submit'])) exit("Invoer mag niet leeg zijn.");

  //CHECK IF THERE ARE EMPTY INPUT-FIELDS & CHECK FOR A WHITESPACE
  foreach($_POST as $input){
      if(empty($input)) exit("Je moet alle vereiste informatie invullen.");
  }

  //DECLARING VARIABLES FOR POST INPUTS FOR ACCESSABILITY
  $login_username = $_POST['login_username'];
  $login_password = $_POST['login_password'];

  //DECLARE BOOLEAN ARRAY FOR LATER USE
  $boolean_array = array();

  //COUNT HOW MANY CHARACTERS THE SPECIFIED INPUT-FIELDS HAS
  function countChars($input_field_id, $input_field_value, $numMinChars, $numMaxChars){
      switch(strlen($input_field_value)){

          case (strlen($input_field_value) < $numMinChars): 
          $numMinChars -= 1;
          echo "<div class='character_notice'>" . "Voer een inlogwachtwoord in met meer dan " . $numMinChars . " tekens." . "</div>";
          global $boolean_array; //GLOBAL IS USED INSIDE FUNCTIONS TO ACCESS VARIABLES IN AN OUTER SCOPE
          $boolean_array[] = 0;
          break;

          case (strlen($input_field_value) > $numMaxChars): 
          $numMaxChars += 1;
          echo "<div class='character_notice'>" . "Voer een inlogwachtwoord in met minder dan " . $numMaxChars . " tekens." . "</div>";
          global $boolean_array; //GLOBAL IS USED INSIDE FUNCTIONS TO ACCESS VARIABLES IN AN OUTER SCOPE
          $boolean_array[] = 0;
          break;
      }
  }

  //FUNCTION THAT CHECKS IF INPUT CONTAINS A WHITESPACE
  function checkWhitespace($input_field_id, $input_field_value){
      if (preg_match('/\s/', $input_field_value)){ 
              global $boolean_array; //GLOBAL IS USED INSIDE FUNCTIONS TO ACCESS VARIABLES IN AN OUTER SCOPE
              $boolean_array[] = 0;
          echo "<div class='whitespace_notice'>" . ucfirst($input_field_id) . " input-field contains spaces." . "</div>";
      } 
  } 

  //SET MIN / MAX CHARACTERS FOR INPUT-FIELDS
  countChars("login_username", $login_username, 3, 30);
  countChars("login_password", $login_password, 8, 50);

  //CHECK IF THESE INPUT-FIELDS CONTAIN A
  checkWhitespace("login_username", $login_username);
  checkWhitespace("login_password", $login_password);

  //CHECK IF BOOLEAN ARRAY CONTAINS FALSE VALUES
  if (in_array(0, $boolean_array)) exit();



  //IF ALL CHECKS PASS, CONNECT TO THE DB TO SEARCH FOR THE USER LOGIN
  require 'class/dbconnection.php';
  $dbh = new dbconnection();


  //CHECK IF ACCOUNT EXISTS
  $query = $dbh->connect()->prepare("SELECT login, wachtwoord, nickname 
                                     FROM medewerker 
                                     WHERE login = ?");
  $query->bindParam(1, $login_username, PDO::PARAM_STR, 99);
  $query->execute();
  $login_data = $query->fetch(PDO::FETCH_ASSOC);

  //CHECK IF ACCOUNT IS FOUND
  if(empty($login_data)){
      exit("Account niet gevonden.");
  } 

  //CHECK IF login_passwordS MATCH
  if(!password_verify($login_password, $login_data['wachtwoord'])){
      exit("Wachtwoord komt niet overeen.");
  }

  //GIVE USER ACCESS TO ADMIN PANEL
  $_SESSION['login_username'] = $login_data['login']; //SET SESSION ID
  $_SESSION['login_wachtwoord'] = $login_data['wachtwoord'];
  $_SESSION['medewerker_nickname'] = $login_data['nickname'];

  header("Location: ../index.php"); //REDIRECT USER TO ADMIN PANEL

  //EXIT THE DOCUMENT
  exit();
?>