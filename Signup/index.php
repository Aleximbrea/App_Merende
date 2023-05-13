<!DOCTYPE html>
<html>
<?php require_once '../other/php/utils.php' ?>
<head>
    <meta name="csrf_token" content="<?php echo createToken(); ?>" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1" />
    <title>Registrazione</title>
    <script src="https://kit.fontawesome.com/48c9af8c84.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="mobile.css">
</head>
<?php
include_once('../other/php/utils.php');
?>

<body>
    <form id="registerForm" method="post" action="signup.php" novalidate>
        <h1>Registrazione</h1>
        <div class="input_blocks" id="nome_cogn">
            <div class="input_block">
                <input name='first_name' id="first_name" type="text" required autoComplete="off" />
                <label for="first_name">Nome</label>
                <span class="error_message" id="err_nome"></span>
            </div>
            <div class="space"></div>
            <div class="input_block">
                <input name='last_name' id="last_name" type="text" required autoComplete="off" />
                <span class="error_message" id="err_cogn"></span>
                <label for="last_name">Cognome</label>
            </div>
        </div>
        <div class="input_block">
            <select name="istituto" id="istituto" onchange="show_label('istituto', 'labelistituto')" required>
                <script>
                    document.getElementById("istituto").addEventListener("change", function () {
                        var istituto = $(this).val();
                        // Effettua una richiesta AJAX a una pagina php per recuperare le opzioni per "classe" in base alla scuola selezionata
                        $.ajax({
                            url: '../other/php/showclassi.php', // Il percorso del file PHP che gestirà la richiesta AJAX
                            data: { istituto: istituto }, // Passiamo al file php l'istituto selezionato
                            type: 'POST', // Utilizza il metodo POST per inviare la richiesta
                            success: function (data) {
                                // Se la richiesta ha avuto successo, aggiorna le opzioni per il select "classe" con i dati restituiti dal server
                                $('#classe').html(data);
                                document.getElementById('labelclasse').style.visibility = 'hidden';
                            }
                        });
                    });
                </script>
                <option value="" selected disabled hidden>Seleziona Istituto</option>
                <?php
                $dbh = connect();
                $res = sqlSelect($dbh, "SELECT * FROM istituti;");
                while ($row = $res->fetch_assoc()) {
                    echo "<option value=" . $row['ID'] . ">" . $row['NomeIstituto'] . "</option>";
                }
                $dbh->close();
                ?>
            </select>
            <label for="istituto" class="hiddenlabel" id="labelistituto">Istituto</label>
            <span class="error_message" id="err_istituto"></span>
        </div>
        <div class="input_blocks" id="blocco_classe">
            <div class="input_block">
                <select name="classe" id="classe" onchange="show_label('classe', 'labelclasse')" required>
                    <option value="" selected disabled hidden>Classe</option>
                </select>
                <label for="classe" class="hiddenlabel" id="labelclasse">Classe</label>
                <span class="error_message" id="err_classe"></span>
            </div>
            <div class="space"></div>
            <div class="input_block">
                <select name="gender" id="gender" onchange="show_label('gender', 'labelgender')" required>
                    <option value="" selected disabled hidden>Sesso</option>
                    <option value="m">Maschio</option>
                    <option value="f">Femmina</option>
                </select>
                <label for="gender" class="hiddenlabel" id="labelgender">Sesso</label>
                <span class="error_message" id="err_gender"></span>
            </div>
        </div>
        <div class="input_block">
            <input name='email' id="email" type="text" required autoComplete="off" />
            <span class="error_message" id="err_email"></span>
            <label for="email">Email</label>
        </div>
        <div class="input_block password-container">
            <input name='password' id="password" type="password" required autoComplete="off" />
            <span class="error_message" id="err_password"></span>
            <label for="password">Password</label>
            <i class="fa-solid fa-eye" id="eye" onclick="show_psswd()"></i>
        </div>
        <input type="submit" value="Registrati" onclick="signup(event);">
        <p>Hai già un account? <a href="../Login/" class="btn">Accedi</a></p>
    </form>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../other/js/script.js"></script>
</body>

</html>