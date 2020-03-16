<?php
  session_start();
  if(isset($_GET['uitloggen']) && $_GET['uitloggen'] == 'uitloggen'){
    if(session_status() == PHP_SESSION_ACTIVE){
        session_unset();
        session_destroy();
    }
  }
?>

<!doctype html>
<html lang="en">
  <head>
      <!-- Required meta tags -->
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

      <!-- Fonts -->
      <link href="https://fonts.googleapis.com/css?family=Pacifico&display=swap" rel="stylesheet">

      <!-- Bootstrap CSS -->
      <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

      <!-- My own CSS -->
      <link rel="stylesheet" href="../css/general.css">
      <link rel="stylesheet" href="../css/index.css">
      <link rel="stylesheet" href="../css/contact.css">
      <link rel="stylesheet" href="../css/zenderoverzicht.css">
      <link rel="stylesheet" href="../css/login.css">
      <link rel="stylesheet" href="../css/register.css">
      <link rel="stylesheet" href="../css/footer.css">
      <link rel="stylesheet" href="../css/programmaoverzicht.css">
      <link rel="stylesheet" href="../css/navbar.css">
      <link rel="stylesheet" href="../css/detailoverzicht.css">
      <link rel="stylesheet" href="../css/gevonden_nummer_of_artiest.css">
      <link rel="stylesheet" href="../css/gevonden_programma.css">
      <title>Kraeken en Krønen</title>
    </head>
  <body>