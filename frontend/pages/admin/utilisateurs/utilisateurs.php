<?php
require_once('../admin_auth.php');

// Connexion à la base de données
require_once('../../../../backend/config.php');
$utilisateurs = $pdo->query("SELECT id, nom, email, statut FROM utilisateur")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestion des Utilisateurs</title>
  <link rel="stylesheet" href="utilisateurs.css">
  <link rel="stylesheet" href="../style-global-admin.css">
</head>
<body>

<div class="conteneur">
  
  <!-- Barre latérale -->
  <?php include("../barreLaterale.php"); ?>

  <!-- Partie principale -->
  <div class="principal">
    
    <!-- Barre du haut -->
    <?php $pageTitle = "Gestion des Utilisateurs"; ?>
    <?php include("../barreHaut.php"); ?>

    <!-- Tableau des utilisateurs -->
    <section class="contenu">
      <h2 class="titre-section">Liste des Utilisateurs</h2>

      <!-- barre de recherche -->
      <div class="barre-recherche">
        <label for="recherche"> Rechercher :</label>
        <input type="text" id="recherche" placeholder="Tapez un mot-clé...">
      </div>


      <table class="table-utilisateurs">
        <thead>
          <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($utilisateurs as $u): ?>
          <tr>
            <td><?= htmlspecialchars($u['nom']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['statut']) ?></td>
            <td>
              <a href="modifier-utilisateur.php?id=<?= $u['id'] ?>" class="btn-modifier">Modifier/Gérer</a> <!-- Lien vers la page de modification avec l'ID de l'utilisateur -->
              <a href="supprimer-utilisateur.php?id=<?= $u['id'] ?>" class="btn-supprimer" onclick="return confirm('Confirmer la suppression ?');">Supprimer</a> <!-- Button de suppression avec l'ID de l'utilisateur -->
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>

  </div>

</div>


<!-- Script pour la barre de recherche -->
<script>
document.getElementById('recherche').addEventListener('input', function () {
  const filtre = this.value.toLowerCase();
  const lignes = document.querySelectorAll('table tbody tr');

  lignes.forEach((ligne) => {
    const texte = ligne.textContent.toLowerCase();
    ligne.style.display = texte.includes(filtre) ? '' : 'none';
  });
});
</script>


</body>
</html>
