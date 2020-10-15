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

function displayAdminControls()
{
    global $INSTALL_LINK;
    $templatefile = fopen(__DIR__."/res/htmltemplates/admincontrols.html", "r");
    $template = fread($templatefile, filesize(__DIR__."/res/htmltemplates/admincontrols.html"));
    fclose($templatefile);
    echo sprintf($template, $INSTALL_LINK, $INSTALL_LINK, $INSTALL_LINK, $INSTALL_LINK, $INSTALL_LINK);
}

function displayEnable2faForm($src, $err = "none"){
    $templatefile = fopen(__DIR__."/res/htmltemplates/2faenable.html", "r");
    $template = fread($templatefile, filesize(__DIR__."/res/htmltemplates/2faenable.html"));
    fclose($templatefile);
    if($err = "none"){
        $err_ok = "";
    }else{
        $err_ok = "<div class='alert alert-danger' role='alert'>" . $err . "</div>";
    }
    echo sprintf($template, $src, $err_ok, date("Y"));
    
}

function displayDisable2faForm($err = "none"){
    $templatefile = fopen(__DIR__."/res/htmltemplates/2fadisable.html", "r");
    $template = fread($templatefile, filesize(__DIR__."/res/htmltemplates/2fadisable.html"));
    fclose($templatefile);
    if($err = "none"){
        $err_ok = "";
    }else{
        $err_ok = "<div class='alert alert-danger' role='alert'>" . $err . "</div>";
    }
    echo sprintf($template, $err_ok, date("Y"));
    
}

function displayOTPField()
{
    global $INSTALL_LINK;
    $templatefile = fopen(__DIR__."/res/htmltemplates/otpfield.html", "r");
    $template = fread($templatefile, filesize(__DIR__."/res/htmltemplates/otpfield.html"));
    fclose($templatefile);
    echo $template;
}

