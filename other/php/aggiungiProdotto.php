<?php
include_once('utils.php');
session_start();
if (isset($_POST['json_data'])) {
    $data = json_decode($_POST['json_data'], true);
    // Controllo il token
    if (isset($data['Token']) && $data['Token'] == $_SESSION['Token']) {
        $prodotto = $data['data'];
        // Inizializzo la connessione con il database
        $dbh = connect();
        $sql = "INSERT INTO `prodotti`(`ID`, `IDVenditore`, `Nome`, `Descrizione`, `Prezzo`, `Disponibile`) VALUES (null, ?, ?, ?, ?, ?)";
        $nome = $prodotto['nome'];
        $desc = $prodotto['desc'];
        $prezzo = $prodotto['prezzo'];
        $disponibilita = $prodotto['checkbox'];
        // Inserisco il record nel databse
        $res = sqlInsert($dbh, $sql, 'issdi', $_SESSION['userID'], $nome, $desc, $prezzo, $disponibilita);
        if ($res != -1) {
            echo json_encode('Insert riuscito');
        } else {
            echo json_encode('Insert fallito');
        }
        $dbh->close();
    } else {
        echo json_encode('Token non valido');
    }
} else {
    echo json_encode('POST fallito');
}
?>