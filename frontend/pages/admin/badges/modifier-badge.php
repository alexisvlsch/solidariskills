<?php
require_once('../admin_auth.php');
require_once('../../../../backend/config.php');

// Vérifier que l'ID est présent dans l'URL
if (!isset($_GET['id_badge']) || !is_numeric($_GET['id_badge'])) {
    echo "ID invalide.";
    exit;
}

$id_badge = (int) $_GET['id_badge'];
$message = '';

// Récupérer le badge
$stmt = $pdo->prepare("SELECT * FROM badge WHERE id_badge = :id_badge");
$stmt->execute(['id_badge' => $id_badge]);
$badge = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$badge) {
    echo "Badge introuvable.";
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($nom !== '') {
        $stmt = $pdo->prepare("UPDATE badge SET nom_badge = :nom, description_badge = :description WHERE id_badge = :id_badge");
        $stmt->execute([
            ':nom' => $nom,
            ':description' => $description,
            ':id_badge' => $id_badge
        ]);
        $message = "Le badge a été mis à jour.";
        // rafraîchir les données
        $badge['nom_badge'] = $nom;
        $badge['description_badge'] = $description;
    } else {
        $message = "Le nom du badge est requis.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Modifier un Badge</title>
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
      <h2 class="titre-section">Modifier le badge</h2>

      <?php if ($message): ?>
        <p style="color: green;"><?= htmlspecialchars($message) ?></p>
      <?php endif; ?>

      <form method="POST" class="formulaire-ajout">
        <div class="groupe-formulaire">
          <label for="nom">Nom :</label>
          <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($badge['nom_badge']) ?>" required>
        </div>

        <div class="groupe-formulaire">
          <label for="description">Description :</label>
          <textarea id="description" name="description" rows="3"><?= htmlspecialchars($badge['description_badge']) ?></textarea>
        </div>

        <div class="bouton-formulaire">
        <div style="margin-top: 20px;">
            <a href="badges.php" class="btn-retour">← Retour</a>
            <button type="submit" class="btn-valider">Enregistrer</button>
        </div>

        </div>
      </form>
    </section>
  </div>
</div>
</body>
</html>
