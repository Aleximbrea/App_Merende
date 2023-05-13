<?php 
    include_once('utils.php');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Controlla se il valore del parametro "istituto" è stato passato nella richiesta
        if (isset($_POST['istituto'])) {
            $dbh = connect();
            // Effettua una query per recuperare le opzioni per "classe" in base alla scuola selezionata
            $res = sqlSelect($dbh, "SELECT * FROM classi WHERE IDIstituto = ?", "s", $_POST['istituto']);
            echo '<option value="" selected disabled hidden>Classe</option>';
            while ($row = $res->fetch_assoc()) {
                echo '<option value="' . $row['ID'] . '">' . $row['Classe'] . '</option>';
            }        
            $dbh->close();        
            }
        }
?>