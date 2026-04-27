<?php
require_once('../admin_auth.php');
require_once('../../../../backend/config.php');

// Vérifier l'ID de l'utilisateur
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID utilisateur manquant ou invalide.");
}
$id_user = (int)$_GET['id'];

// Traitement de l'attribution d'un badge (formulaire POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_badge'])) {
    $id_badge = (int) $_POST['id_badge'];

    // Vérifier si le badge n'est pas déjà attribué
    $check = $pdo->prepare("SELECT 1 FROM attribuer WHERE id = :id_user AND id_badge = :id_badge");
    $check->execute(['id_user' => $id_user, 'id_badge' => $id_badge]);

    if ($check->rowCount() === 0) {
        $insert = $pdo->prepare("INSERT INTO attribuer (id, id_badge) VALUES (:id_user, :id_badge)");
        $insert->execute(['id_user' => $id_user, 'id_badge' => $id_badge]);
    }

}

// Récupérer tous les badges disponibles
$badges = $pdo->query("SELECT * FROM badge ORDER BY nom_badge")->fetchAll();

// Récupérer les badges déjà attribués à cet utilisateur
$stmt = $pdo->prepare("
    SELECT b.* FROM badge b
    JOIN attribuer a ON b.id_badge = a.id_badge
    WHERE a.id = :id_user
");
$stmt->execute(['id_user' => $id_user]);
$badgesUser = $stmt->fetchAll();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des Badges</title>
  <link rel="stylesheet" href="badges.css">
  <link rel="stylesheet" href="../style-global-admin.css">
</head>
<body>
<div class="conteneur">
  <?php include("../barreLaterale.php"); ?>
  <div class="principal">
  <?php $pageTitle = "Gestion des Badges"; ?>
    <?php include("../barreHaut.php"); ?>

    <section class="contenu">
      <h2 class="titre-section">Gestion des badges de l'utilisateur - ID <?= $id_user ?></h2>

      <h3>Badges attribués</h3>

<ul class="badge-user-list">
  <?php foreach ($badgesUser as $b): ?>
    <li>
      <div>
        <strong><?= htmlspecialchars($b['nom_badge']) ?></strong> – <?= htmlspecialchars($b['description_badge']) ?>
      </div>
      <a href="retirer-badge.php?id=<?= $id_user ?>&id_badge=<?= $b['id_badge'] ?>" class="btn-retirer">Retirer</a>
    </li>
  <?php endforeach; ?>
</ul>

<h3>Attribuer un nouveau badge</h3>

<form method="post" class="form-attribuer">
  <select name="id_badge" required>
    <option value="">-- Sélectionner un badge --</option>
    <?php foreach ($badges as $badge): ?>
      <option value="<?= $badge['id_badge'] ?>"><?= htmlspecialchars($badge['nom_badge']) ?></option>
    <?php endforeach; ?>
  </select>
  <button type="submit" class="btn-ajouter">Attribuer</button>
</form>


<a href="../badges/badges.php" class="btn-retour">← Retour</a>

    </section>
  </div>
</div>
</body>
</html>
