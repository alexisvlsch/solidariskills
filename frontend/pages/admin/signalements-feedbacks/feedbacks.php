<?php
require_once('../admin_auth.php');

require_once('../../../../backend/config.php');

// Requête pour récupérer tous les feedbacks avec auteur et activité
$stmt = $pdo->query("
  SELECT f.id_fb, f.titre_fb, f.note_fb, f.commentaire_fb, f.date_fb, f.traite,
         u.nom AS nom_utilisateur,
         a.titre AS titre_activite
  FROM feedback f
  JOIN utilisateur u ON f.id_user = u.id
  JOIN activite a ON f.id_act = a.id_act
  ORDER BY f.date_fb DESC
");


$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Signalements & Feedbacks</title>
  <link rel="stylesheet" href="feedbacks.css">
  <link rel="stylesheet" href="../style-global-admin.css">
</head>
<body>

<div class="conteneur">
  <?php include("../barreLaterale.php"); ?>
  <div class="principal">
    <?php $pageTitle = "Signalements & Feedbacks"; ?>
    <?php include("../barreHaut.php"); ?>

    <section class="contenu">
      <h2 class="titre-section">Feedbacks et Signalements</h2>

      <div class="barre-recherche">
        <label for="recherche">Rechercher :</label>
        <input type="text" id="recherche" placeholder="Tapez un mot-clé...">
      </div>

      <table class="table-feedbacks">
        <thead>
          <tr>
          <th>Utilisateur</th>
          <th>Titre</th>
          <th>Note</th>
          <th>Commentaire</th>
          <th>Activité</th>
          <th>Statut</th>
          <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($feedbacks as $feedback): ?>
          <tr>
              <td><?= htmlspecialchars($feedback['nom_utilisateur']) ?></td>
              <td><?= htmlspecialchars($feedback['titre_fb']) ?></td>
              <td><?= str_repeat('⭐', (int)$feedback['note_fb']) ?></td>
              <td><?= htmlspecialchars($feedback['commentaire_fb']) ?></td>        
              <td><?= htmlspecialchars($feedback['titre_activite']) ?></td>
              <td>
                  <?php if ($feedback['traite']) : ?>
                    <span class="statut-traite">✔ Traité</span>
                  <?php else: ?>
                    <span class="statut-non-traite">✖ Non traité</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="actions-buttons">
                    <a href="details-feedback.php?id_fb=<?= $feedback['id_fb'] ?>" class="btn-consulter">Details</a>
                    <?php if (!$feedback['traite']) : ?>
                      <a href="traiter-feedback.php?id_fb=<?= $feedback['id_fb'] ?>" class="btn-valider">Traité</a>
                    <?php endif; ?>
                    <a href="supprimer-feedback.php?id_fb=<?= $feedback['id_fb'] ?>" class="btn-supprimer" onclick="return confirm('Confirmer la suppression ?');">Supprimer</a>
                  </div>
                </td>

          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>
  </div>
</div>

<!-- Barre de recherche -->
<script>
document.getElementById('recherche').addEventListener('input', function () {
  const filtre = this.value.toLowerCase();
  const lignes = document.querySelectorAll('table tbody tr');
  lignes.forEach(ligne => {
    const texte = ligne.textContent.toLowerCase();
    ligne.style.display = texte.includes(filtre) ? '' : 'none';
  });
});
</script>

</body>
</html>
