<?php
require_once('../admin_auth.php');

// Connexion à la base de données
require_once('../../../../backend/config.php'); 

// Vérification de la session

// Vérifie la présence de l'ID dans l'URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: activites.php");
    exit;
}

// Récupérer l'ID de l'activité
$id = (int) $_GET['id'];

// Récupérer les infos de l’activité
$stmt = $pdo->prepare("SELECT titre, description, localisation, date_activite, conditions_req FROM activite WHERE id_act = :id");
$stmt->execute([':id' => $id]);
$activite = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$activite) {
    echo "Activité introuvable.";
    exit;
}

// Récupérer les conditions météo disponibles
$conditions_req = $pdo->query("SELECT DISTINCT conditions_req FROM activite WHERE conditions_req IS NOT NULL")->fetchAll(PDO::FETCH_ASSOC);

// Traitement de la modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'] ?? '';
    $description = $_POST['description'] ?? '';
    $localisation = $_POST['localisation'] ?? '';
    $date_activite = $_POST['date_activite'] ?? '';
    $conditions_req_post = $_POST['conditions_req'] ?? null;

    // Validation des données (valeur vide = NULL autorisé)
    $allowed_conditions = ['Sec', 'Pluie', 'Neige'];
    if ($conditions_req_post !== '' && !in_array($conditions_req_post, $allowed_conditions, true)) {
        echo "Condition météo invalide.";
        exit;
    }
    $conditions_req_post = $conditions_req_post !== '' ? $conditions_req_post : null;

    if ($titre && $description && $localisation && $date_activite) {
        $stmt = $pdo->prepare("UPDATE activite SET titre = :titre, description = :description, localisation = :localisation, date_activite = :date_activite, conditions_req = :conditions_req WHERE id_act = :id");
        if (!$stmt->execute([
            ':titre' => $titre,
            ':description' => $description,
            ':localisation' => $localisation,
            ':date_activite' => $date_activite,
            ':conditions_req' => $conditions_req_post,
            ':id' => $id
        ])) {
            echo "Erreur lors de la mise à jour de l'activité.";
            exit;
        }
    }

    header("Location: activites.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modifier Activité</title>
  <link rel="stylesheet" href="activites.css">
  <link rel="stylesheet" href="../style-global-admin.css">
</head>
<body>

<div class="conteneur">
  <!-- Barre latérale -->
  <?php include("../barreLaterale.php"); ?>

  <!-- Partie principale -->
  <div class="principal">
    <!-- Barre du haut -->
    <?php $pageTitle = "Gestion d'Activités"; ?>
    <?php include("../barreHaut.php"); ?>

    <!-- Formulaire de modification -->
    <section class="contenu">
      <h2 class="titre-section">Modification des informations de l'Activité</h2>

      <form class="formulaire-modification" method="POST">
        <div class="groupe-formulaire">
          <label for="titre">Titre :</label>
          <input type="text" name="titre" id="titre" value="<?= htmlspecialchars($activite['titre']) ?>" required>
        </div>

        <div class="groupe-formulaire">
          <label for="description">Description :</label>
          <textarea name="description" id="description" required><?= htmlspecialchars($activite['description']) ?></textarea>
        </div>

        <div class="groupe-formulaire">
          <label for="localisation">Lieu :</label>
          <input type="text" name="localisation" id="localisation" value="<?= htmlspecialchars($activite['localisation']) ?>" required>
        </div>

        <div class="groupe-formulaire">
          <label for="date_activite">Date :</label>
          <input type="date" name="date_activite" id="date_activite" value="<?= htmlspecialchars($activite['date_activite']) ?>" required>
        </div>

        <div class="groupe-formulaire">
          <label for="conditions_req">Conditions Météo :</label>
          <select id="conditions_req" name="conditions_req" required>
            <option value="">-- Sélectionner une condition --</option>
            <?php foreach ($conditions_req as $condition): ?>
              <option value="<?= htmlspecialchars($condition['conditions_req']) ?>" 
                <?= ($activite['conditions_req'] === $condition['conditions_req']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($condition['conditions_req']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="bouton-formulaire">
          <button type="submit" class="btn-sauvegarder">Enregistrer</button>
        </div>
      </form>
    </section>
  </div>
</div>

</body>
</html>