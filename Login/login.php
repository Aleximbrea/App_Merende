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
        $res = sqlSelect($dbh, "SELECT * FROM Studenti WHERE Email=?;", 's', $_POST['email']);
        $row = $res->fetch_assoc();
        if ($row != null) {
            //Account esistente
            // recupera la password hashata dal database
            $hashed_password = $row['Password'];
            // verifica se la password inviata corrisponde alla password hashata
            if (password_verify($_POST['password'], $hashed_password)) {
                // Accesso riuscito, reindirizza l'utente alla pagina di benvenuto
                // Password corretta verifico che l'utente abbia confermato la mail
                if ($row['EmailVerificata'] == 0) {
                    // Email non verificata
                    $errors[] = 5;
                } else {
                    // Accesso riuscito
                    $_SESSION['user'] = 0; // Il tipo di utente registrato 0 = studente
                    $_SESSION['loggedin'] = true;
                    $_SESSION['userID'] = $row['ID'];
                    setcookie('sessionID', session_id(), [
                        'expires' => time() + (90 * 24 * 60 * 60),
                        //90 giorni
                        'path' => '/',
                        'secure' => true,
                        'httponly' => true,
                        'sameSite' => 'Lax' // Prevenzione attachi CSRF 
                    ]);
                    $errors[] = 0;
                }
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