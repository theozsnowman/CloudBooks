<?php
/*  
    CloudBooks. Open source hotel and restaurant management software.
    Copyright (C) 2020 Vittorio Lo Mele

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published
    by the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <https://www.gnu.org/licenses/>.
    Contact me at: vittorio[at]mrbackslash.it
*/

if (!file_exists("../config.php")) {
    die("Please run installer in /installer directory");
}
session_start();
require_once "../config.php";
require_once "../functions.php";
require "../res/libs/PHPGangsta/GoogleAuthenticator.php";

if (!isset($_SESSION["loggedin"]) | $_SESSION["loggedin"] != true) {
    header("location: " . $INSTALL_LINK);
}

if ($_SESSION["2fa"] == "tocheck") {
    header("location: " . $INSTALL_LINK . "2fa/");
}
$err = "";
//get useful data from db
$gauth = new PHPGangsta_GoogleAuthenticator;
$accid = $SQLINK->escape_string($_SESSION["id"]);
$result = $SQLINK->query("SELECT * FROM `users` WHERE `id` = '$accid'", MYSQLI_STORE_RESULT);
$userdata = $result->fetch_array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["oldpass"]) && isset($_POST["newpass"]) && isset($_POST["newpassre"])) {
    } else {
        $err = loginError("Compila tutti i campi.");
    }
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CloudBooks - Cambio Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="shortcut icon" href="<?php echo $INSTALL_LINK; ?>res/img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="<?php echo $INSTALL_LINK; ?>res/img/favicon.ico" type="image/x-icon">
    <meta name="theme-color" content="#563d7c">
    <link href="<?php echo $INSTALL_LINK; ?>res/css/basestyle.css" rel="stylesheet">
    <link href="<?php echo $INSTALL_LINK; ?>res/css/2fasettings.css" rel="stylesheet">

</head>

<body>
    <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-3" href="#">CloudBooks - <?php echo $CNAME; ?></a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-toggle="collapse" data-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <ul class="navbar-nav px-3">
            <li class="nav-item text-nowrap">
                <a class="nav-link" href="<?php echo $INSTALL_LINK; ?>logout.php"><span data-feather="user"></span> <?php echo $_SESSION["username"]; ?> - Esci <span data-feather="log-out"></span></a>
            </li>
        </ul>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="sidebar-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $INSTALL_LINK; ?>dashboard">
                                <span data-feather="home"></span>
                                Dashboard
                            </a>
                        </li>
                    </ul>

                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Camere</span>
                    </h6>
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $INSTALL_LINK; ?>bookings/">
                                <span data-feather="calendar"></span>
                                Prenotazioni Attive
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $INSTALL_LINK; ?>bookings/history/">
                                <span data-feather="archive"></span>
                                Storico
                            </a>
                        </li>
                    </ul>

                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Tavoli</span>
                    </h6>
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $INSTALL_LINK; ?>restaurant/">
                                <span data-feather="coffee"></span>
                                Occupati Oggi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $INSTALL_LINK; ?>restaurant/history">
                                <span data-feather="archive"></span>
                                Storico
                            </a>
                        </li>
                    </ul>

                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Clienti</span>
                    </h6>
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $INSTALL_LINK; ?>personaldata/">
                                <span data-feather="user"></span>
                                Anagrafiche Clienti
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $INSTALL_LINK; ?>analytics/">
                                <span data-feather="trending-up"></span>
                                Analytics
                            </a>
                        </li>
                    </ul>

                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Account</span>
                    </h6>
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item">
                            <a class="nav-link active" href="<?php echo $INSTALL_LINK; ?>account/">
                                <span data-feather="lock"></span>
                                Cambia Password <span class="sr-only">(current)</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $INSTALL_LINK; ?>account/2fasettings/">
                                <span data-feather="layers"></span>
                                Autenticazione a due fattori
                            </a>
                        </li>
                    </ul>

                    <?php
                    //if admin display admin controls
                    if ($_SESSION["type"] == "1") {
                        displayAdminControls();
                    } ?>

                </div>
            </nav>

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Cambia Password</h1>
                </div>
                <form class="form-2fa" action="" method="post">
                    <div class="text-center mb-4">

                        <br><a style="color: white;">---</a><br>
                    </div>
                    <fiedlset disabled>
                        <div class="form-label-group" autocomplete="off">
                            <input type="text" id="inOldPass" class="form-control" placeholder="Username" autocomplete="false" value="<?php echo $_SESSION["username"]; ?>" disabled>
                            <label for="inOldPass">
                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-person" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm6 5c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z" />
                                </svg>
                                Username
                            </label>
                        </div>
                    </fiedlset>
                    <div class="form-label-group" autocomplete="off">
                        <input type="text" name="oldpass" id="inOldPass" class="form-control" placeholder="Password Vecchia" minlength="6" maxlength="72" required autofocus autocomplete="false">
                        <label for="inOldPass">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-key-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M3.5 11.5a3.5 3.5 0 1 1 3.163-5H14L15.5 8 14 9.5l-1-1-1 1-1-1-1 1-1-1-1 1H6.663a3.5 3.5 0 0 1-3.163 2zM2.5 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" />
                            </svg>
                            Password Vecchia
                        </label>
                    </div>
                    <div class="form-label-group" autocomplete="off">
                        <input type="text" name="newpass" id="inNewPass" class="form-control" placeholder="Password Nuova" minlength="6" maxlength="72" required autocomplete="false">
                        <label for="inNewPass">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-key" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8zm4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5z" />
                                <path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                            </svg>
                            Password Nuova
                        </label>
                    </div>
                    <div class="form-label-group" autocomplete="off">
                        <input type="text" name="newpassre" id="inNewPassRe" class="form-control" placeholder="Conferma Password Nuova" minlength="6" maxlength="72" required autocomplete="false">
                        <label for="inNewPassRe">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check-all" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M8.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L2.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093L8.95 4.992a.252.252 0 0 1 .02-.022zm-.92 5.14l.92.92a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 1 0-1.091-1.028L9.477 9.417l-.485-.486-.943 1.179z" />
                            </svg>
                            Conferma Password Nuova
                        </label>
                    </div>
                    <?php
                    if ($tfa == "2") {
                        displayOTPField();
                    }
                    echo $err;
                    ?>
                    <button class="btn btn-lg btn-primary btn-block" type="submit">
                        Cambia Password
                    </button>
                    <p class="mt-5 mb-3 text-muted text-center">Gestionale CloudBooks - &copy; <?php echo date("Y"); ?> Vittorio Lo Mele
                    </p>
                </form>
            </main>

        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://getbootstrap.com/docs/4.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-LtrjvnR4Twt/qOuYxE721u19sVFLVSA4hf/rRt6PrZTmiPltdZcI7q7PXQBYTKyf" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.9.0/feather.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>
    <script src="<?php echo $INSTALL_LINK; ?>res/js/dashboard.js"></script>
</body>

</html>