<?php
// File centralizzato per le credenziali del database
$db_host = 'localhost'; 
$db_user = 'mvs'; 
$db_name = 'gruppo_vitolo_db';
$db_pass= '1';
$conn_gu = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn_gu->connect_error) {
    die("Connessione fallita: " . $conn_gu->connect_error);
    }
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn_gu->connect_error);
    }
?>
