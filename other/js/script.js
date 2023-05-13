function request(url, data, callback) {
  // Trasformo i dati in un oggetto formdata se già non lo sono
  var formdata = data
    ? data instanceof FormData
      ? data
      : new FormData(document.querySelector(data))
    : new FormData();
  // Aggiungo il token CSRF ai dati della richiesta
  var csrfMetaTag = document.querySelector('meta[name="csrf_token"]');
  formdata.append("Token", csrfMetaTag.getAttribute("content"));
  formdata.forEach(function (value, key) {
    console.log(key + ":", value);
  });

  // Richiesta AJAX
  $.ajax({
    type: "POST",
    url: url,
    data: formdata,
    processData: false,
    contentType: false,
    success: function (errors) {
      callback(errors);
    },
  });
}

function json_request(url, json_data, callback) {
  // Converto la stringa json in un array per inserire il token CSRF
  var data = JSON.parse(json_data);
  // Aggiungo il token CSRF ai dati della richiesta
  var csrfMetaTag = document.querySelector('meta[name="csrf_token"]');
  // Creo un nuovo oggetto e aggiungo il token e l'array di dati
  var tokendata = {
    Token: csrfMetaTag.getAttribute("content"),
    data: data,
  };

  // Riconverto l'array in una stringa json
  var json_data = JSON.stringify(tokendata);
  console.log(json_data);

  // Richiesta AJAX
  $.ajax({
    type: "POST",
    url: url,
    data: { json_data: json_data },
    dataType: "json",
    success: function (errors) {
      callback(errors);
    },
  });
}

function show_psswd() {
  const passwordInput = document.getElementById("password");
  const icon = document.getElementById("eye");

  if (passwordInput.type === "password") {
    passwordInput.type = "text";
    icon.className = "fa-solid fa-eye-slash";
  } else {
    passwordInput.type = "password";
    icon.className = "fa-solid fa-eye";
  }
}

// Funzione per far apparire il label sopra il select quando viene selezionato un valore

function show_label(selectid, labelid) {
  const mySelect = document.getElementById(selectid);
  if (this.value !== "") {
    document.getElementById(labelid).style.visibility = "visible";
  }
}

function signup(event) {
  event.preventDefault();
  request("../../Signup/signup.php", "#registerForm", function (errors) {
    console.log(errors);
    errors = JSON.parse(errors);
    //Qui gestisco gli errori ricevuti dalla Registrazione
    // Prima di tutto rimuovo tutti gli errori
    error_span = document.querySelectorAll(".error_message");
    error_span.forEach(function (element) {
      element.textContent = "";
    });
    // E poi procedo ad aggiungerli se ci sono
    for (var i = 0; i < errors.length; ++i) {
      switch (errors[i]) {
        case 0:
          // Se non ci sono stati errori e l'account è stato creato devo far confermare la mail all'utente
          window.location = "../../Signup/ConfermaEmail.php";
          document.getElementById("messaggio").style.visibility = "visible";
        case 1:
          document.getElementById("err_nome").textContent = "Nome non valido";
          break;
        case 2:
          document.getElementById("err_cogn").textContent =
            "Cogonome non valido";
          break;
        case 3:
          document.getElementById("err_istituto").textContent =
            "Compila questo campo";
          break;
        case 4:
          document.getElementById("err_email").textContent =
            "Compila questo campo";
          break;
        case 5:
          document.getElementById("err_classe").textContent =
            "Compila questo campo";
          break;
        case 6:
          document.getElementById("err_gender").textContent =
            "Compila questo campo";
          break;
        case 7:
          document.getElementById("err_password").textContent =
            "Minimo 8 caratteri";
          break;
        case 9:
          document.getElementById("err_email").textContent =
            "Non appartiene alla scuola selezionata";
          break;
        case 10:
          document.getElementById("err_email").textContent =
            "Email già registrata";
          break;
        case 11:
          console.log("Token non valido");
          break;
      }
    }
  });
}

function login(event) {
  event.preventDefault();
  request("../../Login/login.php", "#loginForm", function (errors) {
    errors = JSON.parse(errors);
    //Qui gestisco gli errori ricevuti dal Login
    // Prima di tutto rimuovo tutti gli errori
    error_span = document.querySelectorAll(".error_message");
    error_span.forEach(function (element) {
      element.textContent = "";
    });
    // E poi procedo ad aggiungerli se ci sono
    for (var i = 0; i < errors.length; ++i) {
      switch (errors[i]) {
        case 0:
          // Se non ci sono stati errori, l'utente può procedere all'ordine
          window.location = "../../Ordina/";
          break;
        case 1:
          document.getElementById("err_email").textContent =
            "Compila questo campo";
          break;
        case 2:
          document.getElementById("err_psswd").textContent =
            "Inserisci la password";
          break;
        case 3:
          document.getElementById("err_email").textContent =
            "Email non registrata";
          break;
        case 4:
          document.getElementById("err_psswd").textContent = "Password errata";
          break;
        case 5:
          document.getElementById("err_email").textContent =
            "Email non verificata";
          window.location = "../../Signup/ConfermaEmail.php";
          break;
        case 6:
          console.log("Token non valido");
      }
    }
  });
}

function loginVenditori(event) {
  event.preventDefault();
  request("../../LoginVenditori/login.php", "#loginForm", function (errors) {
    errors = JSON.parse(errors);
    //Qui gestisco gli errori ricevuti dal Login
    // Prima di tutto rimuovo tutti gli errori
    error_span = document.querySelectorAll(".error_message");
    error_span.forEach(function (element) {
      element.textContent = "";
    });
    // E poi procedo ad aggiungerli se ci sono
    for (var i = 0; i < errors.length; ++i) {
      switch (errors[i]) {
        case 0:
          // Se non ci sono stati errori, l'utente può procedere all'ordine
          window.location = "../../AreaVenditori/";
          break;
        case 1:
          document.getElementById("err_email").textContent =
            "Compila questo campo";
          break;
        case 2:
          document.getElementById("err_psswd").textContent =
            "Inserisci la password";
          break;
        case 3:
          document.getElementById("err_email").textContent =
            "Email non registrata";
          break;
        case 4:
          document.getElementById("err_psswd").textContent = "Password errata";
          break;
        case 5:
          document.getElementById("err_email").textContent =
            "Email non verificata";
          window.location = "../../Signup/ConfermaEmail.php";
          break;
        case 6:
          console.log("Token non valido");
      }
    }
  });
}

function logout() {
  request("../other/php/logout.php", null, function (errors) {
    window.location = "../../";
  });
}

function sendValidateEmailRequest(event) {
  event.preventDefault();
  request(
    "../other/php/sendEmailVerification.php",
    "#validateEmailForm",
    function (errors) {
      console.log(errors);
      errors = JSON.parse(errors);
      //Qui gestisco gli errori ricevuti dal Login
      // Prima di tutto rimuovo tutti gli errori
      document.getElementById("messaggio").style.visibility = "hidden";
      error_span = document.querySelector(".error_message");
      error_span.textContent = "";
      // E poi procedo ad aggiungerli se ci sono
      for (var i = 0; i < errors.length; ++i) {
        switch (errors[i]) {
          case 0:
            document.getElementById("messaggio").style.visibility = "visible";
            break;
          case 1:
            document.getElementById("err_valemail").textContent =
              "Errore, riprova più tardi";
            break;
          case 2:
            document.getElementById("err_valemail").textContent =
              "Errore, riprova più tardi";
            break;
          case 3:
            document.getElementById("err_valemail").textContent =
              "Hai superato le richieste giornaliere consentite";
            break;
          case 4:
            document.getElementById("err_valemail").textContent =
              "Email già verificata";
            break;
          case 5:
            document.getElementById("err_valemail").textContent =
              "Inserisci un indirizzo valido";
            break;
          case 6:
            document.getElementById("err_valemail").textContent =
              "Errore, riprova più tardi";
            break;
        }
      }
    }
  );
}

function sendPasswordResetRequest(event) {
  event.preventDefault();
  request(
    "../other/php/sendPasswordResetRequest.php",
    "#recuperaPasswordForm",
    function (errors) {
      console.log(errors);
      errors = JSON.parse(errors);
      // Prima di tutto rimuovo tutti gli errori
      document.getElementById("messaggio").style.visibility = "hidden";
      error_span = document.querySelector(".error_message");
      error_span.textContent = "";
      // E poi procedo ad aggiungerli se ci sono
      for (var i = 0; i < errors.length; ++i) {
        switch (errors[i]) {
          case 0:
            document.getElementById("messaggio").style.visibility = "visible";
            break;
          case 1:
            document.getElementById("err_valemail").textContent =
              "Errore, riprova più tardi";
            break;
          case 2:
            document.getElementById("err_valemail").textContent =
              "Errore, riprova più tardi";
            break;
          case 3:
            document.getElementById("err_valemail").textContent =
              "Hai superato le richieste giornaliere consentite";
            break;
          case 5:
            document.getElementById("err_valemail").textContent =
              "Inserisci un indirizzo valido";
            break;
          case 6:
            document.getElementById("err_valemail").textContent =
              "Errore, riprova più tardi";
            break;
        }
      }
    }
  );
}

function cambiaPassword(event) {
  event.preventDefault();
  request(
    "../other/php/cambiaPassword.php",
    "#recuperaPasswordForm",
    function (errors) {
      console.log(errors);
      errors = JSON.parse(errors);
      // Prima di tutto rimuovo tutti gli errori
      document.getElementById("messaggio").style.visibility = "hidden";
      error_span = document.querySelector(".error_message");
      error_span.textContent = "";
      // E poi procedo ad aggiungerli se ci sono
      for (var i = 0; i < errors.length; ++i) {
        switch (errors[i]) {
          case 0:
            document.getElementById("messaggio").style.visibility = "visible";
            window.location = "../../";
            break;
          case 1:
            document.getElementById("err_newpsswd").textContent =
              "La password deve avere almeno 8 caratteri";
            break;
          case 2:
            document.getElementById("err_newpsswd").textContent = "Errore";
            break;
        }
      }
    }
  );
}

function ordina(event) {
  event.preventDefault();
  // Per inserire l'ordine nel database prima devo prendere i venditori poi per ogni venditore prendo nome, quantita
  // e totale di ogni prodotto e se la quantità è maggiore di 0 li aggiungo in un array che verrà serializzato e passato
  // a un file php tramite ajax

  // Matrice che conterrà gli ordini
  var ordine = [];
  // Ora prendo i vari venditori
  venditori = document.querySelectorAll(".venditore");
  // Scorro ogni venditore
  for (var i = 0; i < venditori.length; i++) {
    // ID del venditore nel database
    var idvenditore = venditori[i].dataset.value;
    // Prendo i dati di quel venditore
    prodotti = venditori[i].querySelectorAll(".prodotto");
    quantita = venditori[i].querySelectorAll(".num");
    semitot = venditori[i].querySelectorAll(".semitot");
    for (var j = 0; j < prodotti.length; j++) {
      // Cerco i prodotti con quantità maggiore di 0 e li aggiungo in un array a cui aggiungo pure l'id del venditore
      if (parseInt(quantita[j].textContent) != 0) {
        ordine.push([
          parseInt(prodotti[j].dataset.value),
          parseInt(quantita[j].textContent),
          parseFloat(semitot[j].textContent),
          parseInt(idvenditore),
        ]);
        quantita[j].textContent = 0;
        semitot[j].textContent = 0;
      }
    }
  }
  document.querySelector("#totale").textContent = 0;
  // Trasformo in dati json
  var json_arr = JSON.stringify(ordine);
  json_request("../../Ordina/ordina.php", json_arr, function (errors) {});
  window.location = "../../Ordina";
}

function aggiungiProdotto(event) {
  event.preventDefault();
  var prodotto = {};
  // Card del nuovo prodotto
  var new_card = document.querySelector(".new-product-card");
  // Prendo nome, descrizione, prezzo e disponibilità del nuovo prodotto
  prodotto.nome = new_card.querySelector(".product-name").textContent;
  prodotto.desc = new_card.querySelector(".product-description").textContent;
  prodotto.prezzo = new_card.querySelector(".price-value").textContent;
  prodotto.checkbox = document.querySelector(".available").checked;

  // Trasformo in dati json
  var json_arr = JSON.stringify(prodotto);

  json_request(
    "../other/php/aggiungiProdotto.php",
    json_arr,
    function (errors) {
      if (errors == "Insert riuscito") {
        location.reload();
      }
    }
  );
}

function eliminaProdotto(event, id_prodotto) {
  // Per eliminare un prodotto dal database passo l'id del prodotto alla funzione js che tramite richiesta ajax lo mandera
  // a una funzione php
  event.preventDefault();
  array_id = {
    id: id_prodotto,
  };
  // Trasformo in dati json
  var json_arr = JSON.stringify(array_id);
  json_request(
    "../other/php/eliminaProdotto.php",
    json_arr,
    function (errors) {
      if (errors == "Delete riuscito") {
        location.reload();
      }
    }
  );
}

function modificaProdotto(event, id_prodotto) {
  event.preventDefault();
  var prodotto = {};
  var card = document.querySelector("#card" + id_prodotto);
  // Prendo nome, descrizione, prezzo e disponibilità modificati
  // Inserisco nell'array anche l'id del prodotto modificato
  prodotto.id = id_prodotto,
  prodotto.nome = card.querySelector(".product-name").textContent.trim().replace(/\s+/g, " ");
  prodotto.desc = card.querySelector(".product-description").textContent.trim().replace(/\s+/g, " ");
  prodotto.prezzo = card.querySelector(".price-value").textContent;
  prodotto.checkbox = card.querySelector(".available").checked;

  // Trasformo in dati json
  var json_arr = JSON.stringify(prodotto);
  console.log(json_arr);

  json_request(
    "../other/php/modificaProdotto.php",
    json_arr,
    function (errors) {
      if (errors == "Update riuscito") {
        location.reload();
      }
    }
  );
}
