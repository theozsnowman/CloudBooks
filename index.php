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
if (!file_exists("config.php")) {
    die("Please run installer in /installer directory");
}
session_start();

//check if user is logged in
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    if ($_SESSION["2fa"] == "notneeded" | $_SESSION["2fa"] == "ok"){
        header("location: ".$INSTALL_LINK."dashboard/");
        exit;
    }else{
        header("location: ".$INSTALL_LINK."2fa/");
    }
}

//import config
require_once "config.php";
require_once "functions.php";
$username = $password = $err = "";

//process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["user"])) | strlen(trim($_POST["user"])) > 25 | strlen(trim($_POST["pass"])) < 4) {
        //username error
        $err = "Username non valido";
    } else {
        //escape and store
        $username = $SQLINK->real_escape_string(trim($_POST["user"]));
    }

    if (empty(trim($_POST["pass"])) | strlen(trim($_POST["pass"])) > 72 | strlen(trim($_POST["pass"])) < 6) {
        //password error
        $err = "Password non valida";
    } else {
        $password = $SQLINK->real_escape_string(trim($_POST["pass"]));
    }

    //if username and password are valid
    if (empty($err)) {
        //prepare statement (select)
        $stmt = $SQLINK->prepare("SELECT id, username, password, 2fa, acctype, logintries FROM users WHERE username = ?");
        $stmt->bind_param("s", $param_username);
        $param_username = $username;
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows() == 1) {
                $stmt->bind_result($res_id, $res_user, $res_pass, $res_2fa, $res_acctype, $logintries);
                if ($stmt->fetch()) {
                    if (password_verify($password, $res_pass)) {
                        if ($logintries <= "5") {
                            //password is ok
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $res_id;
                            $_SESSION["username"] = $username;
                            $_SESSION["type"] = $res_acctype;
                            //reset tries + log
                            setLoginTries(0, $res_id);
                            insertLog($ip, "login", "ACCEPTED FOR USER " . $res_user);
                            //check if 2fa is enabled
                            if ($res_2fa == "2") {
                                $_SESSION["2fa"] = "tocheck";
                                header("location: ".$INSTALL_LINK."2fa/");
                            } else {
                                $_SESSION["2fa"] = "notneeded";
                                updateLastLogin($res_id);
                                header("location: ".$INSTALL_LINK."dashboard/");
                            }
                        } else {
                            $err = loginError("Utente sconosciuto!");
                            insertLog($ip, "login", "DENIED FOR LOCKED USER " . $res_user);
                        }
                    } else {
                        $err = loginError("Utente sconosciuto!");
                        //increment login tries
                        setLoginTries($logintries + 1, $res_id);
                        insertLog($ip, "login", "DENIED FOR USER " . $res_user);
                    }
                } else {
                    $err = loginError();
                }
            } else {
                $err = loginError("Utente sconosciuto!");
                insertLog($ip, "login", "DENIED FOR UNKNOWN USER " . $param_username);
            }
        } else {
            $err = loginError();
        }
        $stmt->close();
    }
    $SQLINK->close();
}
?>
<!doctype html>
<html lang="en">

<head>
    <?php printHead("Login") ?>
    <link href="<?php echo $INSTALL_LINK;?>res/css/login.css" rel="stylesheet">
</head>

<body>
    <form class="form-signin" action="" method="post">
        <div class="text-center mb-4">
            <img class="mb-4" src="<?php echo $INSTALL_LINK;?>res/img/logo.png" width="391" height="279" style="margin: 0px !important">
        </div>
        <div class="form-label-group">
            <input type="text" name="user" id="inputUsername" class="form-control" placeholder="Nome Utente" required autofocus>
            <label for="inputUsername">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-person" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M10 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm6 5c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
            </svg>
            Nome Utente
            </label>
        </div>
        <div class="form-label-group">
            <input type="password" name="pass" id="inputPassword" class="form-control" placeholder="Password" required>
            <label for="inputPassword">
                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-key" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8zm4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5z"/>
                    <path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                </svg>
            Password
            </label>
        </div>
        <?php echo $err; ?>
        <button class="btn btn-lg btn-primary btn-block" type="submit">
        Accedi
        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-bar-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M6 8a.5.5 0 0 0 .5.5h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L12.293 7.5H6.5A.5.5 0 0 0 6 8zm-2.5 7a.5.5 0 0 1-.5-.5v-13a.5.5 0 0 1 1 0v13a.5.5 0 0 1-.5.5z"/>
        </svg>
        </button>
        <p class="mt-5 mb-3 text-muted text-center">Struttura attiva: <?php echo $CNAME; ?><br>
        Gestionale CloudBooks - &copy; <?php echo date("Y"); ?> Vittorio Lo Mele</p>
    </form>
</body>

</html>