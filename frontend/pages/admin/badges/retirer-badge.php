<?php
require_once('../admin_auth.php');
require_once('../../../../backend/config.php');

// Vérifier que les paramètres sont bien passés dans l'URL
if (!isset($_GET['id']) || !isset($_GET['id_badge']) || !is_numeric($_GET['id']) || !is_numeric($_GET['id_badge'])) {
    die("Paramètres invalides.");
}

$id_user = (int)$_GET['id'];
$id_badge = (int)$_GET['id_badge'];

try {
    // Supprimer la ligne dans la table attribuer
    $stmt = $pdo->prepare("DELETE FROM attribuer WHERE id = :id_user AND id_badge = :id_badge");
    $stmt->execute([
        'id_user' => $id_user,
        'id_badge' => $id_badge
    ]);

    // Redirection vers la page de gestion du badge pour ce user
    header("Location: badge-utilisateur.php?id=$id_user");
    exit;
} catch (PDOException $e) {
    echo "Erreur lors de la suppression du badge : " . $e->getMessage();
}
?>
