<?php
require_once('../../../../backend/config.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Vérifier l'ID
if (!isset($_GET['id_fb']) || !is_numeric($_GET['id_fb'])) {
    echo "ID de feedback non fourni ou invalide.";
    exit;
}

$id_fb = (int)$_GET['id_fb'];

// Récupérer le feedback
$stmt = $pdo->prepare("
    SELECT f.*, 
           u.nom AS nom_utilisateur,
           a.titre AS titre_activite,
           ad.email AS email_admin
    FROM feedback f
    JOIN utilisateur u ON f.id_user = u.id
    JOIN activite a ON f.id_act = a.id_act
    LEFT JOIN admin ad ON f.id_admin_gerant = ad.id_admin
    WHERE f.id_fb = :id_fb
");
$stmt->execute(['id_fb' => $id_fb]);
$feedback = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$feedback) {
    echo "Feedback introuvable.";
    exit;
}

// Compter les autres feedbacks de l'utilisateur
$stmt2 = $pdo->prepare("SELECT COUNT(*) FROM feedback WHERE id_user = :id_user");
$stmt2->execute(['id_user' => $feedback['id_user']]);
$totalFeedbacks = $stmt2->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Détails Feedback</title>
  <link rel="stylesheet" href="feedbacks.css">
  <link rel="stylesheet" href="../style-global-admin.css">
</head>
<body>
<div class="conteneur">
  <?php include("../barreLaterale.php"); ?>
  <div class="principal">
    <?php $pageTitle = "Signalements & Feedbacks "; ?>
    <?php include("../barreHaut.php"); ?>

    <section class="contenu">
      <h2 class="titre-section">Détails du Feedback</h2>

      <table class="table-feedbacks">
  <tr>
    <th>Utilisateur</th>
    <td><?= htmlspecialchars($feedback['nom_utilisateur']) ?></td>
  </tr>
  <tr>
    <th>Activité</th>
    <td><?= htmlspecialchars($feedback['titre_activite']) ?></td>
  </tr>
  <tr>
    <th>Titre</th>
    <td><?= htmlspecialchars($feedback['titre_fb']) ?></td>
  </tr>
  <tr>
    <th>Note</th>
    <td><span class="note-<?= (int)$feedback['note_fb'] ?>"><?= str_repeat('⭐', (int)$feedback['note_fb']) ?></span></td>
  </tr>
  <tr>
    <th>Commentaire</th>
    <td><?= nl2br(htmlspecialchars($feedback['commentaire_fb'])) ?></td>
  </tr>
  <tr>
    <th>Date</th>
    <td><?= htmlspecialchars($feedback['date_fb']) ?></td>
  </tr>
  <tr>
    <th>Admin gérant</th>
    <td><?= $feedback['email_admin'] ? htmlspecialchars($feedback['email_admin']) : 'Non assigné' ?></td>
  </tr>
  <tr>
    <th>Feedbacks totaux de cet utilisateur</th>
    <td><?= $totalFeedbacks ?></td>
  </tr>
  <tr>
    <th>Statut</th>
    <td>
      <?php if ($feedback['traite']) : ?>
        <span class="statut-traite">✔ Traité</span>
      <?php else: ?>
        <span class="statut-non-traite">✖ Non traité</span>
      <?php endif; ?>
    </td>
  </tr>

</table>


      <div class="actions-feedback">
        <a href="feedbacks.php" class="btn-retour">← Retour</a>
        <a href="traiter-feedback.php?id_fb=<?= $feedback['id_fb'] ?>" class="btn-valider-details">Traité</a>
      </div>
    </section>
  </div>
</div>
</body>
</html>
