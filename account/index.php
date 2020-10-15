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

//check if request is valid
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["oldpass"]) && isset($_POST["newpass"]) && isset($_POST["newpassre"])) {
    if (!empty($_POST["oldpass"]) && !empty($_POST["newpass"]) && !empty($_POST["newpassre"])) {
        //check length criteria
        if (strlen(trim($_POST["newpass"])) < 6 | strlen(trim($_POST["newpass"])) > 72) {
            $err = loginError("La password non rispetta i criteri di lunghezza.");
        }
        //check if confirm password is ok
        if (trim($_POST["newpass"]) != trim($_POST["newpassre"])) {
            $err = loginError("La conferma password non corrisponde alla password inserita.");
        }
        //check if old password is valid
        if (!password_verify(trim($_POST["oldpass"]), $userdata["password"])) {
            $err = loginError("La password vecchia è errata.");
        }
        //check if otp is needed and not provided | not valid
        if ($userdata["2fa"] == "2") {
            if (!isset($_POST["otp"]) | empty($_POST["otp"])) {
                $err = loginError("Inserire il codice di verifica OTP.");
            }
            if (!$gauth->verifyCode($userdata["2fasecret"], trim($_POST["otp"]))) {
                $err = loginError("Codice di verifica non valido.");
            }
        }
        //if all checks are passed proceed with pass change
        if (empty($err)) {
            $updatequery = "UPDATE `users` SET `password` = ? WHERE `id` = ?";
            $ud_stmt = $SQLINK->prepare($updatequery);
            $ud_stmt->bind_param("si", $p_newpass, $p_accid);
            //setting parameters
            $p_newpass = password_hash(trim($_POST["newpass"]), PASSWORD_BCRYPT);
            $p_accid = $accid;
            //exec
            $ud_stmt->execute();
            //check if affected rows is one, if not err
            if (!$ud_stmt->affected_rows == 1) {
                $err = loginError();
            } else {
                $err = alertOk("Cambio password eseguito con successo!");
            }
            $ud_stmt->close();
        }
    } else {
        $err = loginError("Compila tutti i campi.");
    }
}

?>

<!doctype html>
<html lang="en">

<head>
    <?php printHead("Cambio Password") ?>
    <link href="<?php echo $INSTALL_LINK; ?>res/css/basestyle.css" rel="stylesheet">
    <link href="<?php echo $INSTALL_LINK; ?>res/css/2fasettings.css" rel="stylesheet">

</head>

<body>
    <?php printBar($_SESSION["username"]) ?>
    <div class="container-fluid">
        <div class="row">

            <?php
            printNavigator(SM_CHANGEPWD);
            //if admin display admin controls
            if ($_SESSION["type"] == "1") {
                displayAdminControls();
            }
            printNavigatorClose();
            ?>

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
                        <input type="password" name="oldpass" id="inOldPass" class="form-control" placeholder="Password Vecchia" minlength="6" maxlength="72" required autofocus autocomplete="false">
                        <label for="inOldPass">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-key-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M3.5 11.5a3.5 3.5 0 1 1 3.163-5H14L15.5 8 14 9.5l-1-1-1 1-1-1-1 1-1-1-1 1H6.663a3.5 3.5 0 0 1-3.163 2zM2.5 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" />
                            </svg>
                            Password Vecchia
                        </label>
                    </div>
                    <div class="form-label-group" autocomplete="off">
                        <input type="password" name="newpass" id="inNewPass" class="form-control" placeholder="Password Nuova" minlength="6" maxlength="72" required autocomplete="false">
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
                    if ($userdata["2fa"] == "2") {
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
    <?php printBaseDeps() ?>
    <script src="<?php echo $INSTALL_LINK; ?>res/js/dashboard.js"></script>
</body>

</html>