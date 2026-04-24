<?php
// Détermine quelle page est actuellement active
$currentPage = $_SERVER['PHP_SELF'] ?? '';
?>
<aside class="sidebar">
  <nav>
    <ul>
      <li><a href="/frontend/pages/accueil/accueil.php" class="<?= strpos($currentPage, 'accueil.php') !== false ? 'active' : '' ?>">Accueil</a></li>
      <li><a href="/frontend/pages/monProfil/profil.php" class="<?= strpos($currentPage, 'profil.php') !== false ? 'active' : '' ?>">Mon Profil</a></li>
      <li><a href="/frontend/pages/mesActivites/mesActivites.php" class="<?= strpos($currentPage, 'mesActivites.php') !== false ? 'active' : '' ?>">Mes Activités</a></li>
      <li><a href="/frontend/pages/mesBadges/badges.php" class="<?= strpos($currentPage, 'badges.php') !== false ? 'active' : '' ?>">Mes Badges</a></li>
      <li><a href="/frontend/pages/mesAvis/mesAvis.php" class="<?= strpos($currentPage, 'mesAvis.php') !== false ? 'active' : '' ?>">Mes Avis</a></li>
      <li><a href="/frontend/pages/contact/contact.php" class="<?= strpos($currentPage, 'contact.php') !== false ? 'active' : '' ?>">Contact</a></li>
      <li><a href="/frontend/pages/messagerie/messagerie.php" class="<?= strpos($currentPage, 'messagerie.php') !== false ? 'active' : '' ?>">Messagerie</a></li>
    </ul>
  </nav>
</aside>
<button class="toggle-sidebar" onclick="toggleSidebar()" title="Réduire/Étendre">&#9776;</button>

<script>
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