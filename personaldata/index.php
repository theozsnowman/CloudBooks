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
?>

<!doctype html>
<html lang="en">

<head>
    <?php printHead("Anagrafiche Clienti") ?>
    <link href="<?php echo $INSTALL_LINK; ?>res/css/basestyle.css" rel="stylesheet">
</head>

<body>
    <?php printBar($_SESSION["username"]) ?>
    <div class="container-fluid">
        <div class="row">
            <?php
            printNavigator(SM_PERSONALDATA);
            //if admin display admin controls
            if ($_SESSION["type"] == "1") {
                displayAdminControls();
            }
            printNavigatorClose();
            ?>

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Anagrafiche Clienti</h1>
                </div>
            </main>

        </div>
    </div>
    <?php printBaseDeps() ?>
    <script src="<?php echo $INSTALL_LINK; ?>res/js/dashboard.js"></script>
</body>

</html>