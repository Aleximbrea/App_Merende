<?php
function crypt_password($password) {
    // Genera un salt
    $salt = '$2y$10$'.base64_encode(mcrypt_create_iv(22, MCRYPT_DEV_URANDOM)).'$';
    
    // Cripta la password utilizzando il salt generato
    $hashed_password = crypt($password, $salt);
    
    // Restituisce la password criptata
    return $hashed_password;
}
?>