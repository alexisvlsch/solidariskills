<?php
/**
 * @file postgre.php
 * @brief Connexion sécurisée à PostgreSQL
 * @warning NE PAS EXPOSER CE FICHIER PUBLICIQUEMENT
 */


// Initialise la connexion sécurisée à PostgreSQL
header("Content-Type: application/json");

define('DB_HOST', 'localhost');
define('DB_NAME', 'solidariskills_db');
define('DB_USER', 'etu');
define('DB_PASS', 'solidariskills');


try {
    $pdo = new PDO(
        "pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    error_log("[POSTGRE ERROR] " . date('Y-m-d H:i:s') . " - " . $e->getMessage());
    envoieDonnees("erreur", "Service temporairement indisponible", 503);
    exit;
}
?>
