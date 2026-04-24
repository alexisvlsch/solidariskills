<?php
require_once('../../../../backend/config.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$message = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($nom !== '') {
        $stmt = $pdo->prepare("INSERT INTO badge (nom_badge, description_badge) VALUES (:nom, :description)");
        $stmt->execute([
            ':nom' => $nom,
            ':description' => $description
        ]);
        
        // Rediriger vers la page des badges
        header("Location: badges.php");
        exit;

    } else {
        $message = "Le nom du badge est obligatoire.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ajouter un Badge</title>
  <link rel="stylesheet" href="badges.css">
  <link rel="stylesheet" href="../style-global-admin.css">
</head>
<body>

<div class="conteneur">
  <?php include("../barreLaterale.php"); ?>
  <div class="principal">
    <?php $pageTitle = "Ajouter un Badge"; ?>
    <?php include("../barreHaut.php"); ?>

    <section class="contenu">
      <h2 class="titre-section">Ajouter un nouveau badge</h2>

      <form method="POST" class="formulaire-ajout">
        <div class="groupe-formulaire">
          <label for="nom">Nom de Badge :</label>
          <input type="text" id="nom" name="nom" required>
        </div>

        <div class="groupe-formulaire">
          <label for="description">Description :</label>
          <textarea id="description" name="description" rows="3"></textarea>
        </div>

        <div class="bouton-formulaire">
          <a href="badges.php" class="btn-retour">← Retour</a>
          <button type="submit" class="btn-valider">Ajouter le badge</button>
        </div>
      </form>
    </section>
  </div>
</div>

</body>
</html>
