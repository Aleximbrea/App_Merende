<?php
// Se il cookie contenente l'id della sessione è settato ripristino la sessione
if(isset($_COOKIE['sessionID'])) {
  session_id($_COOKIE['sessionID']);
  session_start();
  // Se è presente una sessione reindirizzo i vari utenti alle pagine dedicate
  if($_SESSION['user'] == 0) {
    header('Location: ../Ordina');
  } else if($_SESSION['user'] == 1) {
    header('Location: ../AreaVenditori');
  }
} else {
  session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Home/style.css">
    <link rel="stylesheet" href="Home/mobile.css">
    <title>Home</title>
</head>
<body>
  <nav>
      <h1 id="title">Merende</h1>
      <ul>
        <li><a href="/Login/" class="btn">Login Studenti</a></li>
        <li><a href="/LoginVenditori" class="btn">Area Venditori</a></li>       
      </ul>
  </nav>  
  <div id="background"></div>
  <div id="testo">
    <h1 class="testo">Ordinare la merenda non è mai stato così facile!</h1>
    <h2 class="testo">Accedete all'app e scoprite tutte le prelibatezze che abbiamo da offrirvi.</h2>
    <p class="testo">Cliccate sul bottone qui sotto per iniziare il vostro ordine.</p>
    <button class="button2" onclick="window.location='../Signup';">Registrati per iniziare</button>
  </div>
</body>
</html>