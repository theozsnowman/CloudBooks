<?php
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
