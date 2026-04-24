<?php
session_start();
require_once('../../../backend/config.php');

// Gère la modification du profil utilisateur
$id_user = $_SESSION['user_id'] ?? 1;

$success = false;
$password_message = '';
$password_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gère la modification du mot de passe
    if (isset($_POST['new_password'], $_POST['confirm_password'])) {
        $new = $_POST['new_password'];
        $confirm = $_POST['confirm_password'];
        $current_password = $_POST['current_password'];
        $stmt = $pdo->prepare("SELECT password_hash FROM utilisateur WHERE id = :id");
        $stmt->execute([':id' => $id_user]);
        $hash_actuel = $stmt->fetchColumn();
        if (!password_verify($current_password, $hash_actuel)) {
            $password_message = "Le mot de passe actuel est incorrect.";
        } elseif (strlen($new) < 12) {
            $password_message = "Le mot de passe doit contenir au moins 12 caractères.";
        } elseif (strlen($confirm) < 12) {
            $password_message = "La confirmation du mot de passe doit contenir au moins 12 caractères.";
        } elseif (preg_match('/\s/', $new)) {
            $password_message = "Le mot de passe ne doit pas contenir d'espaces.";
        } elseif (preg_match('/\s/', $confirm)) {
            $password_message = "La confirmation du mot de passe ne doit pas contenir d'espaces.";
        } elseif ($new === $current_password) {
            $password_message = "Le nouveau mot de passe doit être différent de l'ancien.";
        } elseif ($new === $confirm && password_verify($new, $hash_actuel)) {
            $password_message = "Le nouveau mot de passe doit être différent de l'ancien.";
        } elseif (empty($new) || empty($confirm)) {
            $password_message = "Veuillez remplir tous les champs.";
        } elseif (empty($current_password)) {
            $password_message = "Veuillez entrer votre mot de passe actuel.";
        } elseif (empty($new)) {
            $password_message = "Veuillez entrer un nouveau mot de passe.";
        } elseif (empty($confirm)) {
            $password_message = "Veuillez confirmer le nouveau mot de passe.";
        } elseif (empty($current_password)) {
            $password_message = "Veuillez entrer votre mot de passe actuel.";
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{12,}$/', $new)) {
            $password_message = "Le mot de passe ne respecte pas les conditions de sécurité.";
        } else {
            $hash = password_hash($new, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt = $pdo->prepare("UPDATE utilisateur SET password_hash = :hash WHERE id = :id");
            $stmt->execute([':hash' => $hash, ':id' => $id_user]);
            $password_message = "Mot de passe mis à jour avec succès.";
            $password_success = true;
        }
    } else {
        // Gère la modification des autres informations du profil
        $nom = $_POST['nom'] ?? '';
        $email = $_POST['email'] ?? '';
        $ville = $_POST['ville'] ?? '';
        $code_postal = $_POST['code_postal'] ?? '';
        $num_rue = $_POST['num_rue'] ?? '';
        $adresse = $_POST['adresse'] ?? '';
        $statut = $_POST['statut'] ?? '';
        $description = $_POST['description'] ?? '';
        $imagepdp = $_POST['imagepdp'] ?? '';
        $stmt = $pdo->prepare("UPDATE utilisateur SET nom = :nom, email = :email, ville = :ville, code_postal = :code_postal, num_rue = :num_rue, adresse = :adresse, statut = :statut, description = :description, imagepdp = :imagepdp WHERE id = :id");
        $stmt->execute([
            ':nom' => $nom,
            ':email' => $email,
            ':ville' => $ville,
            ':code_postal' => $code_postal,
            ':num_rue' => $num_rue,
            ':adresse' => $adresse,
            ':statut' => $statut,
            ':description' => $description,
            ':imagepdp' => $imagepdp,
            ':id' => $id_user
        ]);
        $success = true;
    }
}

// INFOS À JOUR
$stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id = :id");
$stmt->execute([':id' => $id_user]);
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$utilisateur) die("Utilisateur non trouvé.");

// Compétences (jointure gerer_cpu + competence)
$stmt = $pdo->prepare("SELECT c.nom FROM competence c INNER JOIN gerer_cpu g ON c.id_competence = g.id_competence WHERE g.id = :id");
$stmt->execute([':id' => $id_user]);
$competences = $stmt->fetchAll(PDO::FETCH_COLUMN);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Modifier mon profil</title>
  <link rel="stylesheet" href="styles-profil-pdp.css">
  <link rel="stylesheet" href="../../menu/menu.css">
  <script src="../../menu/menu-script.js"></script>
  <style>
    .popup { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.3); display: none; align-items: center; justify-content: center; z-index: 9999; }
    .popup-content { background: #fff; border-radius: 12px; padding: 32px 24px; min-width: 320px; max-width: 90vw; box-shadow: 0 2px 12px rgba(0,0,0,0.12); }
    .edit-icon { cursor: pointer; margin-left: 8px; color: #006666; font-size: 1.2em; vertical-align: middle; }
    .password-field { display: flex; align-items: center; }
    .popup .form-group { margin-bottom: 16px; }
    .btn-save { background: #006666; color: #fff; border: none; padding: 10px 24px; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    .btn-save:hover { background: #13494e; }
  </style>
</head>
<body>
  <?php include_once('../../header/header.php'); ?>
  <div class="main-layout">
    <?php include_once('../../menu/menu.php'); ?>
    <div class="container">
      <main>
        <h1>Modifier mon profil</h1>
        <?php if (!empty($success)): ?>
          <div style="color:green;margin-bottom:16px;">Profil mis à jour avec succès !</div>
        <?php endif; ?>
        <form class="profile-form" method="post" action="">
          <div class="profile-form-grid">
            <div class="profile-form-col">
              <a href="../monProfil/profil.php" class="back-link">Retour à mon profil</a>
              <div class="profile-avatar-edit">
                <img id="current-avatar" src="<?= !empty($utilisateur['imagepdp']) ? htmlspecialchars($utilisateur['imagepdp']) : '/frontend/images/photoProfil/default.png' ?>" alt="Avatar">
                <span class="edit-icon" onclick="openPopup()">✎</span>
              </div>
              <h3 class="mt-4 mb-3">Mes informations personnelles</h3>
              <div class="form-group">
                <label>Nom</label>
                <input type="text" name="nom" value="<?= htmlspecialchars($utilisateur['nom']) ?>">
              </div>
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($utilisateur['email']) ?>">
              </div>
              <div class="form-group">
                <label>Ville</label>
                <input type="text" name="ville" value="<?= htmlspecialchars($utilisateur['ville']) ?>">
              </div>
              <div class="form-group">
                <label>Code postal</label>
                <input type="text" name="code_postal" value="<?= htmlspecialchars($utilisateur['code_postal']) ?>">
              </div>
              <div class="form-group">
                <label>Numéro de rue</label>
                <input type="text" name="num_rue" value="<?= htmlspecialchars($utilisateur['num_rue']) ?>">
              </div>
              <div class="form-group">
                <label>Adresse</label>
                <input type="text" name="adresse" value="<?= htmlspecialchars($utilisateur['adresse']) ?>">
              </div>
              <div class="form-group">
                <label>Mot de passe</label>
                <div class="password-field">
                  <input type="password" name="mot_de_passe" value="************" readonly>
                  <span class="edit-icon" onclick="openPasswordPopup()">✎</span>
                </div>
              </div>
            </div>
            <div class="profile-form-col">
              <div class="form-group">
                <label>Statut</label>
                <input type="text" name="statut" value="<?= htmlspecialchars($utilisateur['statut']) ?>" readonly>
              </div>
              <div class="form-group">
                <label>Mes compétences</label>
                <div class="competences-field">
                  <input type="text" id="competences-display" value="<?= htmlspecialchars(implode(', ', $competences)) ?>" readonly>
                  <span class="edit-icon" onclick="openCompetencesPopup()">✎</span>
                </div>
              </div>
              <div class="form-group">
                <label for="description">Bio :</label>
                <textarea name="description" id="description" class="form-control" rows="4" placeholder="Parlez un peu de vous..."><?= htmlspecialchars($utilisateur['description'] ?? '') ?></textarea>
              </div>
              <button type="submit" class="btn-save">Enregistrer</button>
            </div>
          </div>
          <input type="hidden" name="imagepdp" id="imagepdp" value="<?= htmlspecialchars($utilisateur['imagepdp'] ?? '') ?>">
        </form>
      </main>
    </div>
  </div>
  <div id="avatarPopup" class="popup" style="display:none;">
    <div class="popup-content">
      <h3>Choisir une photo de profil</h3>
      <div class="avatar-list">
        <?php
          $images = glob($_SERVER['DOCUMENT_ROOT'] . '/frontend/images/photoProfil/*.png');
          foreach ($images as $img):
            $webPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath($img));
        ?>
          <img src="<?= $webPath ?>" onclick="selectAvatar('<?= $webPath ?>')">
        <?php endforeach; ?>
      </div>
      <button onclick="closePopup()">Fermer</button>
    </div>
  </div>
  <div id="passwordPopup" class="popup" style="display:none;">
    <div class="popup-content">
      <h3>Changer mon mot de passe</h3>
      <form method="post" action="" id="passwordForm">
        <div class="form-group">
          <label for="current_password">Mot de passe actuel</label>
          <input type="password" name="current_password" id="current_password" required>
        </div>
        <div class="form-group">
          <label for="new_password">Nouveau mot de passe</label>
          <input type="password" name="new_password" id="new_password" required minlength="12">
        </div>
        <div class="form-group">
          <label for="confirm_password">Confirmer le mot de passe</label>
          <input type="password" name="confirm_password" id="confirm_password" required minlength="12">
        </div>
        <small>
          Conditions requises :
          <ul>
            <li>Minimum 12 caractères</li>
            <li>1 majuscule et 1 minuscule</li>
            <li>1 chiffre</li>
            <li>1 caractère spécial (@$!%*?&)</li>
          </ul>
        </small>
        <div style="margin-top:16px;">
          <button type="submit" class="btn-save">Enregistrer</button>
          <button type="button" onclick="closePasswordPopup()">Annuler</button>
        </div>
        <?php if (!empty($password_message)): ?>
          <div style="color:<?= $password_success ? 'green' : 'red' ?>;margin-top:10px;">
            <?= htmlspecialchars($password_message) ?>
          </div>
        <?php endif; ?>
      </form>
    </div>
  </div>
  <div id="competencesPopup" class="popup" style="display:none;">
    <div class="popup-content competences-popup">
      <h3>Gérer mes compétences</h3>
      <div class="competences-container">
        <div class="theme-selector">
          <label for="theme-select">Catégorie:</label>
          <select id="theme-select">
            <option value="jardinage">Jardinage</option>
            <option value="programmation">Programmation</option>
            <option value="poterie">Poterie</option>
            <option value="sport">Sport</option>
            <option value="cuisine">Cuisine</option>
            <option value="musique">Musique</option>
            <option value="photo">Photographie</option>
            <option value="bricolage">Bricolage</option>
            <option value="lecture">Lecture</option>
            <option value="écriture">Écriture</option>
            <option value="ski">Ski</option>
            <option value="Autre">Autre</option>
          </select>
        </div>
        <div class="competences-list" id="competences-list">
          <p>Sélectionnez une catégorie pour voir les compétences disponibles</p>
        </div>
      </div>
      <div class="selected-competences">
        <h4>Compétences sélectionnées:</h4>
        <div id="selected-competences-container"></div>
      </div>
      <div class="popup-actions">
        <button type="button" class="btn-save" onclick="saveCompetences()">Enregistrer</button>
        <button type="button" onclick="closeCompetencesPopup()">Annuler</button>
      </div>
    </div>
  </div>
  <script>
    // Fonctions pour gérer les popups et l'état du menu latéral
    function openPopup() {
      document.getElementById('avatarPopup').style.display = 'flex';
    }
    function closePopup() {
      document.getElementById('avatarPopup').style.display = 'none';
    }
    function selectAvatar(path) {
      document.getElementById('current-avatar').src = path;
      document.getElementById('imagepdp').value = path;
      closePopup();
    }
    function openPasswordPopup() {
      document.getElementById('passwordPopup').style.display = 'flex';
    }
    function closePasswordPopup() {
      document.getElementById('passwordPopup').style.display = 'none';
    }
    function openCompetencesPopup() {
      document.getElementById('competencesPopup').style.display = 'flex';
    }
    function closeCompetencesPopup() {
      document.getElementById('competencesPopup').style.display = 'none';
    }
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
  <script>
    let initialUserCompetences = <?= json_encode($competences) ?>;
  </script>
  <script src="js/menuCompetences.js"></script>
  <?php include_once('../../footer/footer.php'); ?>
</body>
</html>