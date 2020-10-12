<?php
if (!file_exists("../config.php")) {
    die("Please run installer in /installer directory");
}
require_once "../config.php"; 
session_start();
if (!isset($_SESSION["loggedin"]) | $_SESSION["loggedin"] != true) {
    header("location: ".$INSTALL_LINK);
    exit;
}
if ($_SESSION["2fa"] == "notneeded" | $_SESSION["2fa"] == "ok"){
    header("location: ".$INSTALL_LINK."dashboard/");
    exit;
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CloudBooks - 2FA</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <meta name="theme-color" content="#563d7c">
    <link rel="shortcut icon" href="<?php echo $INSTALL_LINK;?>res/img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="<?php echo $INSTALL_LINK;?>res/img/favicon.ico" type="image/x-icon">
    <link href="<?php echo $INSTALL_LINK;?>res/css/login.css" rel="stylesheet">
</head>

<body>
    <form class="form-signin" action="" method="post">
        <div class="text-center mb-4">
            <img class="mb-4" src="<?php echo $INSTALL_LINK;?>res/img/logo.png" width="391" height="279" style="margin: 0px !important">
            <h1 class="h3 mb-3 font-weight-normal">Autenticazione a due fattori</h1>
        </div>
        <div class="form-label-group">
            <input type="text" name="otp" id="inOtp" class="form-control" placeholder="Codice di verifica" minlength="6" maxlength="6" required autofocus>
            <label for="inOtp">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check2" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
            </svg>
            Codice di verifica
            </label>
        </div>
        <?php //echo $err; ?>
        <button class="btn btn-lg btn-primary btn-block" type="submit">
        Verifica
        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check2-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M15.354 2.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L8 9.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
            <path fill-rule="evenodd" d="M8 2.5A5.5 5.5 0 1 0 13.5 8a.5.5 0 0 1 1 0 6.5 6.5 0 1 1-3.25-5.63.5.5 0 1 1-.5.865A5.472 5.472 0 0 0 8 2.5z"/>
        </svg>
        </button>
        <p class="mt-5 mb-3 text-muted text-center">Gestionale CloudBooks - &copy; <?php echo date("Y"); ?> Vittorio Lo Mele</p>
    </form>
</body>

</html>