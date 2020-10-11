<?php
if(!file_exists("config.php")){
    die("Please run installer in /installer directory");
}

require_once "config.php";

function insertLog($ip, $calledfrom, $action)
{
    $SQLINK->query("INSERT INTO `logs` (`timestamp`, `ipaddr`, `calledfrom`, `action`) VALUES (current_timestamp(), '$ip', '$calledfrom', '$action')");
}

function loginError($error = "Errore interno!"){
    return "<div class='alert alert-danger' role='alert'>".$error."</div>";
}
