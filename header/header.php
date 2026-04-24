<?php
// Traite le consentement des cookies et définit le cookie approprié
if (isset($_GET['cookie_consent'])) {
    $choice = $_GET['cookie_consent'];
    if ($choice === 'accept' || $choice === 'refuse') {
        setcookie('cookie_consent', $choice, time() + 365*24*3600, '/');
        $url = strtok($_SERVER['REQUEST_URI'], '?');
        header("Location: $url");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/frontend/header/header.css">
  <title>BeActivity</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<header>
  <div class="logo">
    <a href="/frontend/pages/accueil/accueil.php">
      <img src="/frontend/images/logoBE.png" alt="Logo">
    </a>
  </div>
  <div class="nav-icons">
    
    <a href="/frontend/pages/monProfil/profil.php" class="header-icon" title="Mon Profil">
      <i class="fas fa-user-circle"></i>
    </a>
    
    <a href="/frontend/pages/logout.php" class="header-icon" title="Déconnexion">
      <i class="fas fa-sign-out-alt"></i>
    </a>
  </div>
</header>

<script>
  document.getElementById('profileIcon').addEventListener('click', function(event) {
    event.preventDefault();
    document.getElementById('profileMenu').classList.toggle('show');
    console.log('Menu toggled');
  });
  
  window.addEventListener('click', function(event) {
    if (!event.target.matches('#profileIcon')) {
      var dropdown = document.getElementById('profileMenu');
      if (dropdown.classList.contains('show')) {
        dropdown.classList.remove('show');
      }
    }
  });
</script>
</body>
</html>