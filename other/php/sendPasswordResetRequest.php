<?php
require_once 'utils.php';
// Verifico che non ci sia già una sessione attiva altrimenti ne avvio una
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['email']) && isset($_POST['Token']) && $_POST['Token'] == $_SESSION['Token']) {
    $email = $_POST['email'];
    $errors = [];
    $dbh = connect();
    if ($dbh) {
        // Distinguo venditori da studenti
        $oneDayAgo = time() - 60 * 60 * 24;
        if ($_POST['usertype'] == 0) {
            $sql = 'SELECT studenti.ID,Nome,COUNT(requests.ID) as NumRichieste FROM studenti LEFT JOIN requests ON studenti.ID = requests.IDUtente AND Tipo=1 AND Usertype = 0 AND Orario > ? WHERE Email=? GROUP BY studenti.ID';
            $res = sqlSelect($dbh, $sql, 'is', $oneDayAgo, $email);
            $link = "Login";
        } else if ($_POST['usertype'] == 1) {
            $sql = 'SELECT venditori.ID,Nome,COUNT(requests.ID) as NumRichieste FROM venditori LEFT JOIN requests ON venditori.ID = requests.IDUtente AND Tipo=1 AND Usertype = 1 AND Orario > ? WHERE Email=? GROUP BY venditori.ID';
            $res = sqlSelect($dbh, $sql, 'is', $oneDayAgo, $email);
            $link = 'LoginVenditori';
        }
        if ($res && $res->num_rows === 1) {
            $utente = $res->fetch_assoc();
            if ($utente['NumRichieste'] <= MAX_PASSWORD_RESET_REQUESTS_PER_DAY) {
                // Mando il link per verificare la mail
                $verifyCode = random_bytes(32);
                $hash = password_hash($verifyCode, PASSWORD_BCRYPT);
                $requestID = sqlInsert($dbh, 'INSERT INTO requests VALUES (NULL, ?, ?, ?, 1, ?)', 'isii', $utente['ID'], $hash, time(), $_POST['usertype']);
                if ($requestID !== -1) {
                    $email_body = '<div style="font-family: Arial, Helvetica, sans-serif;
						font-size: 16px;
						line-height: 1.5;
						color: #444444;"><p>Ciao ' . $utente['Nome'] . ',</p>
						<p>Clicca sul seguente link per cambiare password:</p>
						<a href="localhost/' . $link . '/RecuperaPassword.php?id=' . $requestID . '&hash=' . urlSafeEncode($verifyCode) . '" style="color: #007bff;
						text-decoration: none;">Cambia Password</a>
						<p>Se il link non funziona, copia e incolla questo URL nella barra degli indirizzi del tuo browser:</p>
						<p>localhost/' . $link . '/RecuperaPassword.php?id=' . $requestID . '&hash=' . urlSafeEncode($verifyCode) . '</p>
						<p>Grazie per aver scelto il nostro servizio.</p>
						<p>Il team del sito</p></div>';
                    if (sendEmail($email, $utente['Nome'], 'Password Reset', $email_body)) {
                        $errors[] = 0;
                    } else {
                        // Problema nell'invio della mail
                        $errors[] = 1;
                    }
                } else {
                    // Problema nell'inserimento della richiesta nel db
                    $errors[] = 2;
                }
            } else {
                // Numero di richieste giornaliere superato
                $errors[] = 3;
            }
            $res->free_result();
        } else {
            //Email non esistente
            $errors[] = 5;
        }
        $dbh->close();
    } else {
        //Connessione al database fallita
        $errors[] = 6;
    }
} else {
    $errors[] = 4;

}
echo json_encode($errors);
?>