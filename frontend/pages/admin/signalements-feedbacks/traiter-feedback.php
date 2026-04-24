<?php
require_once('../../../../backend/config.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Vérifie que l’ID est présent et valide
if (isset($_GET['id_fb']) && is_numeric($_GET['id_fb'])) {
    $id_fb = (int)$_GET['id_fb'];

    try {
        // Marque le feedback comme traité
        $stmt = $pdo->prepare("UPDATE feedback SET traite = TRUE WHERE id_fb = :id_fb");
        $stmt->bindParam(':id_fb', $id_fb, PDO::PARAM_INT);
        $stmt->execute();

        // Redirection vers la liste
        header("Location: feedbacks.php");
        exit;
    } catch (PDOException $e) {
        echo "Erreur lors du traitement du feedback : " . $e->getMessage();
    }
} else {
    echo "ID de feedback non fourni.";
}
