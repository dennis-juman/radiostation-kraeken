<?php 
  require_once 'layout/head.php';
  require_once 'layout/brand.php';

  //CHECK IF PREVIOUS SESSIONS HAVE BEEN SET
  if(session_status() == PHP_SESSION_ACTIVE){
    session_unset();
    session_destroy();
  }
?>


  <div class="aanmelding-container">
    <form class="px-4 py-3" method="POST" action="php/auth_login.php">
      <div class="form-group">
        <label for="exampleDropdownFormEmail1">Gebruikersnaam</label>
        <input type="text" name="login_username" class="form-control" id="exampleDropdownFormEmail1" placeholder="Gebruikersnaam">
      </div>
      <div class="form-group">
        <label for="exampleDropdownFormPassword1">Wachtwoord</label>
        <input type="password" name="login_password" class="form-control" id="exampleDropdownFormPassword1" placeholder="Wachtwoord">
      </div>
      <div class="form-check">
        <input type="checkbox" class="form-check-input" id="dropdownCheck">
        <label class="form-check-label" for="dropdownCheck">Onthoud mijn login</label>
      </div>
      <button type="submit" value="submit_pressed" name="login_submit" class="btn btn-primary aanmelden-submit-button">Aanmelden</button>
    </form>
    <div class="dropdown-divider"></div>
    <a class="dropdown-item" href="#">Een nieuw account aanmaken</a>
    <a class="dropdown-item" href="#">Wachtwoord vergeten?</a>
  </div>


<?php
  require_once 'layout/footer.php';
?>