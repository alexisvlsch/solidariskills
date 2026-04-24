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

// Initialisation des variables pour éviter les erreurs
$erreur = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nouveau = $_POST['nouveau_mdp'] ?? '';
    $confirmation = $_POST['confirmation'] ?? '';

    if (empty($nouveau) || empty($confirmation)) {
        $message = "Tous les champs sont requis.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{12,}$/', $nouveau)) {
        $message = "Le mot de passe ne respecte pas les conditions de sécurité.";
    } elseif ($nouveau !== $confirmation) {
        $message = "Les mots de passe ne correspondent pas.";
    } else {
        $hash = password_hash($nouveau, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $pdo->prepare("UPDATE admin SET mdp = :hash WHERE id_admin = :id_admin");
        $stmt->execute([
            'hash' => $hash,
            'id_admin' => $admin_id
        ]);
        $message = "Mot de passe mis à jour avec succès.";
    }
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Changer le mot de passe</title>
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
      <h2 class="titre-section">Changer le mot de passe</h2>

      <?php if ($erreur): ?>
        <p style="color: red;"><?= htmlspecialchars($erreur) ?></p>
      <?php elseif ($message): ?>
        <p style="color: green;"><?= htmlspecialchars($message) ?></p>
      <?php endif; ?>


        <form method="post" class="formulaire-ajout">
        <div class="groupe-formulaire">
          <label for="nouveau_mdp">Nouveau mot de passe :</label>
          <input type="motdepasse" name="nouveau_mdp" id="nouveau_mdp" onpaste="return false" placeholder="Entrez le nouveau le mot de passe" required>
        </div>

        <div class="groupe-formulaire">
          <label for="confirmation">Confirmer le nouveau mot de passe :</label>
          <input type="motdepasse" name="confirmation" id="confirmation" onpaste="return false" placeholder="Confirmez le mot de passe" required>
          <small class="conditions-mdp">
            Conditions requises :
            <ul>
              <li>Minimum 12 caractères</li>
              <li>1 majuscule et 1 minuscule</li>
              <li>1 chiffre</li>
              <li>1 caractère spécial (@$!%*?&)</li>
            </ul>
          </small>
        </div>

        <div class="bouton-formulaire">
          <button type="submit" class="btn-valider">Changer le mot de passe</button>
        </div>
      </form>


      <a href="para-admin.php" class="btn-retour">← Retour au profil</a>
    </section>
  </div>
</div>
</body>
</html>
