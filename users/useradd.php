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
$closetab = false;
if (!isset($_SESSION["loggedin"]) | $_SESSION["loggedin"] != true) {
    $closetab = true;
}

if ($_SESSION["2fa"] == "tocheck") {
    $closetab = true;
}

if ($_SESSION["type"] != "1"){
    //if user not admin kick out
    $closetab = true;
}

?>
<!doctype html>
<html lang="en">
<head>
    <?php printHead("Aggiunta nuovo utente") ?>
    <link href="<?php echo $INSTALL_LINK; ?>res/css/diagstyle.css" rel="stylesheet">
</head>
<body>
<?php printTopBarDiag("Aggiunta nuovo utente") ?>
<main role="main">
    <form action="" method="post">
        <div class="container-fluid">
            <div class="form-label-group">
                <input type="text" name="user" id="inputUsername" class="form-control" placeholder="Nome Utente" required autofocus>
                <label for="inputUsername">
                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-person" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm6 5c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                    </svg>
                    Nome Utente
                </label>
            </div>

            <div class="form-row">
                <div class="col form-label-group">
                    <input type="password" name="pass" id="inputPassword" class="form-control" placeholder="Password" required>
                    <label for="inputPassword">
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-key" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8zm4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5z"/>
                            <path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                        </svg>
                        Password
                    </label>
                </div>
                <div class="col form-label-group">
                    <input type="password" name="verpass" id="inputCheckPass" class="form-control" placeholder="Conferma Password" required autofocus>
                    <label for="inputCheckPass">
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-person" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm6 5c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                        </svg>
                        Conferma Password
                    </label>
                </div>
            </div>
        </div>
    </form>
</main>
<?php printBaseDeps() ?>
<script>
    feather.replace()
</script>
<?php if($closetab == true){
    echo "<script>window.close();</script>";
} ?>
</body>
</html>
