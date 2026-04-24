<?php
require_once('../../../../backend/config.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Récupère tous les badges
$stmt = $pdo->query("SELECT id_badge, nom_badge, description_badge FROM badge ORDER BY id_badge ASC");
$badges = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupère tous les utilisateurs ayant des badges attribués
$rows = $pdo->query("
  SELECT u.id, u.nom AS nom_utilisateur, u.email, 
       STRING_AGG(b.nom_badge, ', ') AS badges
  FROM attribuer ab
  JOIN utilisateur u ON ab.id = u.id
  JOIN badge b ON ab.id_badge = b.id_badge
  GROUP BY u.id, u.nom, u.email
  ORDER BY u.nom
")->fetchAll(PDO::FETCH_ASSOC);

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

    <!-- Liste des Badges -->
    <section class="contenu">
        <div class="header-section">
        <h2 class="titre-section">Liste des Badges</h2>
        <a href="ajouter-badge.php" class="btn-ajouter">Créer un nouveau badge</a>
      </div>

        <table class="table-badges">
          <thead>
            <tr>
              <th>Nom de badge</th>
              <th>Description</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($badges as $badge): ?>
              <tr>
                <td><?= htmlspecialchars($badge['nom_badge']) ?></td>
                <td><?= htmlspecialchars($badge['description_badge']) ?></td>
                <td>
                  <a href="modifier-badge.php?id_badge=<?= $badge['id_badge'] ?>" class="btn-modifier">Modifier</a>
                  <a href="supprimer-badge.php?id_badge=<?= $badge['id_badge'] ?>" class="btn-supprimer" onclick="return confirm('Confirmer la suppression ?');">Supprimer</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

    </section>


    <!-- Liste des utilisateurs avec badges -->
    <section class="contenu">
      <h2 class="titre-section">Utilisateurs ayant des badges</h2>

      <table class="table-badges-utilisateurs">
        <thead>
          <tr>
            <th>Utilisateur</th>
            <th>Email</th>
            <th>Badges</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['nom_utilisateur']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['badges']) ?></td>
            <td>
              <a href="badge-utilisateur.php?id=<?= $row['id'] ?>" class="btn-gerer">Gérer</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </section>

  </div>
</div>

</body>
</html>
