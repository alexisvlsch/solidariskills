<?php
require_once('../../../backend/config.php');
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);

$themeImages = [
    'jardinage'      => '../../images/jardinage.png',
    'programmation'  => '../../images/programmation.png',
    'poterie'        => '../../images/poterie.png',
    'sport'          => '../../images/sport.png',
    'cuisine'        => '../../images/cuisine.png',
    'musique'        => '../../images/musique.png',
    'photo'          => '../../images/photo.png',
    'bricolage'      => '../../images/bricolage.png',
    'lecture'        => '../../images/lecture.png',
    'écriture'       => '../../images/ecriture.png',
    'Autre'          => '../../images/autre.png',
    'patisserie'     => '../../images/patisserie.png',
    'basketball'     => '../../images/basketball.png',
    'ski'          => '../../images/ski.png'
];

// Récupère l'activité demandée
$id_activite = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_activite <= 0) {
    echo "<div class='container'><p>ID d'activité invalide.</p>";
    echo "<a href='../accueil/accueil.php' class='back-link'>Retour à l'accueil</a></div>";
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT a.*, a.theme, m.condition_meteo, m.temperature_meteo,
                          u.nom AS createur_nom, u.imagepdp AS createur_avatar,
                          u.id AS createur_id
                          FROM activite a 
                          LEFT JOIN meteo m ON a.loc_meteo = m.loc_meteo AND a.date_meteo = m.date_meteo
                          LEFT JOIN utilisateur u ON a.id_createur = u.id
                          WHERE a.id_act = :id");
    $stmt->bindParam(':id', $id_activite, PDO::PARAM_INT);
    $stmt->execute();
    $activite = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$activite) {
        echo "<div class='container'><p>Activité non trouvée.</p>";
        echo "<a href='../accueil/accueil.php' class='back-link'>Retour à l'accueil</a></div>";
        exit();
    }

    $stmt_comp = $pdo->prepare("SELECT c.nom, c.niveau FROM competence c
                               JOIN necessite n ON c.id_competence = n.id_competence
                               WHERE n.id_act = :id");
    $stmt_comp->bindParam(':id', $id_activite, PDO::PARAM_INT);
    $stmt_comp->execute();
    $competences = $stmt_comp->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo "<div class='container'><p>Erreur lors de la récupération des détails: " . $e->getMessage() . "</p>";
    echo "<a href='../accueil/accueil.php' class='back-link'>Retour à l'accueil</a></div>";
    exit();
}

// Récupère la note moyenne du créateur
$note_moyenne = null;
$nb_avis = 0;
if (isset($activite['createur_id'])) {
    try {
        $stmt_note = $pdo->prepare("
            SELECT AVG(f.note_fb) AS note_moyenne, COUNT(f.note_fb) AS nombre_avis
            FROM feedback f
            JOIN activite a ON f.id_act = a.id_act
            WHERE a.id_createur = :id_createur
        ");
        $stmt_note->bindParam(':id_createur', $activite['createur_id'], PDO::PARAM_INT);
        $stmt_note->execute();
        $result_note = $stmt_note->fetch(PDO::FETCH_ASSOC);
        if ($result_note && $result_note['nombre_avis'] > 0) {
            $note_moyenne = round($result_note['note_moyenne'], 1);
            $nb_avis = $result_note['nombre_avis'];
        }
    } catch(PDOException $e) {}
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM participer WHERE id_act = :id");
$stmt->execute([':id' => $id_activite]);
$nb_inscrits = $stmt->fetchColumn();
$places_restantes = $activite['nb_places'] - $nb_inscrits;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Détails de l'Activité</title>
  <link rel="stylesheet" href="../accueil/styles-accueil.css">
  <link rel="stylesheet" href="styles-activites.css">
  <link rel="stylesheet" href="../../menu/menu.css">
  <script src="../../menu/menu-script.js"></script>
</head>
<body>
  <?php include_once('../../header/header.php'); ?>
  <div class="main-layout">
    <?php include_once('../../menu/menu.php'); ?>
    <div class="container">
      <h1>Détails de l'activité</h1>
        <div class="activity-details">
           <a href="../accueil/accueil.php" class="back-link">Retour à l'accueil</a>
           <?php if (isset($activite['createur_id'])): ?>
           <div class="creator-info">
               <a href="../mesAvis/profil_utilisateur.php?id=<?= $activite['createur_id'] ?>" class="creator-link">
                   <div class="creator-avatar">
                       <img src="<?= !empty($activite['createur_avatar']) ? htmlspecialchars($activite['createur_avatar']) : '../../images/default-avatar.png' ?>" alt="Avatar du créateur">
                   </div>
                   <div class="creator-details">
                       <span class="creator-name"><?= htmlspecialchars($activite['createur_nom']) ?></span>
                       <?php if ($note_moyenne !== null): ?>
                           <div class="creator-rating">
                               <div class="stars">
                                   <?php for($i = 1; $i <= 5; $i++): ?>
                                       <?php if ($i <= $note_moyenne): ?>
                                           <span class="star filled">★</span>
                                       <?php else: ?>
                                           <span class="star">☆</span>
                                       <?php endif; ?>
                                   <?php endfor; ?>
                               </div>
                               <span class="rating-value"><?= $note_moyenne ?>/5 (<?= $nb_avis ?> avis)</span>
                           </div>
                       <?php else: ?>
                           <div class="creator-rating">
                               <span class="no-rating">Pas encore d'avis</span>
                           </div>
                       <?php endif; ?>
                   </div>
               </a>
           </div>
           <?php endif; ?>
          <div class="activity-header">
            <div class="activity-image">
              <?php
                $theme = $activite['theme'] ?? 'Autre';
                $imagePath = $themeImages[$theme] ?? '../../images/logoBE.png';
              ?>
              <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($theme) ?>">
            </div>
            <div class="activity-info">
              <h1><?= htmlspecialchars($activite['titre']) ?></h1>
              <p><?= htmlspecialchars($activite['description']) ?></p>
            </div>
          </div>
          <div class="activity-meta">
            <div class="meta-item">
              <span class="meta-label">Localisation:</span>
              <?= htmlspecialchars($activite['localisation']) ?>
            </div>
            <div class="meta-item">
              <span class="meta-label">Date de l'activité :</span>
              <?= htmlspecialchars(date('d/m/Y', strtotime($activite['date_activite']))) ?>
            </div>
            <div class="meta-item">
              <span class="meta-label">Heure de début :</span>
              <?= htmlspecialchars(date('H:i', strtotime($activite['date_activite']))) ?>
            </div>
            <div class="meta-item">
              <span class="meta-label">Places restantes :</span>
              <?= max(0, $places_restantes) ?>/<?= htmlspecialchars($activite['nb_places']) ?>
            </div>
            <div class="meta-item">
              <span class="meta-label">Conditions requises:</span>
              <?= $activite['conditions_req'] == 'NULL' ? 'Aucune' : htmlspecialchars($activite['conditions_req']) ?>
            </div>
          </div>
          <?php if (!empty($competences)): ?>
          <div class="competences-section">
            <h2>Compétences requises</h2>
            <ul>
              <?php foreach ($competences as $competence): ?>
              <li>
                <?= htmlspecialchars($competence['nom']) ?> 
                (niveau <?= htmlspecialchars($competence['niveau']) ?>)
              </li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php endif; ?>
          <div class="actions">
            <h2>Actions</h2>
            <form action="/backend/activite/participer.php" method="post">
              <input type="hidden" name="id_activite" value="<?= $id_activite ?>">
              <button type="submit" class="btn-primary" <?= ($places_restantes <= 0) ? 'disabled' : '' ?>>Participer à cette activité</button>
            </form>
          </div>
        </div>
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
<?php if (isset($_GET['success'])): ?>
  <div class="popup-message" id="popup-confirm">
    <div class="popup-message-content">
      <span class="success">Participation enregistrée avec succès !</span>
      <br>
      <button class="popup-message-btn" onclick="document.getElementById('popup-confirm').style.display='none'">Fermer</button>
    </div>
  </div>
<?php elseif (isset($_GET['error'])): ?>
  <div class="popup-message" id="popup-confirm">
    <div class="popup-message-content">
      <span class="error">Erreur lors de la participation.</span>
      <br>
      <button class="popup-message-btn" onclick="document.getElementById('popup-confirm').style.display='none'">Fermer</button>
    </div>
  </div>
<?php elseif (isset($_GET['info'])): ?>
  <div class="popup-message" id="popup-confirm">
    <div class="popup-message-content">
      <span class="info">Vous êtes déjà inscrit à cette activité.</span>
      <br>
      <button class="popup-message-btn" onclick="document.getElementById('popup-confirm').style.display='none'">Fermer</button>
    </div>
  </div>
<?php endif; ?>
</html>