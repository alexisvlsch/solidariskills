<aside class="barre-laterale">
  <div class="logo">
  
  <a href="../dashboard/dashboard.php"><img src="../../../images/logoBE.png" alt="Logo" ></a>
  </div>
  <nav class="navigation-laterale">
    <!-- pour gadrer la même button de la barre latérale active et même dans les sous-pages -->
    <?php $page = basename($_SERVER['PHP_SELF']); ?>
    <a href="../dashboard/dashboard.php" class="<?= $page === 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
    <a href="../utilisateurs/utilisateurs.php" class="<?= in_array($page, ['utilisateurs.php', 'modifier-utilisateur.php']) ? 'active' : '' ?>">Utilisateurs</a>
    <a href="../activites/activites.php" class="<?= in_array($page, ['activites.php', 'modifier-activite.php', 'participants.php']) ? 'active' : '' ?>">Activités</a>
    <a href="../signalements-feedbacks/feedbacks.php" class="<?= in_array($page, ['feedbacks.php', 'details-feedback.php']) ? 'active' : '' ?>">Signalements & Feedbacks</a>
    <a href="../badges/badges.php" class="<?= in_array($page, ['badges.php', 'modifier-badge.php', 'badge-utilisateur.php', 'ajouter-badge.php']) ? 'active' : '' ?>">Badges</a>
    <a href="../para-admin/para-admin.php" class="<?= in_array($page, ['para-admin.php', 'modifier-mdp.php', 'modifier-email.php']) ? 'active' : '' ?>">Paramètres ADMIN</a>

  </nav>
</aside>
