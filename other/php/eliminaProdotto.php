<?php
// Non vado ad eliminare il prodotto per davvero ma imposto il campo eliminato a 1
include_once('utils.php');
session_start();
if (isset($_POST['json_data'])) {
    $data = json_decode($_POST['json_data'], true);
    // Controllo il token
    if (isset($data['Token']) && $data['Token'] == $_SESSION['Token']) {
        $prodotto = $data['data'];
        // Inizializzo la connessione con il database
        $dbh = connect();
        $sql = "UPDATE `prodotti` SET `Eliminato`= 1, `Disponibile`=0 WHERE ID = ?";
        $id = $prodotto['id']; 
        $res = sqlUpdate($dbh, $sql, 'i', $id);
        if ($res) {
            echo json_encode('Delete riuscito');
        } else {
            echo json_encode('Delete fallito');
        }
        $dbh->close();
    } else {
        echo json_encode('Token non valido');
    }
} else {
    echo json_encode('POST fallito');
}
?>