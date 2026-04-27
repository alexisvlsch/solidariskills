<?php
require_once('../admin_auth.php');
require_once('../../../../backend/config.php');

$id_user = $_GET['id'] ?? null;
$id_act = $_GET['id_act'] ?? null;

if ($id_user && $id_act) {
    $stmt = $pdo->prepare("DELETE FROM participer WHERE id = :id AND id_act = :id_act");
    $stmt->execute([
        ':id' => $id_user,
        ':id_act' => $id_act
    ]);
}

header("Location: participants.php?id_act=$id_act");
exit;

