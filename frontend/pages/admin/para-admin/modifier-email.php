<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once('../../../../backend/config.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Vérifie que l'admin est connecté
$admin_id = $_SESSION['admin_id'] ?? null;
if (!$admin_id) {
    die("Admin non connecté.");
}

// Initialisation des variables
$erreur = '';
$message = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nouvel_email = trim($_POST['nouvel_email'] ?? '');
    $confirmation = trim($_POST['confirmation'] ?? '');

    if (empty($nouvel_email) || empty($confirmation)) {
        $erreur = "Tous les champs sont requis.";
    } elseif (!filter_var($nouvel_email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Format d'email invalide.";
    } elseif ($nouvel_email !== $confirmation) {
        $erreur = "Les emails ne correspondent pas.";
    } else {
        // Mise à jour en base
        $stmt = $pdo->prepare("UPDATE admin SET email = :email WHERE id_admin = :id_admin");
        $stmt->execute([
            'email' => $nouvel_email,
            'id_admin' => $admin_id
        ]);
        $message = "Email mis à jour avec succès.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Changer l'email</title>
  <link rel="stylesheet" href="profil-admin.css">
  <link rel="stylesheet" href="../style-global-admin.css">
</head>
<body>
<div class="conteneur">
  <?php include("../barreLaterale.php"); ?>
  <div class="principal">
    <?php $pageTitle = "Paramètres Administrateur"; ?>
    <?php include("../barreHaut.php"); ?>

    <section class="contenu">
      <h2 class="titre-section">Changer l'adresse email</h2>

      <?php if ($erreur): ?>
        <p style="color: red;"><?= htmlspecialchars($erreur) ?></p>
      <?php elseif ($message): ?>
        <p style="color: green;"><?= htmlspecialchars($message) ?></p>
      <?php endif; ?>

      <form method="post" class="formulaire-ajout">
        <div class="groupe-formulaire">
          <label for="nouvel_email">Nouvel email :</label>
          <input type="email" name="nouvel_email" id="nouvel_email" onpaste="return false" placeholder="Entrez le nouvel email" required>
        </div>

        <div class="groupe-formulaire">
          <label for="confirmation">Confirmer le nouvel email :</label>
          <input type="email" name="confirmation" id="confirmation" onpaste="return false" placeholder="Confirmez l'email" required>
        </div>

        <div class="bouton-formulaire">
          <button type="submit" class="btn-valider">Changer l'email</button>
        </div>
      </form>

      <a href="para-admin.php" class="btn-retour">← Retour au profil</a>
    </section>
  </div>
</div>
</body>
</html>
