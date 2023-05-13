<?php 
require_once 'config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master\src\Exception.php';
require 'PHPMailer-master\src\PHPMailer.php';
require 'PHPMailer-master\src\SMTP.php';

function connect() {
    // Inizializzo la connessione al db che poi includerò in tutti gli altri file
    $dbh = new mysqli('localhost', 'root', '', 'merende');
    return $dbh;
}

function sqlSelect($db, $sql, $format = false, ...$params) {
    $stmt = $db->prepare($sql);
    if($format) {
        $stmt->bind_param($format, ...$params);
    }
    if($stmt->execute()) {
        $res = $stmt->get_result();
        $stmt->close();
        return $res;
    }
    $stmt->close();
    return false;
}

function sqlInsert($db, $sql, $format = false, ...$params) {
    $stmt = $db->prepare($sql);
    if($format) {
        $stmt->bind_param($format, ...$params);
    }
    if($stmt->execute()) {
        $id = $stmt->insert_id;
        $stmt->close();
        return $id;
    }
    $stmt->close();
    return -1;
}

function sqlUpdate($db, $sql, $format = false, ...$params) {
    $stmt = $db->prepare($sql);
    if($format) {
        $stmt->bind_param($format, ...$params);
    }
    if($stmt->execute()) {
        $stmt->close();
        return true;
    }
    $stmt->close();
    return false;
}

function sqlDelete($db, $sql, $format = false, ...$params) {
    $stmt = $db->prepare($sql);
    if($format) {
        $stmt->bind_param($format, ...$params);
    }
    if($stmt->execute()) {
        $stmt->close();
        return true;
    }
    $stmt->close();
    return false;
}

function createToken() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $seed = urlSafeEncode(random_bytes(8));
    $t = time();
    $hash = urlSafeEncode(hash_hmac('sha256', session_id() . $seed . $t, CSRF_TOKEN_SECRET, true));
    $_SESSION['Token'] = urlSafeEncode($hash);
    return urlSafeEncode($hash);
}

function urlSafeEncode($string) {
    return rtrim(strtr(base64_encode($string), '+/', '-_'), '=');
}
function urlSafeDecode($string) {
    return base64_decode(strtr($string, '-_', '+/'));
}

function sendEmail($destinatario, $nome, $subj, $body){
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = SMTP_HOST;                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = SMTP_USERNAME;                     //SMTP username
        $mail->Password   = SMTP_PASSWORD;                               //SMTP password
        $mail->SMTPSecure = 'ssl';            //Enable implicit TLS encryption
        $mail->Port       = SMTP_PORT;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $mail->addAddress($destinatario, $nome);     //Add a recipient

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subj;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo $e;
        return false;
    }
}

function isOnTime($order_timestamp, $deadline_timestamp) {
    // In questa funzione comparo due orari in cui uno è la scadenza e restituisco true o false per vedere
    // se ancora sono in tempo a fare gli ordini
    $order_day = date("d-m-Y", $order_timestamp);
    $deadline_day = date("d-m-Y", $deadline_timestamp);
    if ($order_day == $deadline_day) {
        // Se il giorno coincide verifico che l'ora attuale sia inferiore a quella passata per parametro
        // Converte la stringa del timestamp dell'ordine in un timestamp Unix
        $order_time = strtotime(date("H:i:s", $order_timestamp));

        // Confronta i timestamp per verificare se l'ora dell'ordine è precedente all'ora di scadenza
        $deadline_time = strtotime(date("H:i:s", $deadline_timestamp));
        if ($order_time < $deadline_time) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
function getDayID($dbh) {
    $timestamp = time();
    $date = new DateTime('@' . $timestamp); // Crea un oggetto DateTime
    $day_name = ucfirst($date->format('l'));
    $sql = "SELECT * FROM giorni;";
    $res = sqlSelect($dbh, $sql);
    $giorni = $res->fetch_all(MYSQLI_ASSOC);
    $res->free();
    foreach($giorni as $giorno) {
        if($giorno['Giorno'] == $day_name) {
            return $giorno['ID'];
        }
    }
}

?>