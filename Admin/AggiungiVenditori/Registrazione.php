<?php
include_once("../../other/php/utils.php");
session_start();
$dbh = connect();
//prendo i dati dal form
$nome = $_POST["nome"];
$email = $_POST["email"];
$password = $_POST["password"];

// Controllo che i campi siano stati completati
$errors = [];
if (empty($_POST['nome'])) {
    $errors[] = 1;

}
if (empty($_POST['email'])) {
    $errors[] = 2;

}
if (empty($_POST['password'])) {
    $errors[] = 3;

}
if (count($errors) == 0) {
    $hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $sql = "INSERT INTO venditori (ID,Nome,Email,Password)";
    $sql .= "VALUES ('',?,?,?)";
    $res = sqlInsert($dbh, $sql, 'sss', $nome, $email, $hash);
    echo 'Venditore inserito con successo!';
}
$dbh->close();

?>