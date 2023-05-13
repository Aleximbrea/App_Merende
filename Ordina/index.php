<?php
require_once('../other/php/utils.php');
session_start();
//A questa pagina ci si può accedere solo se si è loggati
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
  header('Location: ../Login');
} else if (!isset($_SESSION['user']) || $_SESSION['user'] != 0) {
  header('Location: ../');
}
?>
<!DOCTYPE html>
<html>

<head>
  <meta name="csrf_token" content="<?php echo createToken(); ?>" />
  <meta charset="UTF-8">
  <title>Ordina</title>
  <link rel="stylesheet" href="style.css">
  <script src="../other/js/functions.js"></script>
  <script src="https://kit.fontawesome.com/48c9af8c84.js" crossorigin="anonymous"></script>
</head>
<?php
$dbh = connect();
// Devo prendere tutti i venditori che riforniscono l'istituto dell'utente
$sql = "SELECT venditori.Nome, venditori.ID FROM venditori, rifornisce WHERE venditori.ID = rifornisce.IDVenditore and rifornisce.IDIstituto = (SELECT studenti.IDIstituto FROM studenti WHERE studenti.ID = ?);";
$res = sqlSelect($dbh, $sql, 'i', $_SESSION['userID']);
$venditori = $res->fetch_all(MYSQLI_ASSOC);
$res->free();
// Nome e cognome dell'utente loggato
$res = sqlSelect($dbh, 'SELECT Nome, Cognome from studenti where ID = ?', 'i', $_SESSION['userID']);
$utente = $res->fetch_assoc();
?>

<body>
  <div class="header">
    <center>
      <h1 id="titolo">Menù della casa</h1>
    </center>
    <div class="navbar-icon" onclick="openNav()">
      <i class="fas fa-bars"></i>
    </div>
  </div>
  <div id="mySidenav" class="sidenav">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
    <p>
      <?php echo $utente['Nome'] . ' ' . $utente['Cognome'] ?>
    </p>
    <a href="index.php" class='scelta'>Ordina</a>
    <a href="IMieiOrdini.php" class='scelta'>I Miei Ordini</a>
    <a href="#" onclick="logout()" id="esci">Esci</a>
  </div>
  <div id="background"></div>
  <div class="tab">
    <button class="tablinks active" onclick="openTab(event, 'Tab1')">Ordina</button>
    <button class="tablinks" onclick="openTab(event, 'Tab2')">Lista ordini</button>
  </div>
  <?php
  $today = date('Y-m-d'); // data corrente
  // Query per prendere l'orario entro cui si possono effettuare gli ordini
  $sql = "SELECT orari.ID, orari.IDGiorno, orari.Orario FROM `orari`, istituti, studenti WHERE orari.IDIstituto = istituti.ID AND studenti.IDIstituto = istituti.ID AND studenti.ID = ? AND orari.IDGiorno = ? ORDER BY orari.Orario ASC;";
  $res = sqlSelect($dbh, $sql, 'ii', $_SESSION['userID'], getDayID($dbh));
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
  ?>
  <div id="Tab1" class="tabcontent">
    <?php
    if ($deadline != null && time() > $past_deadline) {
      ?>
      <form action="#" method="post" id="modulo">
        <h2 style="padding: 10px;">É possibile ordinare fino alle
          <?php echo date('H:i:s', $deadline); ?>
        </h2>
        <?php
        foreach ($venditori as $venditore) {
          $idvenditore = $venditore['ID'];
          ?>
          <div class="card venditore" data-value="<?php echo $idvenditore ?>" id="card<?php echo $idvenditore ?>" ?>
            <div class="card-header" onclick="toggleCard('card<?php echo $idvenditore ?>')">
              <h3>
                <?php echo $venditore['Nome'] ?>
              </h3>
              <span class="card-toggle">+</span>
            </div>
            <div class="card-content">
              <div class="card-list">
                <?php
                $sql = "SELECT prodotti.Nome, prodotti.ID, prodotti.Prezzo, prodotti.Descrizione from prodotti WHERE prodotti.IDVenditore = ? AND prodotti.Disponibile = 1 AND prodotti.Eliminato = 0;";
                $res = sqlSelect($dbh, $sql, 'i', $venditore['ID']);
                $prodotti = $res->fetch_all(MYSQLI_ASSOC);
                $res->free();
                foreach ($prodotti as $prodotto) {
                  ?>
                  <div class="card">
                    <div class="subcard">
                      <div class="descrizione">
                        <h2 class="prodotto" data-value="<?php echo $prodotto['ID'] ?>"><?php echo $prodotto['Nome'] ?></h2>
                        <p>
                          <?php echo $prodotto['Descrizione'] ?>
                        </p>
                        <div class="prezzo">
                          <span>Prezzo: </span>
                          <span id="prezzo<?php echo $prodotto['ID'] ?>"><?php echo $prodotto['Prezzo'] ?></span>
                          <span>€</span>
                        </div>
                      </div>
                      <div class="total">
                        <div class="quantity">
                          <span class="meno"><i class="fa-solid fa-circle-minus" style="color: rgba(207, 8, 8, 0.888);"
                              onclick="minus('num<?php echo $prodotto['ID'] ?>', 'prezzo<?php echo $prodotto['ID'] ?>', 'tot<?php echo $prodotto['ID'] ?>')"></i></span>
                          <span class="num" id="num<?php echo $prodotto['ID'] ?>">0</span>
                          <span class="piu"><i class="fa-solid fa-circle-plus" style="color: green;"
                              onclick="plus('num<?php echo $prodotto['ID'] ?>', 'prezzo<?php echo $prodotto['ID'] ?>', 'tot<?php echo $prodotto['ID'] ?>')"></i></span>
                        </div>
                        <div>
                          <span>Totale: </span>
                          <span class="semitot" id="tot<?php echo $prodotto['ID'] ?>">0</span>
                          <span>€</span>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
        <?php } ?>
        <div class="completa_ordine">
          <div>
            <span>Totale: </span>
            <span id="totale">0</span>
            <span>€</span>
          </div>
          <button type="submit" onclick="ordina(event)">Ordina</button>
        </div>
      </form>
      <?php
    } else {
      ?>
      <h1>Non è possibile ordinare in questo momento</h1>
      <?php
    }
    ?>
  </div>
  <div id="Tab2" class="tabcontent">
    <?php
    //Classe dell'utente loggato
    $sql = "SELECT classi.ID, classi.Classe, classi.IDIstituto FROM studenti, classi WHERE classi.ID = studenti.IDClasse AND studenti.ID=?";
    $res = sqlSelect($dbh, $sql, 'i', $_SESSION['userID']);
    $classe_studente = $res->fetch_assoc();
    $res->free();
    // Ora prendo gli id e i nomi di tutti i venditori della scuola frequentata dall'utente
    $sql = 'SELECT venditori.Nome, venditori.ID FROM venditori, rifornisce WHERE rifornisce.IDVenditore = venditori.ID AND rifornisce.IDIstituto = ?;';
    $res = sqlSelect($dbh, $sql, 'i', $classe_studente['IDIstituto']);
    $venditori_classe = $res->fetch_all(MYSQLI_ASSOC);
    $res->free();
    ?>
    <h1 style="margin-bottom: 30px;">Lista delle merende della classe
      <?php echo $classe_studente['Classe'] ?>
    </h1>
    <?php
    $temp = 0;
    foreach ($venditori_classe as $venditore_classe) {
      ?>
      <h1 style="margin-bottom: 20px;">
        <span>
          <?php echo $venditore_classe['Nome'] ?>
        </span>
        <span style="margin-left: 20px;" class="totale_venditore">
          <?php

          $sql = 'SELECT SUM(ordini.Totale) AS tot from ordini, studenti where ordini.IDVenditore = ? AND ordini.IDStudente = studenti.ID and studenti.IDClasse = ? AND ordini.Ora > ? AND ordini.Ora < ?;';
          $res = sqlSelect($dbh, $sql, 'iiii', $venditore_classe['ID'], $classe_studente['ID'], $past_deadline, $deadline);
          $totale_venditore = $res->fetch_assoc();
          $res->free();
          if ($totale_venditore) {
            echo $totale_venditore['tot'];
          } else {
            echo 0;
          }
          ?>
        </span>
        <span>€</span>
      </h1>
      <?php
      // Trovo i nomi delle persone che hanno fatto un ordine in quella classe presso quella lista
      $sql = 'SELECT DISTINCT s.Nome, s.Cognome, s.ID FROM studenti s INNER JOIN ordini o ON s.ID = o.IDStudente INNER JOIN venditori v ON o.IDVenditore = v.ID WHERE s.IDClasse = ? AND v.ID = ? AND o.Ora > ? AND o.Ora < ?;';
      $res = sqlSelect($dbh, $sql, 'iiii', $classe_studente['ID'], $venditore_classe['ID'], $past_deadline, $deadline);
      $studenti = $res->fetch_all(MYSQLI_ASSOC);
      $res->free();
      ?>
      <div class="person_container">
        <?php
        foreach ($studenti as $studente) {
          if ($studente['Nome'] != "") {
            // Ora per ogni persona che ha fatto un ordine trovo quanti prodotti ha ordinato da quel venditore
            $sql = "SELECT prodotti.Nome, ordini.Quantita, ordini.Totale FROM prodotti, ordini, studenti WHERE ordini.IDProdotto = prodotti.ID AND ordini.IDStudente = studenti.ID AND studenti.ID = ? AND ordini.IDVenditore = ? AND ordini.Ora > ? AND ordini.Ora < ?;";
            $res = sqlSelect($dbh, $sql, 'iiii', $studente['ID'], $venditore_classe['ID'], $past_deadline, $deadline);
            $ordini = $res->fetch_all(MYSQLI_ASSOC);
            $res->free();
            ?>
            <div class="person"">
        <div class=" persona">
              <h2>
                <?php echo $studente['Nome'] . ' ' . $studente['Cognome'] ?>
              </h2>
              <div class="totale">
                <span class="totale_persona">
                  <?php
                  $sql = 'SELECT SUM(ordini.Totale) AS tot from ordini, studenti where ordini.IDVenditore = ? AND ordini.IDStudente = studenti.ID and studenti.IDClasse = ? AND studenti.ID = ? AND ordini.Ora > ? AND ordini.Ora < ?;';
                  $res = sqlSelect($dbh, $sql, 'iiiii', $venditore_classe['ID'], $classe_studente['ID'], $studente['ID'], $past_deadline, $deadline);
                  $totale_venditore = $res->fetch_assoc();
                  $res->free();
                  if ($totale_venditore) {
                    echo $totale_venditore['tot'];
                  } else {
                    echo 0;
                  }
                  ?>
                </span>
                <span>€</span>
              </div>
            </div>
            <table>
              <thead>
                <tr>
                  <th>Prodotto</th>
                  <th>Quantità</th>
                  <th>Totale</th>
                </tr>
              </thead>
              <tbody>
                <?php
                foreach ($ordini as $ordine) {
                  ?>
                  <tr>
                    <td class="<?php echo $ordine['Nome'] ?>"><?php echo $ordine['Nome'] ?></td>
                    <td>
                      <?php echo $ordine['Quantita'] ?>
                    </td>
                    <td>
                      <span class="semitot_tab2">
                        <?php echo $ordine['Totale'] ?>
                      </span>
                      <span>€</span>
                    </td>
                  </tr>
                  <?php
                }
                ?>
              </tbody>
            </table>
          </div>
          <?php $temp = $temp + 1;
          }
        }
        ?>
    </div>
    <?php
    } ?>
  </div>

  <script>
    // Funzione per aprire il tab selezionato
    function openTab(evt, tabName) {
      var i, tabcontent, tablinks;
      tabcontent = document.getElementsByClassName("tabcontent");
      for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
      }
      tablinks = document.getElementsByClassName("tablinks");
      for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
      }
      document.getElementById(tabName).style.display = "block";
      evt.currentTarget.className += " active";
    }
    // Apri il primo tab per default
    document.getElementById("Tab1").style.display = "block";
  </script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="../other/js/script.js"></script>
</body>
<?php $dbh->close() ?>

</html>