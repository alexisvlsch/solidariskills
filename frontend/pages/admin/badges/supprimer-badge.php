<?php
require_once('../../../../backend/config.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Vérifier que l'ID est présent dans l'URL
if (isset($_GET['id_badge']) && is_numeric($_GET['id_badge'])) {
    $id_badge = (int) $_GET['id_badge'];

    try {
        // Supprimer le badge
        $stmt = $pdo->prepare("DELETE FROM badge WHERE id_badge = :id_badge");
        $stmt->execute(['id_badge' => $id_badge]);

        // Rediriger vers la page des badges
        header("Location: badges.php");
        exit;
    } catch (PDOException $e) {
        echo "Erreur lors de la suppression : " . $e->getMessage();
    }
} else {
    echo "ID de badge non fourni.";
}
