<?php
if(!file_exists("config.php")){
    die("Please run installer in /installer directory");
}
session_start();

//check if user is logged in
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    if ($_SESSION["2fa"] == "notneeded" | $_SESSION["2fa"] == "ok")
        header("location: dashboard/");
    exit;
}

//import config
require_once "config.php";
require_once "functions.php";
$username = $password = $err = "";

//process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["user"])) | strlen(trim($_POST["user"])) > 25) {
        //username error
        $err = "Username non valido";
    } else {
        //escape and store
        $username = $SQLINK->real_escape_string(trim($_POST["user"]));
    }

    if (empty(trim($_POST["pass"])) | strlen(trim($_POST["pass"])) > 72) {
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
                        if($logintries <= "5"){
                            //password is ok
                            session_start();
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $res_id;
                            $_SESSION["username"] = $username;
                            $_SESSION["type"] = $res_acctype;
                            //reset tries
                            $SQLINK->query("UPDATE `users` SET `logintries` = '0' WHERE `id` = $res_id");
                            //check if 2fa is enabled
                            if ($res_2fa == true) {
                                $_SESSION["2fa"] = "tocheck";
                                header("location: 2fa/");
                            } else {
                                $_SESSION["2fa"] = "notneeded";
                                header("location: dashboard/");
                            }
                        }else{
                            $err = loginError("Utente sconosciuto!");
                        }
                    } else {
                        $err = $err = $err = loginError("Utente sconosciuto!");
                        //increment login tries
                        $actualtries = $logintries + 1;
                        $SQLINK->query("UPDATE `users` SET `logintries` = '$actualtries' WHERE `id` = $res_id");
                    }
                } else {
                    $err = loginError();
                }
            } else {
                $err = loginError("Utente sconosciuto!");
            }
        } else {
            $err = loginError();
        }
        $stmt->close();
    } else {
        $SQLINK->close();
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CloudBooks - Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <meta name="theme-color" content="#563d7c">
    <link href="res/css/login.css" rel="stylesheet">
</head>

<body>
    <form class="form-signin" action="" method="post">
        <div class="text-center mb-4">
            <img class="mb-4" src="res/img/logo.png" width="391" height="279" style="margin: 0px !important">
        </div>
        <div class="form-label-group">
            <input type="text" name="user" id="inputUsername" class="form-control" placeholder="Nome Utente" required autofocus>
            <label for="inputUsername">Nome Utente</label>
        </div>
        <div class="form-label-group">
            <input type="password" name="pass" id="inputPassword" class="form-control" placeholder="Password" required>
            <label for="inputPassword">Password</label>
        </div>
        <?php echo $err; ?>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Accedi</button>
        <p class="mt-5 mb-3 text-muted text-center">Gestionale CloudBooks - &copy; <?php echo date("Y"); ?> Vittorio Lo Mele</p>
    </form>
</body>

</html>