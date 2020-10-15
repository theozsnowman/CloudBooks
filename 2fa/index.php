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
session_start();
if (!file_exists("../config.php")) {
    die("Please run installer in /installer directory");
}
require_once "../config.php"; 
require_once "../functions.php"; 
require "../res/libs/PHPGangsta/GoogleAuthenticator.php";
$gauth = new PHPGangsta_GoogleAuthenticator;
if (!isset($_SESSION["loggedin"]) | $_SESSION["loggedin"] != true) {
    header("location: ".$INSTALL_LINK);
    exit;
}
if ($_SESSION["2fa"] == "notneeded" | $_SESSION["2fa"] == "ok"){
    header("location: ".$INSTALL_LINK."dashboard/");
    exit;
}
$err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $sessuser = $SQLINK->real_escape_string($_SESSION["username"]);
    $accid = $SQLINK->real_escape_string($_SESSION["id"]);
    $tfastatus = $SQLINK->query("SELECT `2fasecret`, `logintries` FROM `users` WHERE `id` = '$accid'", MYSQLI_STORE_RESULT);
    $tfastatus_res = $tfastatus->fetch_array();
    $secretcode = $tfastatus_res["2fasecret"];
    $logintries = $tfastatus_res["logintries"];
    if($logintries <= "5"){
        if($gauth->verifyCode($secretcode, trim($_POST["otp"]))){
            $_SESSION["2fa"] = "ok";
            insertLog($ip, "2fa", "ACCEPTED FOR USER $sessuser");
            setLoginTries(0, $accid);
            header("location: ".$INSTALL_LINK."dashboard");
            exit;
        }else{
            $err = loginError("Impossibile verificare codice.");
            //increment login tries
            setLoginTries($logintries + 1, $accid);
            insertLog($ip, "2fa", "DENIED FOR USER " . $sessuser);
        }
    }else{
        //disable account
        $_SESSION = array();
        session_destroy();
        header("location: /");
        exit;
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <?php printHead("2FA") ?>
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
        <?php echo $err; ?>
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