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
require_once "config.php";
define("AM_BACKUP", "1");
define("AM_USERS", "2");
define("AM_TEST", "3");
define("AM_LOG", "4");
define("AM_SETTINGS", "5");
//
define("SM_DASHBOARD", "1");
define("SM_BOOKINGS", "2");
define("SM_BHISTORY", "3");
define("SM_RESTAURANT", "4");
define("SM_RHISTORY", "5");
define("SM_PERSONALDATA", "6");
define("SM_ANALYTICS", "7");
define("SM_CHANGEPWD", "8");
define("SM_2FA", "9");

//obtain ip address
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

function insertLog($ip, $calledfrom, $action)
{
    global $SQLINK;
    $SQLINK->query("INSERT INTO `logs` (`timestamp`, `ipaddr`, `calledfrom`, `action`) VALUES (current_timestamp(), '$ip', '$calledfrom', '$action')");
}

function loginError($error = "Errore interno!")
{
    return "<div class='alert alert-danger' role='alert'>" . $error . "</div>";
}

function alertOk($message = "OK!")
{
    return "<div class='alert alert-success' role='alert'>" . $message . "</div>";
}

function setLoginTries($tries, $id)
{
    global $SQLINK;
    $SQLINK->query("UPDATE `users` SET `logintries` = '$tries' WHERE `id` = $id");
}

function displayAdminControls($active = 0)
{
    $a1 = "active";
    $a2 = '<span class="sr-only">(current)</span>';
    global $INSTALL_LINK;
    $templatefile = fopen(__DIR__ . "/res/htmltemplates/admincontrols.html", "r");
    $template = fread($templatefile, filesize(__DIR__ . "/res/htmltemplates/admincontrols.html"));
    fclose($templatefile);
    switch ($active) {
        case 1:
            echo sprintf($template, $a1, $INSTALL_LINK, $a2,   "", $INSTALL_LINK, "",   "", $INSTALL_LINK, "",   "", $INSTALL_LINK, "",   "", $INSTALL_LINK, "");
            break;
        case 2:
            echo sprintf($template, "", $INSTALL_LINK, "",   $a1, $INSTALL_LINK, $a2,   "", $INSTALL_LINK, "",   "", $INSTALL_LINK, "",   "", $INSTALL_LINK, "");
            break;
        case 3:
            echo sprintf($template, "", $INSTALL_LINK, "",   "", $INSTALL_LINK, "",   $a1, $INSTALL_LINK, $a2,   "", $INSTALL_LINK, "",   "", $INSTALL_LINK, "");
            break;
        case 4:
            echo sprintf($template, "", $INSTALL_LINK, "",   "", $INSTALL_LINK, "",   "", $INSTALL_LINK, "",   $a1, $INSTALL_LINK, $a2,   "", $INSTALL_LINK, "");
            break;
        case 5:
            echo sprintf($template, "", $INSTALL_LINK, "",   "", $INSTALL_LINK, "",   "", $INSTALL_LINK, "",   "", $INSTALL_LINK, "",   $a1, $INSTALL_LINK, $a2);
            break;
        default:
            echo sprintf($template, "", $INSTALL_LINK, "",   "", $INSTALL_LINK, "",   "", $INSTALL_LINK, "",   "", $INSTALL_LINK, "",   "", $INSTALL_LINK, "");
    }
}

function displayEnable2faForm($src, $err = "none")
{
    $templatefile = fopen(__DIR__ . "/res/htmltemplates/2faenable.html", "r");
    $template = fread($templatefile, filesize(__DIR__ . "/res/htmltemplates/2faenable.html"));
    fclose($templatefile);
    if ($err == "none") {
        $err_ok = "";
    } else {
        $err_ok = "<div class='alert alert-danger' role='alert'>" . $err . "</div>";
    }
    echo sprintf($template, $src, $err_ok, date("Y"));
}

function displayDisable2faForm($err = "none")
{
    $templatefile = fopen(__DIR__ . "/res/htmltemplates/2fadisable.html", "r");
    $template = fread($templatefile, filesize(__DIR__ . "/res/htmltemplates/2fadisable.html"));
    fclose($templatefile);
    if ($err == "none") {
        $err_ok = "";
    } else {
        $err_ok = "<div class='alert alert-danger' role='alert'>" . $err . "</div>";
    }
    echo sprintf($template, $err_ok, date("Y"));
}

function displayOTPField()
{
    $templatefile = fopen(__DIR__ . "/res/htmltemplates/otpfield.html", "r");
    $template = fread($templatefile, filesize(__DIR__ . "/res/htmltemplates/otpfield.html"));
    fclose($templatefile);
    echo $template;
}

function printNavigatorClose(){
    echo "</div>
    </nav>";
}

function printNavigator($active = 0){
    global $INSTALL_LINK;
    $a1 = "active";
    $a2 = '<span class="sr-only">(current)</span>';
    $templatefile = fopen(__DIR__ . "/res/htmltemplates/navigator.html", "r");
    $template = fread($templatefile, filesize(__DIR__ . "/res/htmltemplates/navigator.html"));
    fclose($templatefile);
    $parray = array(0 => "", 1 => "$INSTALL_LINK", 2 => "",      //1
                    3 => "", 4 => "$INSTALL_LINK", 5 => "",      //2
                    6 => "", 7 => "$INSTALL_LINK", 8 => "",      //3
                    9 => "", 10 => "$INSTALL_LINK", 11 => "",    //4
                    12 => "", 13 => "$INSTALL_LINK", 14 => "",   //5
                    15 => "", 16 => "$INSTALL_LINK", 17 => "",   //6
                    18 => "", 19 => "$INSTALL_LINK", 20 => "",   //7
                    21 => "", 22 => "$INSTALL_LINK", 23 => "",   //8
                    24 => "", 25 => "$INSTALL_LINK", 26 => "");  //9
    //build menu
    switch($active){
        case 1:
            $parray[0] = $a1;
            $parray[2] = $a2;
        break;
        case 2:
            $parray[3] = $a1;
            $parray[5] = $a2;
        break;
        case 3:
            $parray[6] = $a1;
            $parray[8] = $a2;
        break;
        case 4:
            $parray[9] = $a1;
            $parray[11] = $a2;
        break;
        case 5:
            $parray[12] = $a1;
            $parray[14] = $a2;
        break;
        case 6:
            $parray[15] = $a1;
            $parray[17] = $a2;
        break;
        case 7:
            $parray[18] = $a1;
            $parray[20] = $a2;
        break;
        case 8:
            $parray[21] = $a1;
            $parray[23] = $a2;
        break;
        case 9:
            $parray[24] = $a1;
            $parray[26] = $a2;
        break;
        default:
            //array is fine as is
    }
    echo vsprintf($template, $parray);
}

function printBaseDeps()
{
    $templatefile = fopen(__DIR__ . "/res/htmltemplates/basedeps.html", "r");
    $template = fread($templatefile, filesize(__DIR__ . "/res/htmltemplates/basedeps.html"));
    fclose($templatefile);
    echo $template;
}

function printHead($title)
{
    global $INSTALL_LINK;
    $templatefile = fopen(__DIR__ . "/res/htmltemplates/head.html", "r");
    $template = fread($templatefile, filesize(__DIR__ . "/res/htmltemplates/head.html"));
    fclose($templatefile);
    echo sprintf($template, $title, $INSTALL_LINK, $INSTALL_LINK);
}

function printBar($username){
    global $INSTALL_LINK;
    global $CNAME;
    $templatefile = fopen(__DIR__ . "/res/htmltemplates/topbar.html", "r");
    $template = fread($templatefile, filesize(__DIR__ . "/res/htmltemplates/topbar.html"));
    fclose($templatefile);
    echo sprintf($template, $CNAME, $INSTALL_LINK, $username);
}

function printTableRow(...$params){
    echo "<tr>";
    foreach ($params as $s){
        echo "<td>".$s."</td>";
    }
    echo "</tr>";
}

function printStatusJson($res){
    $template = '{
        "status": "%s"
    }';
    echo sprintf($template, $res);
}