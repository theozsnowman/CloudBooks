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
$err = "";
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
                    $err = "Errore interno! Riprova...";
                }
            } else {
                //code is not correct, print err
                $err = "Impossibile verificare il codice. Riprova...";
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
                $err = "Impossibile verificare il codice. Riprova...";
            }
            break;
        default:
            $err = "Errore interno! Riprova...";
    }
}

?>

<!doctype html>
<html lang="en">

<head>
    <?php printHead("Impostazioni 2FA") ?>
    <link href="<?php echo $INSTALL_LINK; ?>res/css/basestyle.css" rel="stylesheet">
    <link href="<?php echo $INSTALL_LINK; ?>res/css/2fasettings.css" rel="stylesheet">

</head>

<body>
    <?php printBar($_SESSION["username"]) ?>
    <div class="container-fluid">
        <div class="row">
            <?php
            printNavigator(SM_2FA);
            //if admin display admin controls
            if ($_SESSION["type"] == "1") {
                displayAdminControls();
            }
            printNavigatorClose();
            ?>

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
    <?php printBaseDeps() ?>
    <script src="<?php echo $INSTALL_LINK; ?>res/js/dashboard.js"></script>
    <script>
        //'show err' script
        var error = "<?php echo $err ?>";
        document.addEventListener('DOMContentLoaded', (event) => {
            if (error != "") {
                setTimeout(() => {
                    alert(error);
                }, 50);
            }
        });
    </script>
</body>

</html>