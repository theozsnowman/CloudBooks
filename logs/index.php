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
require "../res/libs/dompdf/autoload.inc.php";
use Dompdf\Dompdf;

if (!isset($_SESSION["loggedin"]) | $_SESSION["loggedin"] != true) {
    header("location: " . $INSTALL_LINK);
    exit;
}

if ($_SESSION["2fa"] == "tocheck") {
    header("location: " . $INSTALL_LINK . "2fa/");
    exit;
}

if ($_SESSION["type"] != "1") {
    //if user not admin kick out
    header("location: " . $INSTALL_LINK . "logout.php");
    exit;
}

//get table data
$result = $SQLINK->query("SELECT * FROM `logs`");

if(isset($_GET["getpdf"]) && $_GET["getpdf"] == "1"){
    $dompdf = new DOMPDF();
    $DOM = "
        <html>
            <head>
                <style>
                    table, th, td {
                      border: 1px solid black;
                      border-collapse: collapse;
                    }
                </style>
            </head>
            <body>
                <h3>LOG ESPORTATI IL " . date("d/m/Y") . "</h3>
                <table style=''>
                    <thead>
                        <tr>
                            <th>Data ed Ora</th>
                            <th>Indirizzo IP</th>
                            <th>Sorgente</th>
                            <th>Descrizione</th>
                        </tr>
                    </thead>
                    <tbody>";
        while ($row = $result->fetch_array()) {
            $DOM = $DOM. retTableRow($row["timestamp"], $row["ipaddr"], $row["calledfrom"], $row["action"]);
        }
        $DOM = $DOM. "</tbody>
                    </table>
                </body>
            </html>";
        $dompdf->loadHtml($DOM);
        $dompdf->render();
        ob_end_clean();
        $dompdf->stream("logs". date("dmY"). ".pdf");
        header("location: ./");
        die;
}
?>
<!doctype html>
<html lang="en">

<head>
    <?php printHead("Ispezione Log") ?>
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
                displayAdminControls(AM_LOG);
            }
            printNavigatorClose();
            ?>

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Ispezione Log</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearTable('<?php echo $INSTALL_LINK.'/logs/cleartbl.php'; ?>');">RIPULISCI TABELLA</button>
                        <a style="color: #FFF"> - </a> <!-- spacer -->
                        <a href="?getpdf=1" class="btn btn-sm btn-outline-primary">SCARICA PDF</a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="logtbl" class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Data ed Ora</th>
                                <th>Indirizzo IP</th>
                                <th>Sorgente</th>
                                <th>Descrizione</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row = $result->fetch_array()) {
                                printTableRow($row["timestamp"], $row["ipaddr"], $row["calledfrom"], $row["action"]);
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </main>

        </div>
    </div>
    <?php printBaseDeps() ?>
    <script src="<?php echo $INSTALL_LINK; ?>res/js/inspectlogs.js"></script>
</body>

</html>