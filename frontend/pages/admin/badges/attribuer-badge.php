<?php
require_once('../admin_auth.php');
require_once('../../../../backend/config.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_user = (int)$_POST['id_user'];
  $id_badge = (int)$_POST['id_badge'];

  $stmt = $pdo->prepare("INSERT INTO attribuer (id, id_badge) VALUES (:id, :id_badge) ON CONFLICT DO NOTHING");
  $stmt->execute(['id' => $id_user, 'id_badge' => $id_badge]);

  header("Location: badges.php");
  exit;
}
?>
