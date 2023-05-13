<?php
require_once '../other/php/utils.php';
?>
<html lang="en">
<head>
	<meta name="csrf_token" content="<?php echo createToken(); ?>" />
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Signup</title>
	<link rel="stylesheet" href="style.css">
	<link rel="stylesheet" href="mobile.css">
</head>

<body>
	<?php
	echo '<form action="#" id="validateEmailForm">
		<div class="input_block">
			<h1 style="margin: 0px;">';
	if (isset($_GET['id']) && $_GET['id'] !== '' && isset($_GET['hash']) && $_GET['hash'] !== '') {
		$dbh = connect();
		if ($dbh) {
			$res = sqlSelect($dbh, 'SELECT IDstudente,Hash,Orario FROM requests WHERE ID=? AND Tipo=0', 'i', $_GET['id']);
			if ($res && $res->num_rows === 1) {
				$request = $res->fetch_assoc();
				if ($request['Orario'] > time() - 60 * 60 * 24) {
					if (password_verify(urlSafeDecode($_GET['hash']), $request['Hash'])) {
						if (sqlUpdate($dbh, 'UPDATE studenti SET EmailVerificata=1 WHERE id=?', 'i', $request['IDstudente'])) {
							sqlUpdate($dbh, 'DELETE FROM requests WHERE IDstudente=? AND Tipo=0', 'i', $request['IDstudente']);
							echo 'Email verificata con successo';
						} else {
							echo 'Errore, riprova più tardi';
						}
					} else {
						echo 'Richiesta non valida';
					}
				} else {
					echo 'Richiesta scaduta';
				}
				$res->free_result();
			} else {
				echo 'Richiesta non valida';
			}
			$dbh->close();
		} else {
			echo 'Errore, riprova più tardi';
		}
		echo '</h1>
			</div>
		</form>';
	} else {
		?>
		<form id="validateEmailForm" action="POST" novalidate>
			<h1>Conferma la tua email</h1>
			<p id="messaggio">Ti abbiamo mandato un email contenente il link per verificare il tuo account.</p>
			<div id="errs" class="errorcontainer"></div>
			<div class="input_blocks">
				<div class="input_block">
					<input name='email' id="email" Tipo="text" required autoComplete="off" />
					<span class="error_message" id="err_valemail"></span>
					<label for="vaidateemail">Verifica Email</label>
				</div>
				<input type="submit" value="Verifica" id="verificamail_btn" onclick="sendValidateEmailRequest(event);">
			</div>
			<p>Hai già un account? <a href="../Login/" class="btn">Accedi</a> oppure <a href="../Signup/"
					class="btn">Registrati</a></p>
		</form>

		<?php
	}
	?>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="../other/js/script.js"></script>
</body>

</html>