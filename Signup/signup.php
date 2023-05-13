<?php
require_once("../other/php/utils.php");
$dbh = connect();
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}
$errors = [];
// Controllo che il nome sia stato inserito e che non superi i 50 caratteri e che utilizzi solo coratteri dalla a alla z e spazi
if (!isset($_POST['first_name']) || strlen($_POST['first_name']) > 50 || !preg_match('/^[a-zA-Z- ]+$/', $_POST['first_name'])) {
  $errors[] = 1;
}
// Faccio la stessa cosa con i cognomi
if (!isset($_POST['last_name']) || strlen($_POST['last_name']) > 50 || !preg_match('/^[a-zA-Z- ]+$/', $_POST['last_name'])) {
  $errors[] = 2;
}
if (!isset($_POST['istituto'])) {
  $errors[] = 3;
}
if (empty($_POST['email']) || strlen($_POST['email']) > 150) {
  $errors[] = 4;
}
if (!isset($_POST['classe'])) {
  $errors[] = 5;
}
if (!isset($_POST['gender'])) {
  $errors[] = 6;
}
if (!isset($_POST['password']) || strlen($_POST['password']) < 8) {
  // La password deve essere di almeno 8 caratteri
  $errors[] = 7;
}

if (count($errors) == 0) {
  // Verifico il Token csrf
  if (isset($_POST['Token']) && $_POST['Token'] == $_SESSION['Token']) {
    //Verifico che l'email non sia già presente nel database
    $res = sqlSelect($dbh, 'SELECT * FROM studenti WHERE email = ?', 's', $_POST['email']);
    if ($res->num_rows == 0) {
      //L'email non è gia registrata quindi verifico che l'email inserita abbia il dominio della scuola che è stata inserita
      //Trovo il dominio dell'email
      $email_parts = explode("@", $_POST['email']);
      $dominio = end($email_parts);
      $res = sqlSelect($dbh, 'SELECT * from istituti WHERE ID = ?', 's', $_POST['istituto']);
      // Se il dominio della scuola selezionata è diverso da quello della mail inserita il codice restituirà un codice di errore
      $row = $res->fetch_assoc();
      if ($row['Dominio'] == $dominio) {
        // L'email inserita corrisponde a quella della scuola selezionata quindi procedo con la registrazione
        // Codifico la password utilizzando l algoritmo bcrypt
        $hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $sql = "INSERT INTO `studenti`(`ID`, `Nome`, `Cognome`, `IDIstituto`, `IDClasse`, `Sesso`, `Email`, `EmailVerificata`, `Password`) VALUES (NULL, ?, ?, ?, ?, ?, ?, 0, ?)";
        $id = sqlInsert($dbh, $sql, 'sssssss', $_POST['first_name'], $_POST['last_name'], $_POST['istituto'], $_POST['classe'], $_POST['gender'], $_POST['email'], $hash);
        if ($id != -1) {
          // Insert riuscito
          $errors[] = 0;
        } else {
          // Insert fallito
          $errors[] = 8;
        }
      } else {
        // Il dominio della mail non corrisponde alla scuola selezionata
        $errors[] = 9;
      }
    } else {
      // L'email è gia presente nel databse allora controllo se l'email è verificata
      $studente = $res->fetch_assoc();
      $studenteid = $studente['ID'];
      if ($studente['EmailVerificata'] == 0) {
        // Se l 'email non è verificata allora procedo a sovrascrivere i dati
        // Questo è lo stesso codice usato in caso la mail non è presente nel db
        //Trovo il dominio dell'email
        $email_parts = explode("@", $_POST['email']);
        $dominio = end($email_parts);
        $res = sqlSelect($dbh, 'SELECT * from istituti WHERE ID = ?', 's', $_POST['istituto']);
        // Se il dominio della scuola selezionata è diverso da quello della mail inserita il codice restituirà un codice di errore
        $row = $res->fetch_assoc();
        if ($row['Dominio'] == $dominio) {
          // L'email inserita corrisponde a quella della scuola selezionata quindi procedo con la registrazione
          // Codifico la password utilizzando l algoritmo bcrypt
          $hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
          $sql = "UPDATE `studenti` SET `ID`=?,`Nome`=?,`Cognome`=?,`IDIstituto`=?,`IDClasse`=?,`Sesso`=?,`Email`=?,`EmailVerificata`=?,`Password`=? WHERE ID = ?;";
          $id = sqlUpdate($dbh, $sql, 'issiissisi', $studenteid, $_POST['first_name'], $_POST['last_name'], $_POST['istituto'], $_POST['classe'], $_POST['gender'], $_POST['email'], 0, $hash, $studenteid);
          if ($id) {
            // Update riuscito
            $errors[] = 0;
          } else {
            // Update fallito
            $errors[] = 8;
          }
        } else {
          // Il dominio della mail non corrisponde alla scuola selezionata
          $errors[] = 9;
        }
      } else {
        //Account già esistente
        $errors[] = 10;
      }
    }
  } else {
    $errors[] = 11;
  }
}
echo json_encode($errors);
$dbh->close();