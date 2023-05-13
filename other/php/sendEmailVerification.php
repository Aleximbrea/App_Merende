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
		$oneDayAgo = time() - 60 * 60 * 24;
		$sql = 'SELECT studenti.ID,Nome,EmailVerificata,COUNT(requests.ID) as NumRichieste FROM studenti LEFT JOIN requests ON studenti.ID = requests.IDStudente AND Tipo=0 AND Usertype = 0  AND Orario > ? WHERE Email=? GROUP BY studenti.ID';
		$res = sqlSelect($dbh, $sql, 'is', $oneDayAgo, $email);
		if ($res && $res->num_rows === 1) {
			$studente = $res->fetch_assoc();
			if ($studente['EmailVerificata'] === 0) {
				if ($studente['NumRichieste'] <= MAX_EMAIL_VERIFICATION_REQUESTS_PER_DAY) {
					// Mando il link per verificare la mail
					$verifyCode = random_bytes(32);
					$hash = password_hash($verifyCode, PASSWORD_BCRYPT);
					$requestID = sqlInsert($dbh, 'INSERT INTO requests VALUES (NULL, ?, ?, ?, 0)', 'isi', $studente['ID'], $hash, time());
					if ($requestID !== -1) {
						$email_body = '<div style="font-family: Arial, Helvetica, sans-serif;
						font-size: 16px;
						line-height: 1.5;
						color: #444444;"><p>Ciao ' . $studente['Nome'] . ',</p>
						<p>Ti ringraziamo per esserti registrato al nostro sito. Per confermare la tua email, clicca sul seguente link:</p>
						<a href="localhost/Signup/ConfermaEmail.php?id=' . $requestID . '&hash=' . urlSafeEncode($verifyCode) . '" style="color: #007bff;
						text-decoration: none;">Conferma email</a>
						<p>Se il link non funziona, copia e incolla questo URL nella barra degli indirizzi del tuo browser:</p>
						<p>localhost/Signup/ConfermaEmail.php?id=' . $requestID . '&hash=' . urlSafeEncode($verifyCode) . '</p>
						<p>Grazie per aver scelto il nostro servizio.</p>
						<p>Il team del sito</p></div>';
						if (sendEmail($email, $studente['Nome'], 'Email Verification', $email_body)) {
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
			} else {
				// Email già verificata
				$errors[] = 4;
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
	echo json_encode($errors);
}
?>