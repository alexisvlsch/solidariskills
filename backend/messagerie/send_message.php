<?php
session_start();
require_once('../config.php');

// Gère l'envoi d'un message
if (!isset($_SESSION['user_id'])) {
    header('Location: /frontend/pages/connexion_inscription/auth.php');
    exit();
}

$id_user = $_SESSION['user_id'];
$destinataire = intval($_POST['destinataire'] ?? 0);
$contenu = trim($_POST['contenu_msg'] ?? '');

if ($destinataire > 0 && $contenu !== '') {
    $stmt = $pdo->prepare("INSERT INTO message (contenu_msg, id_expediteur, id_destinataire) VALUES (:contenu, :exp, :dest)");
    $stmt->execute([
        ':contenu' => $contenu,
        ':exp' => $id_user,
        ':dest' => $destinataire
    ]);
}
header("Location: /frontend/pages/messagerie/messagerie.php?contact=$destinataire");
exit();