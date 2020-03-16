    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-item nav-link" href="index.php">Home</a>
                <a class="nav-item nav-link" href="contact.php">Contact</a>
                <a class="nav-item nav-link" href="zenderoverzicht.php">Zenders</a>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Account</a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <?php
                            if(isset($_SESSION['login_username'])){
                                echo '<a class="dropdown-item" href="index.php?uitloggen=uitloggen">Uitloggen</a>';
                            } else{
                                echo '<a class="dropdown-item" href="login.php">Aanmelden</a>
                                      <a class="dropdown-item" href="register.php">Registeren</a>';
                            }
                        ?>
                    </div>
                </li>
            </div>
        </div>
    </nav>