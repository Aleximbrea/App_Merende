<?php
require_once '../other/php/utils.php';
// Se il cookie contenente l'id della sessione è settato ripristino la sessione
if (isset($_COOKIE['sessionID'])) {
    session_id($_COOKIE['sessionID']);
    session_start();
    // Se è presente una sessione reindirizzo i vari utenti alle pagine dedicate
    if ($_SESSION['user'] == 0) {
        header('Location: ../Ordina');
    } else if ($_SESSION['user'] == 1) {
        header('Location: ../AreaVenditori');
    }
} else {
    session_start();
}
?>
<html lang="en">

<head>
    <meta name="csrf_token" content="<?php echo createToken(); ?>" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="../Signup/style.css">
    <script src="https://kit.fontawesome.com/48c9af8c84.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php
    echo '<form id="recuperaPasswordForm" action="POST" novalidate>
    <div class="input_block">
        <h1 style="margin: 0px;">';
    if (isset($_GET['id']) && $_GET['id'] !== '' && isset($_GET['hash']) && $_GET['hash'] !== '') {
        $dbh = connect();
        if ($dbh) {
            $res = sqlSelect($dbh, 'SELECT IDUtente,Hash,Orario FROM requests WHERE ID=? AND Tipo=1 AND Usertype=1', 'i', $_GET['id']);
            if ($res && $res->num_rows === 1) {
                $request = $res->fetch_assoc();
                // Se la richiesta ha più di un giorno è considerata invalida
                if ($request['Orario'] > time() - 60 * 60 * 24) {
                    if (password_verify(urlSafeDecode($_GET['hash']), $request['Hash'])) {
                        ?>
                        <form id="recuperaPasswordForm" action="POST" novalidate>
                            <h1>Inserisci la nuova password</h1>
                            <p id="messaggio">Password modificata con successo.</p>
                            <input type="hidden" name="userid" value="<?php echo $request['IDUtente'] ?>">
                            <input type="hidden" name="usertype" value="1">
                            <div class="input_blocks">
                                <div class="input_block">
                                    <input name='password' id="password" type="password" required autoComplete="off" />
                                    <span class="error_message" id="err_newpsswd"></span>
                                    <label for="password">Nuova Password</label>
                                    <i class="fa-solid fa-eye" id="eye" onclick="show_psswd()"></i>
                                </div>
                                <input type="submit" value="Cambia" id="verificamail_btn" onclick="cambiaPassword(event);">
                            </div>
                        </form>
                        <?php
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
        <form id="recuperaPasswordForm" action="POST" novalidate>
            <h1>Inserisci la tua email</h1>
            <p id="messaggio">Ti abbiamo mandato un email contenente il link cambiare password.</p>
            <div class="input_blocks">
                <input type="hidden" name="usertype" value="1">
                <div class="input_block">
                    <input name='email' id="email" Tipo="text" required autoComplete="off" />
                    <span class="error_message" id="err_valemail"></span>
                    <label for="email">Email</label>
                </div>
                <input type="submit" value="Verifica" id="verificamail_btn" onclick="sendPasswordResetRequest(event);">
            </div>
        </form>

        <?php
    }
    ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../other/js/script.js"></script>
</body>

</html>