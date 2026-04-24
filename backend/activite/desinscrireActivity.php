<?php
session_start();
require_once('../config.php');

// Désinscrit l'utilisateur d'une activité
if (!isset($_SESSION['user_id'])) {
    echo "Erreur : utilisateur non connecté.";
    exit;
}

$id_user = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['desinscrire_id_act'])) {
    $id_act = intval($_POST['desinscrire_id_act']);

    if ($id_act > 0) {
        $stmt = $pdo->prepare("DELETE FROM participer WHERE id = :id_user AND id_act = :id_act");
        $stmt->execute([':id_user' => $id_user, ':id_act' => $id_act]);
        header('Location: /frontend/pages/mesActivites/mesActivites.php?message=desinscription_reussie');
        exit;
    }
}

echo "Erreur : données invalides.";
?>