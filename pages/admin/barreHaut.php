<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header class="en-tete-principal">

<!-- CSS personnalisé -->
<link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style-global-admin.css">

<!-- Icônes -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


  <div class="titre-page">
    <?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Admin' ?>
  </div>
  <div class="profil-droite">  
    <div class="profil-entete">
      <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'): ?>
      <span class="bienvenue">Bienvenue Admin</span>
      <?php endif; ?>
      <div class="nav-icons">
        <a href="../para-admin/para-admin.php" class="header-icon" title="Paramètres Admin">
          <i class="fas fa-user-circle"></i>
        </a>
        <a href="../../logout.php" class="header-icon" title="Déconnexion">
          <i class="fas fa-sign-out-alt"></i>
        </a>
      </div>
    </div>
  </div>
</header>

