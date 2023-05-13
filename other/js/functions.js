function toggleCard(cardID) {
  var card = document.getElementById(cardID);
  card.classList.toggle("open");
}

function plus(numberID, prezzoID, totaleID) {
  // Il div del numero che indica la quantità
  var number_div = document.getElementById(numberID);
  // Prendo il prezzo del singolo prodotto
  var prezzo = parseFloat(document.getElementById(prezzoID).textContent);
  // Lo span che contiene il costo basato sulla quantità
  var tot_span = document.getElementById(totaleID);
  // Lo span del totale del totale
  var totale_span = document.getElementById("totale");
  // Quantità del prodotto
  var number = parseInt(number_div.textContent);
  // Aumento la quantità di uno
  number = number + 1;
  // Modifico l'html
  number_div.textContent = number;
  // Cambio anche il totale
  tot_span.textContent =
    parseFloat(document.getElementById(totaleID).textContent) + prezzo;
  totale_span.textContent =
    parseFloat(document.getElementById("totale").textContent) + prezzo;
}

function minus(numberID, prezzoID, totaleID) {
  // Il div del numero che indica la quantità
  var number_div = document.getElementById(numberID);
  // Prendo il prezzo del singolo prodotto
  var prezzo = parseFloat(document.getElementById(prezzoID).textContent);
  // Lo span che contiene il costo basato sulla quantità
  var tot_span = document.getElementById(totaleID);
  // Lo span del totale del totale
  var totale_span = document.getElementById("totale");
  // Quantità del prodotto
  var number = parseInt(number_div.textContent);
  if (number > 0) {
    // Diminuisco la quantità di uno
    number = number - 1;
    // Modifico l'html
    number_div.textContent = number;
    // Cambio anche il totale
    tot_span.textContent =
      parseFloat(document.getElementById(totaleID).textContent) - prezzo;
    totale_span.textContent =
      parseFloat(document.getElementById("totale").textContent) - prezzo;
  }
}

// Funzione per aprire la navbar
function openNav() {
  document.getElementById("mySidenav").style.width = "250px";
}

// Funzione per chiudere la navbar
function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
}
