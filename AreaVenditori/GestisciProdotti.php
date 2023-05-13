<!DOCTYPE html>
<html lang="en">
<?php
session_start();
require_once('../other/php/utils.php');
$dbh = connect();
//A questa pagina ci si può accedere solo se si è loggati
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
    header('Location: ../LoginVenditori');
} else if (!isset($_SESSION['user']) || $_SESSION['user'] != 1) {
    header('Location: ../');
}
// Nome del venditore loggato
$res = sqlSelect($dbh, 'SELECT Nome from venditori where ID = ?', 'i', $_SESSION['userID']);
$utente = $res->fetch_assoc();
$nuovo_prodotto = '<div class="card-prodotto" id="nuovo">
<div class="add-product-card">
    <h2>Aggiungi prodotto +</h2>
</div>
<div class="new-product-card" style="display: none;">
    <div class="editable-field">
        <h2 class="product-name" contenteditable="true">Nome</h2>
    </div>
    <div class="editable-field">
        <p class="product-description" contenteditable="true">Descrizione</p>
    </div>
    <div class="editable-field">
        <p class="product-price">Prezzo: <span class="price-value" contenteditable="true"></span> €
        </p>
    </div>
    <div class="availability">
        <p style="font-size: 20px">Disponibile <span><input type="checkbox" name="available" class="available"></span></p>
    </div>
    <div class="card-actions">
        <button class="save-btn" onclick="aggiungiProdotto(event)">Aggiungi</button>
    </div>
</div>
</div>';
?>

<head>
    <meta name="csrf_token" content="<?php echo createToken(); ?>" />
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="../other/js/functions.js"></script>
    <script src="../other/js/script.js"></script>
    <title>Gestione prodotti</title>
</head>

<body>
    <div class="header">
        <center>
            <h1 id="titolo">Gestione prodotti</h1>
        </center>
        <div class="navbar-icon" onclick="openNav()">
            <i class="fas fa-bars" style="font-size: 1.5em;"></i>
        </div>
    </div>
    <div id="mySidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <p>
            <?php echo $utente['Nome'] ?>
        </p>
        <a href="index.php" class="scelta">Visualizza ordini</a>
        <a href="GestisciProdotti.php" class="scelta">Gestione prodotti</a>
        <a href="#" onclick="logout()" id="esci">Esci</a>
        <!-- <a href="#" id='cambia'>Cambia classe</a> -->
    </div>
    <div id="background"></div>
    <div class="main">
        <p style="margin-bottom: 10px;">In questa pagina è possibile inserire e rimuovere prodotti</p>
        <form action="GestisciProdotti.php" method="GET">
            <div class="search-bar">
                <input name="prodotto" type="search" placeholder="Cerca prodotto...">
            </div>
        </form>
        <?php echo $nuovo_prodotto;
        if (isset($_GET['prodotto']) && $_GET['prodotto'] != "") { ?>
            <?php
        $sql = "SELECT prodotti.Nome, prodotti.ID, prodotti.Prezzo, prodotti.Descrizione, prodotti.Disponibile from prodotti WHERE prodotti.IDVenditore = ? AND prodotti.Eliminato = 0 AND prodotti.Nome LIKE ?";
        $res = sqlSelect($dbh, $sql, 'is', $_SESSION['userID'], '%' . $_GET['prodotto'] . '%');
        $prodotti = $res->fetch_all(MYSQLI_ASSOC);
        $res->free();
        foreach ($prodotti as $prodotto) {
            ?>
            <div class="card-prodotto" id="card<?php echo $prodotto['ID'] ?>">
                <div class="editable-field">
                    <h2 class="product-name" contenteditable="true">
                        <?php echo $prodotto['Nome'] ?>
                    </h2>
                </div>
                <div class="editable-field">
                    <p class="product-description" contenteditable="true">
                        <?php echo $prodotto['Descrizione'] ?>
                    </p>
                </div>
                <div class="editable-field">
                    <p class="product-price">Prezzo: <span class="price-value" contenteditable="true">
                            <?php echo $prodotto['Prezzo'] ?>
                        </span> €
                    </p>
                </div>
                <div class="availability">
                    <p style="font-size: 20px">Disponibile <span><input type="checkbox" name="available" class="available"
                                <?php if ($prodotto['Disponibile'] == 1) {
                                    echo 'checked';
                                } ?>></span></p>
                </div>
                <div class="card-actions">
                    <button class="save-btn" onclick="modificaProdotto(event, <?php echo $prodotto['ID'] ?>)">Salva</button>
                    <button class="delete-btn"
                        onclick="eliminaProdotto(event, <?php echo $prodotto['ID'] ?>)">Elimina</button>
                </div>
            </div>
        <?php } ?>

    </div>
        <?php } else { ?>
            <?php
            $sql = "SELECT prodotti.Nome, prodotti.ID, prodotti.Prezzo, prodotti.Descrizione, prodotti.Disponibile from prodotti WHERE prodotti.IDVenditore = ? AND prodotti.Eliminato = 0";
            $res = sqlSelect($dbh, $sql, 'i', $_SESSION['userID']);
            $prodotti = $res->fetch_all(MYSQLI_ASSOC);
            $res->free();
            foreach ($prodotti as $prodotto) {
                ?>
                <div class="card-prodotto" id="card<?php echo $prodotto['ID'] ?>">
                    <div class="editable-field">
                        <h2 class="product-name" contenteditable="true">
                            <?php echo $prodotto['Nome'] ?>
                        </h2>
                    </div>
                    <div class="editable-field">
                        <p class="product-description" contenteditable="true">
                            <?php echo $prodotto['Descrizione'] ?>
                        </p>
                    </div>
                    <div class="editable-field">
                        <p class="product-price">Prezzo: <span class="price-value" contenteditable="true">
                                <?php echo $prodotto['Prezzo'] ?>
                            </span> €
                        </p>
                    </div>
                    <div class="availability">
                        <p style="font-size: 20px">Disponibile <span><input type="checkbox" name="available" class="available"
                                    <?php if ($prodotto['Disponibile'] == 1) {
                                        echo 'checked';
                                    } ?>></span></p>
                    </div>
                    <div class="card-actions">
                        <button class="save-btn" onclick="modificaProdotto(event, <?php echo $prodotto['ID'] ?>)">Salva</button>
                        <button class="delete-btn"
                            onclick="eliminaProdotto(event, <?php echo $prodotto['ID'] ?>)">Elimina</button>
                    </div>
                </div>
            <?php } ?>

        </div>
    <?php }
        ?>

</body>
<script src="https://kit.fontawesome.com/48c9af8c84.js" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const addProductCard = document.querySelector('.add-product-card');
    const newProductCard = document.querySelector('.new-product-card');

    addProductCard.addEventListener('click', () => {
        addProductCard.style.display = 'none';
        newProductCard.style.display = 'block';
    });

</script>

</html>