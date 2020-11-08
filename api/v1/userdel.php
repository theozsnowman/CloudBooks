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

switch($_SERVER["REQUEST_METHOD"]){
    case "GET":
        $id = $_GET["id"];
        $r = $_GET["r"];
        break;
    case "POST":
        $id = $_POST["id"];
        $r = $_POST["r"];
        break;
    default:
        die("ERROR! Invalid request method.");
}

session_start();
require_once "../../config.php";
require_once "../../functions.php";

if (!isset($_SESSION["loggedin"]) | $_SESSION["loggedin"] != true) {
    if($r == "1") {
        header("location: " . $INSTALL_LINK);
        exit;
    }else{
        die("Invalid session cookie");
    }
}

if ($_SESSION["2fa"] == "tocheck") {
    if($r == "1"){
        header("location: ".$INSTALL_LINK."2fa/");
        exit;
    }else{
        die("Invalid session cookie");
    }
}

if ($_SESSION["type"] != "1"){
    if($r=="1"){
        header("location: " . $INSTALL_LINK . "logout.php");
        exit;
    }else{
        die("Invalid session cookie");
    }
}

switch($_SERVER["REQUEST_METHOD"]){
    case "GET":
        $id = $_GET["id"];
        $r = $_GET["r"];
        break;
    case "POST":
        $id = $_POST["id"];
        $r = $_POST["r"];
        break;
    default:
        die("ERROR! Invalid request method.");
}

$id = $SQLINK->real_escape_string($id);
if(is_numeric($id) & $id != "1"){
    $q = $SQLINK->query("DELETE * FROM `users` WHERE `id` = $id");
    if(mysqli_num_rows($q) != 1){
        die("Invalid user id");
    }else{
        die("Success");
    }
}else{
    die("Invalid user id");
}

