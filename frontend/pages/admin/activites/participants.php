<?php



session_start();
/*
if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../../../../backend/connexion.php");
    exit;
}
*/

require_once('../../../../backend/config.php');

$id_act = $_GET['id_act'] ?? null;
if (!$id_act || !is_numeric($id_act)) {
    echo "ID activité invalide.";
    exit;
}

// Récupération du titre de l'activité
$stmtTitre = $pdo->prepare("SELECT titre FROM activite WHERE id_act = :id_act");
$stmtTitre->execute([':id_act' => $id_act]);
$titreActivite = $stmtTitre->fetchColumn();

// requête pour récupérer les participants
$stmt = $pdo->prepare("
  SELECT u.id, u.nom, u.email
  FROM utilisateur u
  JOIN participer p ON u.id = p.id
  WHERE p.id_act = :id_act
");
$stmt->execute([':id_act' => $id_act]);
$participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Participants</title>
  <link rel="stylesheet" href="activites.css">
  <link rel="stylesheet" href="../style-global-admin.css">
</head>
<body>

<div class="conteneur">
  <?php include("../barreLaterale.php"); ?>
  <div class="principal">
    <?php $pageTitle = "Gestion des Participants"; ?>
    <?php include("../barreHaut.php"); ?>

    <section class="contenu">
    <h2 class="titre-section">Participants à l’activité (n°<?= htmlspecialchars($id_act) ?>) : <?= htmlspecialchars($titreActivite) ?></h2>

      <table class="table-participants">
        <thead>
          <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($participants as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['nom']) ?></td>
            <td><?= htmlspecialchars($p['email']) ?></td>
            <td>
              <a href="supprimer-participation.php?id=<?= $p['id'] ?>&id_act=<?= $id_act ?>" class="btn-supprimer" onclick="return confirm('Supprimer ce participant ? ');">Supprimer</a>
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
