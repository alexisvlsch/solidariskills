<?php

// verifier si la session est déjà démarrée
// Si la session n'est pas déjà démarrée, démarrez-la
require_once('../admin_auth.php');
require_once('../../../../backend/config.php'); 

// Récupération de l’ID admin depuis la session
$admin_id = $_SESSION['admin_id'] ?? null;

if (!$admin_id) {
    echo "Vous n'avez pas accès ! Vous n'êtes pas l'Admin principal.";
    exit;
}

// Récupération des infos
$stmt = $pdo->prepare("SELECT email FROM admin WHERE id_admin = :id_admin");
$stmt->execute(['id_admin' => $admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
$adminEmail = $admin['email'] ?? 'Inconnu';

// Récupère tous les utilisateurs avec statut "Admin"
$stmt = $pdo->query("SELECT id, nom, email FROM utilisateur WHERE statut = 'Admin'");
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>



<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Paramètres Administrateur</title>
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
    <h2 class="titre-section">Identifiants Admin Principal</h2>

      <div class="profil-admin-card">
        <p><strong>ID :</strong> <?= htmlspecialchars((string)$admin_id) ?></p>
        <p><strong>Email :</strong> <?= htmlspecialchars($adminEmail) ?></p>


        <div class="boutons-profil">
          <a href="modifier-email.php" class="btn-profil">Modifier l'email</a>
          <a href="modifier-mdp.php" class="btn-profil">Modifier le mot de passe</a>
          <a href="../../logout.php" class="btn-profil-deco">Se déconnecter</a>
        </div>

      </div>

      <h2 class="titre-section">Liste des modérateurs</h2>

      <!-- Barre de recherche -->
      <div class="barre-recherche">
        <label for="recherche">Rechercher :</label>
        <input type="text" id="recherche" placeholder="Tapez un mot-clé...">
      </div>

      <table class="table-utilisateurs">
        <thead>
          <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($admins as $a): ?>
          <tr>
            <td><?= htmlspecialchars($a['nom']) ?></td>
            <td><?= htmlspecialchars($a['email']) ?></td>
            <td>
            <a href="retirer-droit-admin.php?id=<?= $a['id'] ?>" class="btn-supprimer" onclick="return confirm('Retirer les droits d\'admin à cet utilisateur ?');">
              Retirer droit admin
            </a>

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
