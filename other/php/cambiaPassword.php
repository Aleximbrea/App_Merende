<?php
require_once 'utils.php';
// Verifico che non ci sia già una sessione attiva altrimenti ne avvio una
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['password']) && isset($_POST['Token']) && $_POST['Token'] == $_SESSION['Token']) {
    $errors = [];
    $password = $_POST['password'];
    if (strlen($password) < 8) {
        $errors[] = 1;
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $dbh = connect();
        if ($_POST['usertype'] == 1) {
            $sql = "UPDATE `venditori` SET `Password`=? WHERE ID = ?";
            $res = sqlUpdate($dbh, $sql, 'si', $hash, $_POST['userid']);
        } else if ($_POST['usertype'] == 0) {
            $sql = "UPDATE `studenti` SET `Password`=? WHERE ID = ?";
            $res = sqlUpdate($dbh, $sql, 'si', $hash, $_POST['userid']);
        }
        if ($res) {
            if ($_POST['usertype'] == 1) {
                // Cancello le richieste di quell utente
                sqlUpdate($dbh, 'DELETE FROM requests WHERE IDUtente=? AND Tipo=1 AND Usertype=1', 'i', $_POST['userid']);
                $errors[] = 0;
            } else if ($_POST['usertype'] == 0) {
                // Cancello le richieste di quell utente
                sqlUpdate($dbh, 'DELETE FROM requests WHERE IDUtente=? AND Tipo=1 AND Usertype=0', 'i', $_POST['userid']);
                $errors[] = 0;
            }
        }
    }

} else {
    $errors[] = 2;
}
echo json_encode($errors);
?>