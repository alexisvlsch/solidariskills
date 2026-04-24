
<?php
require_once('../../../../backend/config.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Vérifier que l'ID est présent dans l'URL
if (isset($_GET['id_act']) && is_numeric($_GET['id_act'])) {
    $id_act = (int) $_GET['id_act'];

    try {
        // Supprimer l'activité
        $stmt = $pdo->prepare("DELETE FROM activite WHERE id_act = :id_act");
        $stmt->execute(['id_act' => $id_act]);

        // Rediriger vers la page des activités
        header("Location: activites.php");
        exit;
    } catch (PDOException $e) {
        echo "Erreur lors de la suppression : " . $e->getMessage();
    }
} else {
    echo "ID de l'activité non fourni.";
}
