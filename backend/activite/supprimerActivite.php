<?php
session_start();
require_once('../config.php');

// Supprime une activité créée par l'utilisateur
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    echo "Erreur : utilisateur non connecté.";
    exit;
}

$id_user = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_act']) && is_numeric($_POST['id_act'])) {
    $id_act = intval($_POST['id_act']);

    try {
        $stmt = $pdo->prepare("SELECT id_act FROM activite WHERE id_act = :id_act AND id_createur = :id_user");
        $stmt->execute([':id_act' => $id_act, ':id_user' => $id_user]);
        $activite = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$activite) {
            echo "Erreur : vous n'avez pas les droits pour supprimer cette activité ou elle n'existe pas.";
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM reserver WHERE id_act = :id_act");
        $stmt->execute([':id_act' => $id_act]);

        $stmt = $pdo->prepare("DELETE FROM participer WHERE id_act = :id_act");
        $stmt->execute([':id_act' => $id_act]);

        $stmt = $pdo->prepare("DELETE FROM activite WHERE id_act = :id_act AND id_createur = :id_user");
        $stmt->execute([':id_act' => $id_act, ':id_user' => $id_user]);

        header('Location: /frontend/pages/mesActivites/mesActivites.php?message=suppression_reussie');
        exit;
    } catch (Exception $e) {
        echo "Erreur lors de la suppression : " . $e->getMessage();
        exit;
    }
} else {
    echo "Erreur : données invalides.";
    exit;
}
?>