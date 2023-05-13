<?php
session_start();
require_once('../other/php/utils.php');
$dbh = connect();
//A questa pagina ci si può accedere solo se si è loggati
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
    header('Location: ../LoginVenditori');
} else if (!isset($_SESSION['user']) || $_SESSION['user'] != 1) {
    header('Location: ../');
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta name="csrf_token" content="<?php echo createToken(); ?>" />
    <title>Lista ordini</title>
    <link rel="stylesheet" href="style.css">
    <script src="../other/js/functions.js"></script>
    <script src="https://kit.fontawesome.com/48c9af8c84.js" crossorigin="anonymous"></script>
</head>
<?php
if (isset($_GET['id'])) {
    $today = date('Y-m-d'); // data corrente
// Query per prendere l'orario entro cui si possono effettuare gli ordini
    $sql = "SELECT orari.ID, orari.IDGiorno, orari.Orario FROM `orari` WHERE orari.IDIstituto = ? AND orari.IDGiorno = ? ORDER BY orari.Orario ASC;";
    $res = sqlSelect($dbh, $sql, 'ii', $_GET['id'], getDayID($dbh));
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
}
// Nome del venditore loggato
$res = sqlSelect($dbh, 'SELECT Nome from venditori where ID = ?', 'i', $_SESSION['userID']);
$utente = $res->fetch_assoc();
?>
?>

<body>
    <div class="header">
        <center>
            <h1 id="titolo">Ordini delle Classi</h1>
        </center>
        <div class="navbar-icon" onclick="openNav()">
            <i class="fas fa-bars" style="font-size: 1.5em;"></i>
        </div>
    </div>
    <div id="mySidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <p>
            <?php echo $utente['Nome']?>
        </p>
        <a href="index.php" class="scelta">Visualizza ordini</a>
        <a href="GestisciProdotti.php" class="scelta">Gestione prodotti</a>
        <a href="#" onclick="logout()" id="esci">Esci</a>
        <!-- <a href="#" id='cambia'>Cambia classe</a> -->
    </div>
    <div id="background"></div>
    <div class="main">
        <div class="input_block">
            <select name="istituto" id="istituto" required>
                <option value="" selected disabled hidden>Seleziona Istituto</option>
                <?php
                $res = sqlSelect($dbh, "SELECT istituti.ID, istituti.NomeIstituto FROM istituti, rifornisce, venditori WHERE istituti.ID = rifornisce.IDIstituto AND rifornisce.IDVenditore = venditori.ID AND venditori.ID = ?;", 'i', $_SESSION['userID']);
                while ($row = $res->fetch_assoc()) {
                    $selected = isset($_GET['id']) && $_GET['id'] == $row['ID'] ? 'selected' : '';
                    echo "<option value=" . $row['ID'] . " " . $selected . ">" . $row['NomeIstituto'] . "</option>";
                }
                ?>
            </select>
            <script>
                // Quando si sceglie un istituto diverso nel select la pagina cambia il parametro id nell'url
                // in modo che il file php possa recuperare tale id e utilizzarlo per fare le query
                const istitutoSelect = document.querySelector('#istituto');
                istitutoSelect.addEventListener('change', () => {
                    const istitutoId = istitutoSelect.value;
                    window.location = '?id=' + istitutoId
                })
            </script>
        </div>
        <div class="card-gallery">
            <div class="cards-wrapper">
                <?php
                if (isset($_GET['id'])) {
                    // Creo una card per ogni classe della scuola selezionata che ha fatto almeno un ordine e trovo il totale degli ordini
                    $sql = "SELECT classi.ID, classi.Classe FROM classi, istituti WHERE classi.IDIstituto = istituti.ID AND istituti.ID = ?;";
                    $res = sqlSelect($dbh, $sql, 'i', $_GET['id']);
                    $classi = $res->fetch_all(MYSQLI_ASSOC);
                    $res->free();
                    foreach ($classi as $classe) {
                        ?>
                        <div class="card">
                            <div class="card-header">
                                <?php
                                // Totale ordini di tutti gli studneti della classe
                                $sql = "SELECT SUM(ordini.Totale) AS 'Totale' FROM prodotti, ordini, classi, studenti WHERE studenti.IDClasse = classi.ID AND ordini.IDStudente = studenti.ID AND ordini.IDVenditore = ? AND prodotti.ID = ordini.IDProdotto AND ordini.Ora > ? AND ordini.Ora < ? AND classi.ID = ?;";
                                $res = sqlSelect($dbh, $sql, 'iiii', $_SESSION['userID'], $past_deadline, $deadline, $classe['ID']);
                                $tot = $res->fetch_assoc();
                                $res->free();
                                ?>
                                <h3>
                                    <span>Classe
                                        <?php echo $classe['Classe'] ?>
                                    </span>
                                    <span style="margin-left:50%;">
                                        <?php echo $tot['Totale'] ?>
                                    </span>
                                    <span>€</span>
                                </h3>
                            </div>
                            <div class="card-body">
                                <?php
                                // Per ogni classe prendo gli studenti che hanno effettuato un acquisto presso il venditore loggato e l'istituto selezionato
                                $sql = "SELECT DISTINCT studenti.ID, studenti.Nome, studenti.Cognome FROM studenti, ordini, venditori, classi where studenti.ID = ordini.IDStudente AND studenti.IDClasse = classi.ID AND classi.ID = ? AND ordini.IDVenditore = venditori.ID AND studenti.IDIstituto = ? AND venditori.ID = ? AND ordini.Ora > ? AND ordini.Ora < ?";
                                $res = sqlSelect($dbh, $sql, 'iiiii', $classe['ID'], $_GET['id'], $_SESSION['userID'], $past_deadline, $deadline);
                                $studenti = $res->fetch_all(MYSQLI_ASSOC);
                                $res->free();
                                foreach ($studenti as $studente) {
                                    ?>
                                    <div class="person">
                                        <h2>
                                            <?php echo $studente['Nome'] . " " . $studente['Cognome'] ?>
                                        </h2>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Prodotto</th>
                                                    <th>Quantità</th>
                                                    <th>Prezzo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Query per recuperare tutti gli ordini di quello studente presso la scuola selezionata e il venditore loggato
                                                $sql = "SELECT prodotti.Nome, ordini.Quantita, ordini.Totale FROM prodotti, ordini, studenti, classi WHERE
                                                ordini.IDStudente = studenti.ID AND studenti.IDClasse = classi.ID AND classi.ID = ? AND ordini.IDStudente = ? AND ordini.IDVenditore = ? AND prodotti.ID = ordini.IDProdotto AND ordini.Ora > ? AND ordini.Ora < ?";
                                                $res = sqlSelect($dbh, $sql, 'iiiii', $classe['ID'], $studente['ID'], $_SESSION['userID'], $past_deadline, $deadline);
                                                $ordini = $res->fetch_all(MYSQLI_ASSOC);
                                                $res->free();
                                                foreach ($ordini as $ordine) {
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo $ordine['Nome'] ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $ordine['Quantita'] ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $ordine['Totale'] . " €" ?>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                <tr>
                                                    <?php
                                                    // Totale di tutti gli ordini di uno studente
                                                    $sql = "SELECT SUM(ordini.Totale) AS 'Totale_studente' FROM prodotti, ordini WHERE ordini.IDStudente = ? AND ordini.IDVenditore = ? AND prodotti.ID = ordini.IDProdotto AND ordini.Ora > ? AND ordini.Ora < ?;";
                                                    $res = sqlSelect($dbh, $sql, 'iiii', $studente['ID'], $_SESSION['userID'], $past_deadline, $deadline);
                                                    $totale_studente = $res->fetch_assoc();
                                                    $res->free();
                                                    ?>
                                                    <td colspan="2" class="text-right">Totale:</td>
                                                    <td><b>
                                                            <?php echo '€' . $totale_studente['Totale_studente'] ?>
                                                        </b></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="controls">
                        <button class="prev">&lt;</button>
                        <button class="next">&gt;</button>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</body>
<script>
    // Script per cambiare card
    const cardsWrapper = document.querySelector('.cards-wrapper');
    const prevButton = document.querySelector('.prev');
    const nextButton = document.querySelector('.next');

    const cardWidth = cardsWrapper.querySelector('.card').offsetWidth; // larghezza di una card
    const cardsWrapperWidth = cardsWrapper.offsetWidth; // larghezza del wrapper delle card
    const maxScroll = cardsWrapper.scrollWidth - cardsWrapperWidth; // la massima quantità di scorrimento

    prevButton.addEventListener('click', () => {
        cardsWrapper.scrollBy({
            left: -cardsWrapperWidth,
            behavior: 'smooth'
        });
    });

    nextButton.addEventListener('click', () => {
        cardsWrapper.scrollBy({
            left: cardsWrapperWidth,
            behavior: 'smooth'
        });
    });

    cardsWrapper.addEventListener('scroll', () => {
        if (cardsWrapper.scrollLeft === 0) {
            prevButton.disabled = true;
        } else if (cardsWrapper.scrollLeft >= maxScroll) {
            nextButton.disabled = true;
        } else {
            prevButton.disabled = false;
            nextButton.disabled = false;
        }
    });
</script>
<script src="../other/js/functions.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../other/js/script.js"></script>

</html>