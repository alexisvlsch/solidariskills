<?php
require_once('../admin_auth.php');

// Connexion à la base de données
require_once('../../../../backend/config.php'); 

// Vérification de la session

// recuperation des données des activités
$activites = $pdo->query("SELECT id_act, titre, description, localisation, date_activite FROM activite")->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestion des Activités</title>
  <link rel="stylesheet" href="activites.css">
  <link rel="stylesheet" href="../style-global-admin.css">
</head>
<body>

<div class="conteneur">

  <!-- Barre latérale -->
  <?php include("../barreLaterale.php"); ?>

  <!-- Par  tie principale -->
  <div class="principal">

    <!-- Barre du haut -->
    <?php $pageTitle = "Gestion d'Activités"; ?>
    <?php include("../barreHaut.php"); ?>

    <!-- Contenu principal -->
    <section class="contenu">
      <h2 class="titre-section">Liste des Activités</h2>

      <!-- barre de recherche -->
      <div class="barre-recherche">
        <label for="recherche"> Rechercher :</label>
        <input type="text" id="recherche" placeholder="Tapez un mot-clé...">
      </div>


      <table class="table-activites">
        <thead>
          <tr>
            <th>Titre</th>
            <th>Description</th>
            <th>Date</th>
            <th>Lieu</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($activites as $a): ?>
          <tr>
            <td><?= htmlspecialchars($a['titre']) ?></td>
            <td><?= htmlspecialchars($a['description']) ?></td>
            <td><?= $a['date_activite'] ? date('d/m/Y', strtotime($a['date_activite'])) : '—' ?></td>
            <td><?= htmlspecialchars($a['localisation']) ?></td>
            <td>
              <div class="boutons-actions" >
                <a href="modifier-activite.php?id=<?= $a['id_act'] ?>" class="btn-modifier">Modifier</a>
                <a href="participants.php?id_act=<?= $a['id_act'] ?>" class="btn-consulter">Participants</a>
                <a href="supprimer-activite.php?id_act=<?= $a['id_act'] ?>" class="btn-supprimer" onclick="return confirm('Supprimer cette activité ? ');">Supprimer</a>
              </div>
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
