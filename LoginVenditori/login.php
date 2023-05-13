<?php
include_once("../other/php/utils.php");
session_start();
$dbh = connect();
// Controllo che i campi siano stati completati
$errors = [];
if (empty($_POST['email'])) {
    $errors[] = 1;
}
if (empty($_POST['password'])) {
    $errors[] = 2;
}

if (count($errors) == 0) {
    // Verifico che il codice CSRF sia giusto
    if (isset($_POST['Token']) && $_POST['Token'] == $_SESSION['Token']) {
        //Se non ci sono stati errori continuo con le opportune verifiche
        $res = sqlSelect($dbh, "SELECT * FROM venditori WHERE Email=?;", 's', $_POST['email']);
        $row = $res->fetch_assoc();
        if ($row != null) {
            //Account esistente
            // recupera la password hashata dal database
            $hashed_password = $row['Password'];
            // verifica se la password inviata corrisponde alla password hashata
            if (password_verify($_POST['password'], $hashed_password)) {
                // Password corretta verifico che l'utente abbia confermato la mail
                // Accesso riuscito
                $_SESSION['user'] = 1; // Il tipo di utente registrato 1 = venditore
                $_SESSION['loggedin'] = true;
                $_SESSION['userID'] = $row['ID'];
                setcookie('sessionID', session_id(), time() + (90 * 24 * 60 * 60), '/', true, true);
                $errors[] = 0;
            } else {
                // Password non corretta
                $errors[] = 4;
            }
        } else {
            // Account non esistente
            $errors[] = 3;
        }
    } else {
        $errors[] = 6;
    }
}
$dbh->close();
echo json_encode($errors);
?>