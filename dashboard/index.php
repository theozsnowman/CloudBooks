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
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>CloudBooks - Dashboard</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
  <link rel="shortcut icon" href="<?php echo $INSTALL_LINK;?>res/img/favicon.ico" type="image/x-icon">
  <link rel="icon" href="<?php echo $INSTALL_LINK;?>res/img/favicon.ico" type="image/x-icon">
  <meta name="theme-color" content="#563d7c">
  <link href="<?php echo $INSTALL_LINK;?>res/css/basestyle.css" rel="stylesheet">
</head>

<body>
  <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-3" href="#">CloudBooks - <?php echo $CNAME;?></a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-toggle="collapse" data-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <!-- <input class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search"> -->
    <ul class="navbar-nav px-3">
      <li class="nav-item text-nowrap">
        <a class="nav-link" href="<?php echo $INSTALL_LINK; ?>logout.php"><span data-feather="user"></span> <?php echo $_SESSION["username"]; ?>   -   Esci <span data-feather="log-out"></span></a>
      </li>
    </ul>
  </nav>

  <div class="container-fluid">
    <div class="row">
      <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
        <div class="sidebar-sticky pt-3">
          <ul class="nav flex-column">
            <li class="nav-item">
              <a class="nav-link active" href="<?php echo $INSTALL_LINK;?>dashboard">
                <span data-feather="home"></span>
                Dashboard <span class="sr-only">(current)</span>
              </a>
            </li>
          </ul>

          <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Camere</span>
          </h6>
          <ul class="nav flex-column mb-2">
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $INSTALL_LINK;?>bookings/">
                <span data-feather="calendar"></span>
                Prenotazioni Attive
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $INSTALL_LINK;?>bookings/history/">
                <span data-feather="archive"></span>
                Storico
              </a>
            </li>
          </ul>

          <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Tavoli</span>
          </h6>
          <ul class="nav flex-column mb-2">
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $INSTALL_LINK;?>restaurant/">
                <span data-feather="coffee"></span>
                Occupati Oggi
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $INSTALL_LINK;?>restaurant/history">
                <span data-feather="archive"></span>
                Storico
              </a>
            </li>
          </ul>

          <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Clienti</span>
          </h6>
          <ul class="nav flex-column mb-2">
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $INSTALL_LINK;?>personaldata/">
                <span data-feather="user"></span>
                Anagrafiche Clienti
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $INSTALL_LINK;?>analytics/">
                <span data-feather="trending-up"></span>
                Analytics
              </a>
            </li>
          </ul>

          <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Account</span>
          </h6>
          <ul class="nav flex-column mb-2">
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $INSTALL_LINK;?>account/">
                <span data-feather="lock"></span>
                Cambia Password
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $INSTALL_LINK;?>account/2fasettings/">
                <span data-feather="layers"></span>
                Autenticazione a due fattori
              </a>
            </li>
          </ul>

          <?php 
          //if admin display admin controls
          if($_SESSION["type"] == "1"){
          displayAdminControls();}
          ?>

        </div>
      </nav>

      <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">

      </main>

    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://getbootstrap.com/docs/4.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-LtrjvnR4Twt/qOuYxE721u19sVFLVSA4hf/rRt6PrZTmiPltdZcI7q7PXQBYTKyf" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.9.0/feather.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>
  <script src="<?php echo $INSTALL_LINK;?>res/js/dashboard.js"></script>
</body>

</html>