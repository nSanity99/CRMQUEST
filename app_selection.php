<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Selezione Applicazione - Gruppo Vitolo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body, html { height: 100%; margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg,rgba(255, 74, 68, 0.8) 0%,rgb(87, 35, 35) 100%); display: flex; align-items: center; justify-content: center; }
        .selection-container { background: rgba(255,255,255,0.1); backdrop-filter: blur(12px); padding: 40px; border-radius: 20px; box-shadow: 0 8px 32px rgba(0,0,0,0.25); text-align: center; }
        .selection-container img { width: 100px; display: block; margin: 0 auto 30px auto; filter: drop-shadow(0 0 10px #B08D57); }
        h1 { color: #fff; margin-bottom: 20px; }
        .apps { display: flex; flex-direction: column; gap: 15px; }
        .app-link { background: #B08D57; color: white; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; transition: background 0.3s ease, transform 0.2s; }
        .app-link:hover { background: #9c7b4c; transform: translateY(-2px); }
        .app-link.disabled { background: #6c757d; pointer-events: none; }
    </style>
</head>
<body>
    <div class="selection-container">
        <img src="assets/logo.png" alt="Logo Gruppo Vitolo">
        <h1>Scegli l'applicazione</h1>
        <div class="apps">
            <a class="app-link" href="dashboard.php">CRM - Acquisti e Segnalazioni</a>
            <a class="app-link disabled" href="#">App 1 (prossimamente)</a>
            <a class="app-link disabled" href="#">App 2 (prossimamente)</a>
        </div>
    </div>
</body>
</html>
