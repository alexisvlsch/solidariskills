<?php

session_start();
header("Content-Type: application/json");

// Régénérer le token à chaque requête pour plus de sécurité
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

echo json_encode(['token' => $_SESSION['csrf_token']]);
?>