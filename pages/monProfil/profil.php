<?php
session_start();
require_once('../../../backend/config.php');

// Récupère les infos de l'utilisateur connecté
$id_user = $_SESSION['user_id'] ?? 1;

// Infos principales
$stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id = :id");
$stmt->execute([':id' => $id_user]);
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$utilisateur) die("Utilisateur non trouvé.");

// Compétences (jointure gerer_cpu + competence)
$stmt = $pdo->prepare("SELECT c.nom FROM competence c INNER JOIN gerer_cpu g ON c.id_competence = g.id_competence WHERE g.id = :id");
$stmt->execute([':id' => $id_user]);
$competences = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Badges (jointure attribuer + badge)
$stmt = $pdo->prepare("SELECT b.nom_badge FROM badge b INNER JOIN attribuer a ON b.id_badge = a.id_badge WHERE a.id = :id");
$stmt->execute([':id' => $id_user]);
$badges = $stmt->fetchAll(PDO::FETCH_COLUMN);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mon profil - Aperçu Utilisateur PDP</title>
  <link rel="stylesheet" href="styles-profil-pdp.css">
  <link rel="stylesheet" href="../../menu/menu.css">
  <script src="../../menu/menu-script.js"></script>
</head>
<body>
  <?php include_once('../../header/header.php'); ?>
  <div class="main-layout">
    <?php include_once('../../menu/menu.php'); ?>
    <div class="container">
      <main>
        <h1>Mon profil</h1>
        <div class="profile-card">
          <div class="profile-header"></div>
          <div class="profile-content">
            <div class="profile-left">
              <img class="profile-avatar" src="<?= htmlspecialchars($utilisateur['imagepdp'] ?? '/frontend/images/photoProfil/default.png') ?>" alt="Avatar">
              <h2><?= htmlspecialchars($utilisateur['nom']) ?></h2>
              <p class="profile-desc"><?= htmlspecialchars($utilisateur['statut']) ?></p>
              <div class="profile-infos">
                <div>
                  <span class="icon">📍</span>
                  <?= htmlspecialchars($utilisateur['ville']) ?> (<?= htmlspecialchars($utilisateur['code_postal']) ?>)
                </div>
                <div>
                  <span class="icon">@</span>
                  <?= htmlspecialchars($utilisateur['email']) ?>
                </div>
                <p class="profile-bio"><?= nl2br(htmlspecialchars($utilisateur['description'] ?? '')) ?></p>
              </div>
              <div class="profile-buttons">
                <a href="../contact/contact.php" class="btn-contact">Contact</a>
                <a href="../mesAvis/mesAvis.php" class="btn-avis">Mes Avis</a>
              </div>
            </div>
            <div class="profile-right">
              <div class="profile-section">
                <h3>Mes compétences</h3>
                <div class="tags">
                  <?php foreach($competences as $comp): ?>
                    <span class="tag"><?= htmlspecialchars($comp) ?></span>
                  <?php endforeach; ?>
                </div>
              </div>
              <div class="profile-section">
                <h3>Mes badges</h3>
                <div class="tags">
                  <?php foreach($badges as $badge): ?>
                    <span class="tag"><?= htmlspecialchars($badge) ?></span>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>
          <a href="modifierProfil.php" class="btn-edit">Modifier profil</a>
        </div>
      </main>
    </div>
  </div>
  <script>
    // Gère l'état du menu latéral
    document.addEventListener('DOMContentLoaded', function() {
      const sidebar = document.querySelector('.sidebar');
      const container = document.querySelector('.container');
      const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
      if (isCollapsed) {
        sidebar.classList.add('collapsed');
        container.style.marginLeft = '0';
      }
    });
  </script>
  <?php include_once('../../footer/footer.php'); ?>
</body>
</html>