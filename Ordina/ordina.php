<?php
include_once('../other/php/utils.php');
session_start();
if (isset($_POST['json_data'])) {
  $data = json_decode($_POST['json_data'], true);
  // Controllo il token
  if (isset($data['Token']) && $data['Token'] == $_SESSION['Token']) {
    $ordini = $data['data'];
    // Inizializzo la connessione con il database
    $dbh = connect();
    $sql = "INSERT INTO `ordini`(`ID`, `IDStudente`, `IDVenditore`, `IDProdotto`, `Quantita`, `Totale`, `Ora`) VALUES (null,?,?,?,?,?,?)";
    foreach ($ordini as $ordine) {
      $idprodotto = $ordine[0];
      $quantita = $ordine[1];
      $tot = $ordine[2];
      $idvenditore = $ordine[3];
      // Inserisco il record nel databse
      $res = sqlInsert($dbh, $sql, 'iiiidi', $_SESSION['userID'], $idvenditore, $idprodotto, $quantita, $tot, time());
    }
    $dbh->close();
  } else {
    echo json_encode('Token non valido');
  }
} else {
  echo json_encode('POST fallito');
}
?>