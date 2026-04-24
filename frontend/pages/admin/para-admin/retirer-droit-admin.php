<?php
require_once('../../../../backend/config.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Vérifier que l'ID est bien présent
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];

    try {
        // Mettre à jour le statut à 'Membre'
        $stmt = $pdo->prepare("UPDATE utilisateur SET statut = 'Membre' WHERE id = :id");
        $stmt->execute(['id' => $id]);

        // Redirection vers la page de gestion
        header("Location: para-admin.php");
        exit;
    } catch (PDOException $e) {
        echo "Erreur lors de la modification : " . $e->getMessage();
    }
} else {
    echo "ID invalide ou non fourni.";
}
