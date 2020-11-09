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

if (!isset($_SESSION["loggedin"]) | $_SESSION["loggedin"] != true) {
  header("location: ".$INSTALL_LINK);
  exit;
}

if ($_SESSION["2fa"] == "tocheck") {
  header("location: ".$INSTALL_LINK."2fa/");
  exit;
}

if ($_SESSION["type"] != "1"){
    //if user not admin kick out
    header("location: " . $INSTALL_LINK . "logout.php");
    exit;
}

$result = $SQLINK->query("SELECT `id`, `username`, `2fa`, `acctype`, `logintries`, `lastlogin` FROM `users`");
?>

<!doctype html>
<html lang="en">

<head>
    <?php printHead("Gestione Utenti") ?>
    <link href="<?php echo $INSTALL_LINK; ?>res/css/basestyle.css" rel="stylesheet">
</head>

<body>
    <?php printBar($_SESSION["username"]) ?>
    <div class="container-fluid">
        <div class="row">
            <?php
            printNavigator();
            //if admin display admin controls
            if ($_SESSION["type"] == "1") {
                displayAdminControls(AM_USERS);
            }
            printNavigatorClose();
            ?>

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Gestione Utenti</h1>
                    <a onclick="window.open('useradd.php', '_blank', 'location=yes,height=550,width=433,scrollbars=no,status=no,resizable=no');" class="btn btn-sm btn-outline-primary">AGGIUNGI UTENTE</a>
                </div>
                    <div class="table-responsive">
                        <table id="usertbl" class="table table-striped table-sm">
                            <thead>
                            <tr>
                                <th>ID Utente</th>
                                <th>Nome Utente</th>
                                <th>Stato 2FA</th>
                                <th>Tipo Account</th>
                                <th>Stato Account</th>
                                <th>Ultimo Accesso</th>
                                <th>Modifica Utente</th>
                                <th>Elimina Utente</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            while ($row = $result->fetch_array()){
                                if($row["2fa"] == "2"){
                                    $tfastate = "<div class='s_boldgreen'>ATTIVA</div>";
                                }else{
                                    $tfastate = "<div class='s_boldred'>NON ATTIVA</div>";
                                }
                                if($row["acctype"] == "1"){
                                    $acctype = "AMMINISTRATORE";
                                }else{
                                    $acctype = "STANDARD";
                                }
                                if($row["logintries"] <= 5){
                                    $accstate = "<div class='s_boldgreen'>ATTIVO</div>";
                                }else{
                                    $accstate = "<div class='s_boldred'>BLOCCATO</div>";
                                }
                                if($row["id"] != "1"){
                                    $modbtn = "<a href='#' onclick=\"window.open('usermod.php', '_blank', 'location=yes,height=550,width=467,scrollbars=no,status=no,resizable=no');\">" . PencilSVGCode(1) . " MODIFICA</a>";
                                    $delbtn = "<a href='" . $INSTALL_LINK . "api/v1/userdel.php?id=" . $row["id"] . "&r=1'>". XcSVGCode(1) ." ELIMINA</a>";
                                }else{
                                    $modbtn = "<div class='s_boldred'>NO </div>";
                                    $delbtn = "<div class='s_boldred'>NO</div>";
                                }

                                printTableRow($row["id"], $row["username"], $tfastate, $acctype, $accstate, $row["lastlogin"], $modbtn, $delbtn);
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
            </main>

        </div>
    </div>
    <?php printBaseDeps() ?>
    <script src="<?php echo $INSTALL_LINK; ?>res/js/dashboard.js"></script>
</body>

</html>