<!DOCTYPE html>
<?php
require_once('../other/php/utils.php');
session_start();
//A questa pagina ci si può accedere solo se si è loggati
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
    header('Location: ../Login');
} else if (!isset($_SESSION['user']) || $_SESSION['user'] != 0) {
    header('Location: ../');
}
$dbh = connect();
    // Nome e cognome dell'utente loggato
    $res = sqlSelect($dbh, 'SELECT Nome, Cognome from studenti where ID = ?', 'i', $_SESSION['userID']);
    $utente = $res->fetch_assoc();
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="../other/js/functions.js"></script>
    <script src="https://kit.fontawesome.com/48c9af8c84.js" crossorigin="anonymous"></script>
    <title>I Miei Ordini</title>
</head>

<body>
    <div class="header">
        <center>
            <h1 id="titolo">Ordini in corso</h1>
        </center>
        <div class="navbar-icon" onclick="openNav()">
            <i class="fas fa-bars"></i>
        </div>
    </div>
    <div id="mySidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <p>
            <?php echo $utente['Nome'] . ' ' . $utente['Cognome'] ?>
        </p>
        <a href="index.php" class='scelta'>Ordina</a>
        <a href="IMieiOrdini.php" class='scelta'>I Miei Ordini</a>
        <a href="#" onclick="logout()" id="esci">Esci</a>
    </div>
    <div id="background"></div>
</body>
<?php

$today = date('Y-m-d'); // data corrente
// Query per prendere l'orario entro cui si possono effettuare gli ordini
$sql = "SELECT orari.ID, orari.IDGiorno, orari.Orario FROM `orari`, istituti, studenti WHERE orari.IDIstituto = istituti.ID AND studenti.IDIstituto = istituti.ID AND studenti.ID = ? AND orari.IDGiorno = ? ORDER BY orari.Orario ASC;";
$res = sqlSelect($dbh, $sql, 'ii', $_SESSION['userID'], getDayID($dbh));
$orari = $res->fetch_all(MYSQLI_ASSOC);
$res->free();
$deadline = null;
// Imposto la past_deadline alle 8:00 del mattino in modo da selezionare nelle query solo gli ordini che partono dalle 8 di mattina
$past_deadline = INIZIO_ORDINI;
$past_deadline = strtotime("$today $past_deadline"); // Converto in formato unix
foreach ($orari as $orario) {
    $date_string = date("Y-m-d") . $orario['Orario']; // Combina la data di oggi con l'ora specificata
    $timestamp = strtotime($date_string); // Converte la stringa in un timestamp Unix
    if (isOnTime(time(), $timestamp)) {
        $deadline = $orario['Orario'];
        $deadline = strtotime("$today $deadline"); // Converto in formato unix
        break;
    } else {
        $past_deadline = $orario['Orario'];
        $past_deadline = strtotime("$today $past_deadline"); // Converto in formato unix
    }
}
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../other/js/script.js"></script>

</html>