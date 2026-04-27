<?php 
session_start();
include '../backend/fonctions.php';

// Gère le consentement cookies
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['accept_cookies'])) {
    setcookie('cookies_accepted', 'yes', time() + 365 * 24 * 60 * 60, '/');
    header("Refresh:0");
    exit;
  } elseif (isset($_POST['refuse_cookies'])) {
    setcookie('cookies_accepted', 'no', time() + 365 * 24 * 60 * 60, '/');
    header("Refresh:0");
    exit;
  }
}

// Gère le token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Redirige si déjà connecté
if (isset($_SESSION['user_id'])) {
    header('Location: ../accueil/accueil.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Authentification – SolidariSkills</title>
  <link rel="stylesheet" href="styles-auth.css">
  <link rel="stylesheet" href="/frontend/cookies/cookies-style.css"> 
</head>
<body>
  <?php include '../cookies/cookies_banner.php'; ?> 
  <div class="main-layout">
    <div class="container">
      <main>
        <div class="auth-card card">
          <form id="loginForm" class="form-container active" onsubmit="handleLogin(event)">
            <input type="hidden" name="csrf_token" id="loginCsrf"
                   value="<?= htmlspecialchars($csrf_token); ?>">
            <div class="form-group">
              <label>Email</label>
              <input type="email" name="email" required>
            </div>
            <div class="form-group">
              <label>Mot de passe</label>
              <input type="password" name="password" required>
            </div>
            <div class="form-submit">
              <button type="submit">Se connecter</button>
            </div>
            <p class="terms-note">
              En continuant, vous acceptez nos
              <a href="/frontend/pages/conditions.php"      target="_blank">Conditions générales d’utilisation</a>
              et notre
              <a href="/frontend/pages/confidentialite.php" target="_blank">Politique de confidentialité</a>.
            </p>
            <div id="loginMessage" class="message"></div>
          </form>
          <form id="signupForm" class="form-container" onsubmit="handleSignup(event)">
            <input type="hidden" name="csrf_token" id="signupCsrf"
                   value="<?= htmlspecialchars($csrf_token); ?>">
            <div class="form-group">
              <label>Nom d'utilisateur</label>
              <input type="text" name="username" required>
            </div>
            <div class="form-group">
              <label>Email</label>
              <input type="email" name="email" required>
            </div>
            <div class="form-group">
              <label>Mot de passe</label>
              <input type="password" name="password" required>
              <div class="password-conditions">
                <small>Conditions requises :</small>
                <ul>
                  <li>Minimum 12 caractères</li>
                  <li>1 majuscule et 1 minuscule</li>
                  <li>1 chiffre</li>
                  <li>1 caractère spécial (@$!%*?&amp;)</li>
                </ul>
              </div>
            </div>
            <div class="form-group">
              <label>Confirmer le mot de passe</label>
              <input type="password" name="confirm_password" required>
            </div>
            <div class="form-submit">
              <button type="submit">Créer un compte</button>
            </div>
            <div id="message" class="message"></div>
          </form>
        </div>
      </main>
    </div>
  </div>
  <script>
    // Gère l'affichage des formulaires et la soumission AJAX
    function showForm(name){
      const login  = document.getElementById('loginForm');
      const signup = document.getElementById('signupForm');
      if (name === 'login'){
        login.classList.add('active');
        signup.classList.remove('active');
      } else {
        signup.classList.add('active');
        login.classList.remove('active');
      }
    }
    function flash(id, text, isErr){
      const el = document.getElementById(id);
      el.textContent   = text;
      el.className     = 'message ' + (isErr ? 'error' : 'success');
      el.style.display = 'block';
      setTimeout(() => { el.style.display = 'none'; }, 5000);
    }
    async function handleLogin(e){
      e.preventDefault();
      const data = new FormData(e.target);
      try{
        const res = await fetch('/backend/auth/connexion.php', {
          method:'POST',
          body:data
        }).then(r => r.json());
        if (res.statut === 'succes'){
          location.href = res.data.redirect;
        } else {
          flash('loginMessage', res.data, true);
        }
      } catch {
        flash('loginMessage', 'Erreur de connexion au serveur', true);
      }
    }
    async function handleSignup(e){
      e.preventDefault();
      const data = new FormData(e.target);
      if (data.get('password') !== data.get('confirm_password')){
        return flash('message','Les mots de passe ne correspondent pas', true);
      }
      try{
        const res = await fetch('/backend/auth/inscription.php', {
          method:'POST',
          body:data
        }).then(r => r.json());
        if (res.statut === 'succes'){
          flash('message', res.data.message, false);
          showForm('login');
        } else {
          flash('message', res.data, true);
        }
      } catch {
        flash('message', "Erreur lors de l'inscription", true);
      }
    }
  </script>
  <?php include '../../footer/footer.php'; ?>
</body>
</html>
