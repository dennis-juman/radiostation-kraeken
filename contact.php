<?php 
  require_once 'layout/head.php';
  require_once 'layout/brand.php';
  require_once 'layout/navbar.php';
?>
  <div class="form-container">
    <div class="contact-form">
        <form>
            <div class="contact-form-caption">Contactpagina</div>
            <div class="form-group">
                <label for="exampleFormControlInput1">Vul hier je emailadres in</label>
                <input type="email" class="form-control" id="exampleFormControlInput1" placeholder="naam@voorbeeld.nl">
            </div>
            <div class="form-group">
                <label for="exampleFormControlTextarea1">Laat ons een bericht achter</label>
                <textarea class="form-control form-textarea" id="exampleFormControlTextarea1" rows="5"></textarea>
            </div>
            <button type="submit" class="btn btn-primary contact-submit-button">Verstuur</button>
        </form>
    </div>
    <div class="card text-white bg-primary mb-3 business-card" style="max-width: 18rem;">
        <div class="card-header">Onze locatie</div>
        <div class="card-body">
            <h5 class="card-title">Kraeken en Krønen</h5>
            <p class="card-text">Enschedeseweg 81<br/>1229 DE Enschede<br/>Tel. 06493922<br/><br/>E-mail: info@krakenenkronen.nl</p>
        </div>
    </div>
</div>
<?php
  require_once 'layout/footer.php';
?>
