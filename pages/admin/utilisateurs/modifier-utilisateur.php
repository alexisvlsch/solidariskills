<?php

// Connexion à la base de données
require_once('../../../../backend/config.php'); 

// Vérification de la session
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Récupération de l'utilisateur
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: utilisateurs.php");
    exit;
}

$id = (int) $_GET['id'];
$stmt = $pdo->prepare("SELECT nom, email, adresse, statut, description, ville, code_postal FROM utilisateur WHERE id = :id");
$stmt->execute([':id' => $id]);
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$utilisateur) {
    echo "Utilisateur introuvable.";
    exit;
}

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST['nom'] ?? '';
    $email = $_POST['email'] ?? '';
    $adresse = $_POST['adresse'] ?? '';
    $ville = $_POST['ville'] ?? '';
    $code_postal = $_POST['code_postal'] ?? '';
    $description = $_POST['description'] ?? '';
    $statut = $_POST['statut'] ?? '';
    $id_badge = $_POST['id_badge'] ?? null;

    if (!empty($nom) && !empty($email) && !empty($statut)) {
      // Mise à jour des infos utilisateur
      $stmt = $pdo->prepare("UPDATE utilisateur SET nom = :nom, email = :email, statut = :statut, description = :description, adresse = :adresse, ville = :ville, code_postal = :code_postal WHERE id = :id");
      $code_postal = is_numeric($code_postal) && $code_postal !== "" ? (int)$code_postal : null;
      $stmt->execute([
          ':nom' => $nom,
          ':email' => $email,
          ':statut' => $statut,
          ':description' => $description,
          ':adresse' => $adresse,
          ':ville' => $ville,
          ':code_postal' => $code_postal,
          ':id' => $id
      ]);
  
      // Si un badge a été sélectionné, on l'attribue
      if ($id_badge && is_numeric($id_badge)) {
          $stmt = $pdo->prepare("INSERT INTO attribuer (id, id_badge) VALUES (:id, :id_badge) ON CONFLICT DO NOTHING");
          $stmt->execute([
              ':id' => $id,
              ':id_badge' => $id_badge
          ]);
      }
  
      header("Location: utilisateurs.php");
      exit;
  }
}

// Récupère les badges disponibles
$badges = $pdo->query("SELECT id_badge, nom_badge FROM badge")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modifier Utilisateur</title>
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
      
      

    <!-- Formulaire de modification -->
    <section class="contenu">
      <h2 class="titre-section">Modification des informations</h2>

      <form class="formulaire-modification" method="POST">
        <div class="groupe-formulaire">
          <label for="nom">Nom :</label>
          <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($utilisateur['nom']) ?>" required>
        </div>

        <div class="groupe-formulaire">
          <label for="email">Email :</label>
          <input type="email" id="email" name="email" value="<?= htmlspecialchars($utilisateur['email']) ?>" required>
        </div>

       <div class="groupe-formulaire">
          <label for="adresse">Adresse :</label>
          <input type="text" id="adresse" name="adresse" placeholder="Ajouter une adresse..."
            <?= !empty($utilisateur['adresse']) ? 'value="' . htmlspecialchars($utilisateur['adresse']) . '"' : '' ?>>
        </div>

        <div class="groupe-formulaire">
          <label for="ville">Ville :</label>
          <input type="text" id="ville" name="ville" placeholder="Ajouter une ville..."
            <?= !empty($utilisateur['ville']) ? 'value="' . htmlspecialchars($utilisateur['ville']) . '"' : '' ?>>
        </div>

        <div class="groupe-formulaire">
          <label for="code_postal">Code postal :</label>
          <input type="text" id="code_postal" name="code_postal" placeholder="Ajouter un code postal..."
            <?= !empty($utilisateur['code_postal']) ? 'value="' . htmlspecialchars($utilisateur['code_postal']) . '"' : '' ?>>
        </div>


        <div class="groupe-formulaire">
          <label for="description">Description :</label>
          <?php $desc = $utilisateur['description'] ?? ''; ?>
          <textarea name="description" id="description" placeholder="Ajouter une description..."><?= !empty($desc) ? htmlspecialchars($desc) : '' ?></textarea>


        </div>

        <div class="groupe-formulaire">
          <label for="statut">Statut :</label>
          <select id="statut" name="statut" required>
            <option value="Membre" <?= $utilisateur['statut'] === 'Membre' ? 'selected' : '' ?>>Membre</option>
            <option value="Admin" <?= $utilisateur['statut'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
          </select>
        </div>

        <div class="groupe-formulaire">
          <label for="id_badge">Attribuer un badge :</label>
          <select id="id_badge" name="id_badge">
            <option value="">-- Aucun --</option>
            <?php foreach ($badges as $badge): ?>
              <option value="<?= $badge['id_badge'] ?>"><?= htmlspecialchars($badge['nom_badge']) ?></option>
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
