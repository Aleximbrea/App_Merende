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
        $sql = "UPDATE `prodotti` SET `Nome`= ?,`Descrizione`= ? ,`Prezzo`= ? ,`Disponibile`= ? WHERE  ID = ?";
        $id = $prodotto['id'];
        $nome = $prodotto['nome'];
        $desc = $prodotto['desc'];
        $prezzo = $prodotto['prezzo'];
        $disponibilita = $prodotto['checkbox'];
        // Inserisco il record nel databse
        $res = sqlUpdate($dbh, $sql, 'ssdii', $nome, $desc, $prezzo, $disponibilita, $id);
        if ($res) {
            echo json_encode('Update riuscito');
        } else {
            echo json_encode('Update fallito');
        }
        $dbh->close();
    } else {
        echo json_encode('Token non valido');
    }
} else {
    echo json_encode('POST fallito');
}
?>