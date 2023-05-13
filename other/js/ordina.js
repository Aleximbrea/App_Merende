// Questa funzione ricava dall'html i nomi dei vari prodotti e la quantità e il prezzo rispetto alla quantità di
// tutti i prodotti all'interno del file e poi li manda a un file php in formato json

// Aggiungo un evento che aspetta il submit del form
document.querySelector('#modulo').addEventListener('submit', function(e) {
    e.preventDefault(); // previene il comportamento predefinito di invio del modulo

    // Prendo i valori di tutti gli elementi che mi servono
    const prodotti = document.querySelectorAll('.prodotto');
    const quantita_prodotto = document.querySelectorAll('.num');
    const totale_prodotto = document.querySelectorAll('.semitot');

    // Ora riscrivo i dati in formato json
    const prodottiArray = [];

    for (let i = 0; i < prodotti.length; i++) {
    // Creo un oggetto json di un singolo prodotto per volta
    const prodotto = {
        nome: prodotti[i].textContent,
        quantita: quantita_prodotto[i].textContent,
        totale: totale_prodotto[i].textContent
    };
    // E poi lo aggiungo in un array che contiene tutti gli altri
    prodottiArray.push(prodotto);
    }

    // Infine utilizzo la funzione stringify per convertire l'array in una stringa json
    const prodottiJSON = JSON.stringify(prodottiArray);


    // invia il modulo
    this.submit();
  });