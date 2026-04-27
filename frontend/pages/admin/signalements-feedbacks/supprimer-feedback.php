<?php
require_once('../admin_auth.php');
require_once('../../../../backend/config.php'); 

// Activer les erreurs

// Vérifie que l’ID est fourni
if (isset($_GET['id_fb']) && is_numeric($_GET['id_fb'])) {
    $id_fb = (int) $_GET['id_fb'];

    try {
        $stmt = $pdo->prepare("DELETE FROM feedback WHERE id_fb = :id_fb");
        $stmt->bindParam(':id_fb', $id_fb, PDO::PARAM_INT); 
        $stmt->execute();

        header("Location: feedbacks.php");
        exit;
    } catch (PDOException $e) {
        echo "Erreur suppression : " . $e->getMessage();
    }
} else {
    echo "ID de feedback non fourni.";
}
