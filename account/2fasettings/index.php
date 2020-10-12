<?php
if (!file_exists("../../config.php")) {
    die("Please run installer in /installer directory");
}
session_start();
require_once "../../config.php";
require_once "../../functions.php";
require "../../res/libs/PHPGangsta/GoogleAuthenticator.php";

if (!isset($_SESSION["loggedin"]) | $_SESSION["loggedin"] != true) {
    header("location: " . $INSTALL_LINK);
}

if ($_SESSION["2fa"] == "tocheck") {
    header("location: " . $INSTALL_LINK . "2fa/");
}
$err = "null";
//get 2fa status from DB
$gauth = new PHPGangsta_GoogleAuthenticator;
$accid = $SQLINK->escape_string($_SESSION["id"]);
$tfastatus = $SQLINK->query("SELECT `2fa`, `2fasecret` FROM `users` WHERE `id` = '$accid'", MYSQLI_STORE_RESULT);
$tfastatus_res = $tfastatus->fetch_array();
if ($tfastatus_res["2fa"] == "0") {
    //generating a fresh secret code
    $secretcode = $gauth->createSecret();
    $imgsrc = $gauth->getQRCodeGoogleUrl("CloudBooks " . $CNAME, $secretcode);
    //insert in db pending state
    $pre = "UPDATE `users` SET `2fa` = 1, `2fasecret` = ? WHERE `id` = ?";
    $create_stmt = $SQLINK->prepare($pre);
    $create_stmt->bind_param("si", $par_secret_new, $par_id_new);
    $par_secret_new = $SQLINK->real_escape_string($secretcode);
    $par_id_new = $accid;
    $create_stmt->execute();
} else {
    //if there is code store it
    $secretcode = $tfastatus_res["2fasecret"];
    $imgsrc = $gauth->getQRCodeGoogleUrl("CloudBooks " . $CNAME, $secretcode);
}
$tfa_final_state = $tfastatus_res["2fa"];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["mode"]) && isset($_POST["otp"])) {
    switch ($_POST["mode"]) {
        case "enable":
            //check code
            if ($gauth->verifyCode($secretcode, $_POST["otp"])) {
                //code is correct, store into database the new state
                $pre = "UPDATE `users` SET `2fa` = 2 WHERE `id` = ?";
                $insert_stmt = $SQLINK->prepare($pre);
                $insert_stmt->bind_param("i", $par_id);
                $par_id = $accid;
                $insert_stmt->execute();
                $tfa_final_state = "2";
                if ($insert_stmt->affected_rows != 1) {
                    $err = "Errore interno!";
                }
            } else {
                //code is not correct, print err
                $err = "Impossibile verificare il codice.";
            }
            break;
        case "disable":
            if ($gauth->verifyCode($secretcode, $_POST["otp"])) {
                //code is correct, clear db and generate fresh code
                $secretcode = $gauth->createSecret();
                $imgsrc = $gauth->getQRCodeGoogleUrl("CloudBooks " . $CNAME, $secretcode);
                //insert in db pending state
                $pre = "UPDATE `users` SET `2fa` = 1, `2fasecret` = ? WHERE `id` = ?";
                $update_stmt = $SQLINK->prepare($pre);
                $update_stmt->bind_param("si", $par_secret_ud, $par_id_ud);
                $par_secret_ud = $SQLINK->real_escape_string($secretcode);
                $par_id_ud = $accid;
                $update_stmt->execute();
                $tfa_final_state = "1";
            } else {
                //code is not correct, print err
                $err = "Impossibile verificare il codice.";
            }
            break;
        default:
            $err = "Errore interno!";
    }
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CloudBooks - Impostazioni 2FA</title>
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
        <!-- <input class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search"> -->
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
                            <a class="nav-link" href="<?php echo $INSTALL_LINK; ?>account/">
                                <span data-feather="lock"></span>
                                Cambia Password
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="<?php echo $INSTALL_LINK; ?>account/2fasettings/">
                                <span data-feather="layers"></span>
                                Autenticazione a due fattori <span class="sr-only">(current)</span>
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
                    <h1 class="h2">Autenticazione a due fattori</h1>
                </div>
                <?php
                if ($tfa_final_state != "2") {
                    displayEnable2faForm($imgsrc, $err);
                } else {
                    displayDisable2faForm($err);
                }
                ?>
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