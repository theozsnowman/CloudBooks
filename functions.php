<?php
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

function setLoginTries($tries, $id)
{
    global $SQLINK;
    $SQLINK->query("UPDATE `users` SET `logintries` = '$tries' WHERE `id` = $id");
}

function displayAdminControls()
{
    global $INSTALL_LINK;
    echo sprintf('
    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
    <span>Amministrazione</span>
    </h6>
    <ul class="nav flex-column mb-2">
        <li class="nav-item">
            <a class="nav-link" href="%sbackup/">
                <span data-feather="database"></span>
                Importa/Esporta Backup
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="%susers/">
                <span data-feather="users"></span>
                Gestione Utenti
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="%sselftest/">
                <span data-feather="check"></span>
                Self-Test
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="%slogs/">
                <span data-feather="file-text"></span>
                Ispezione log
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="%ssettings/">
                <span data-feather="settings"></span>
                Impostazioni Software
            </a>
        </li>
    </ul>'
    , $INSTALL_LINK, $INSTALL_LINK, $INSTALL_LINK, $INSTALL_LINK, $INSTALL_LINK);
}
