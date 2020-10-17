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
    printStatusJson("false");
}

if ($_SESSION["2fa"] == "tocheck") {
    printStatusJson("false");
}

if ($_SESSION["type"] != "1"){
    printStatusJson("false");
}

$result = $SQLINK->query("TRUNCATE `logs`");
insertLog($ip, "login-inspector", "TABLE CLEARED BY USER ". $_SESSION["username"]);
if($result != false){
    printStatusJson("true");
}else{
    printStatusJson("false");
}