<?php
session_start();
session_destroy();
// Per eliminare il cookie imposto la data di scadenza a una data passata
setcookie('sessionID', null, [
    'expires' => time() - (90 * 24 * 60 * 60),
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'sameSite' => 'Lax' // Prevenzione attachi CSRF 
]);
?>