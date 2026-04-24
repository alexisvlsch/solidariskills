<?php
/**
 * @file fonctions.php
 * @brief Fonctions globales pour l'application
 */

// On inclut config.php pour avoir la variable $pdo prête à l’emploi
require_once __DIR__ . '/config.php';

/**
 * Retourne l'instance PDO connectée à la base
 */
function getDB(): PDO {
    global $pdo;
    return $pdo;
}

/**
 * Envoie une réponse JSON standardisée
 */
function envoieDonnees($statut, $contenu, $code_http = 200) {
    http_response_code($code_http);
    echo json_encode([
        'statut' => $statut,
        'data'   => $contenu
    ]);
    exit;
}

/**
 * Nettoie une entrée utilisateur
 */
function nettoyerInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Génère un token CSRF et le stocke en session
 */
function genererTokenCSRF() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
?>
