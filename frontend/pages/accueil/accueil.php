<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
    'ski'           => '../../images/ski.png',
    'patisserie'      => '../../images/patisserie.png',
    'basketball'     => '../../images/basketball.png',
    'Autre'          => '../../images/autre.png'
];

$id_createur = $_SESSION['user_id'] ?? null;

// Traite la création d'une activité
$messageConfirmation = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titre = htmlspecialchars($_POST["titre"] ?? "");
    $description = htmlspecialchars($_POST["description"] ?? "");
    $localisation = htmlspecialchars($_POST["localisation"] ?? "");
    $temps = $_POST["temps"] ?? "";
    if ($temps === "NULL" || $temps === "null") {
        $temps = null;
    }
    $date = $_POST["date"] ?? "";
    $tags = htmlspecialchars($_POST["tags"] ?? "");
    $theme = $_POST["theme"] ?? "Autre";
    $nb_places = intval($_POST["nb_places"] ?? 1);
    $competences = isset($_POST["competence"]) ? $_POST["competence"] : [];
    $heure = $_POST["heure"] ?? "";
    $date_activite = $date . 'T' . $heure . ':00Z';
    if ($date < date('Y-m-d')) {
        $messageConfirmation = "La date doit être supérieure ou égale à la date actuelle.";
    } else {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO activite (titre, description, localisation, nb_places, conditions_req, date_activite, theme, id_createur) VALUES (:titre, :description, :localisation, :nb_places, :conditions_req, :date_activite, :theme, :id_createur)");
            $stmt->execute([
                ':titre' => $titre,
                ':description' => $description,
                ':localisation' => $localisation,
                ':nb_places' => $nb_places,
                ':conditions_req' => $temps,
                ':date_activite' => $date_activite,
                ':theme' => $theme,
                ':id_createur' => $id_createur
            ]);
            $id_activite = $pdo->lastInsertId();
            if (!empty($competences)) {
                $stmt = $pdo->prepare("INSERT INTO necessite (id_act, id_competence) VALUES (:id_act, :id_competence)");
                foreach ($competences as $id_competence) {
                    $stmt->execute([
                        ':id_act' => $id_activite,
                        ':id_competence' => $id_competence
                    ]);
                }
            }
            $pdo->commit();
            $messageConfirmation = "Activité <strong>$titre</strong> créée avec succès !";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $messageConfirmation = "Erreur lors de la création de l'activité : " . $e->getMessage();
        }
    }
}

// Récupère les activités à venir
try {
    $stmt = $pdo->prepare("SELECT id_act, titre, description, theme FROM activite WHERE date_activite >= CURRENT_DATE ORDER BY id_act DESC");
    $stmt->execute();
    $activites = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Erreur lors de la récupération des activités: " . $e->getMessage();
    $activites = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Accueil - Activités Proposées</title>
  <link rel="stylesheet" href="../accueil/styles-accueil.css">
  <link rel="stylesheet" href="../../menu/menu.css">
  <script src="../../menu/menu-script.js"></script>
</head>
<body>
  <?php include_once('../../header/header.php'); ?>
  <div class="main-layout">
    <?php include_once('../../menu/menu.php'); ?>
    <div class="container">
      <main>
        <h1>Accueil</h1>
        <section class="activites">
          <h2>Activités Proposées</h2>
          <div class="card-grid">
          <?php if(!empty($activites)): ?>
            <?php foreach ($activites as $activite) : ?>
            <?php
                $theme = $activite['theme'] ?? 'Autre';
                $imagePath = $themeImages[$theme] ?? '../../images/logoBE.png';
            ?>
            <a href="../activites/activites.php?id=<?= htmlspecialchars($activite['id_act']) ?>">
              <div class="card">
                <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($theme) ?>">
                <h3><?= htmlspecialchars($activite['titre']) ?></h3>
                <p><?= htmlspecialchars($activite['description']) ?></p>
              </div>
            </a>
            <?php endforeach; ?>
          <?php else: ?>
            <p>Aucune activité disponible pour le moment.</p>
          <?php endif; ?>
          </div>
        </section>
        <section class="activite-form">
          <h2>Créer une Activité</h2>
          <?php if (!empty($messageConfirmation)): ?>
            <div class="form-confirmation">
              <?= $messageConfirmation ?>
            </div>
          <?php endif; ?>
          <form action="accueil.php" method="post" class="form-grid">
            <div class="form-group">
              <label for="titre">Titre</label>
              <input type="text" name="titre" id="titre" required>
              <label for="description">Description</label>
              <textarea name="description" id="description" required></textarea>
              <label for="localisation">Localisation</label>
              <input type="text" name="localisation" id="localisation" required>
              <label for="temps">Temps Nécessaire</label>
              <select name="temps" id="temps">
                <option value="Sec">Sec</option>
                <option value="Pluie">Pluie</option>
                <option value="Neige">Neige</option>
                <option value="NULL">Aucune</option>
              </select>
              <label for="date">Date</label>
              <input type="date" name="date" id="date" required min="<?= date('Y-m-d') ?>">
              <label for="heure">Heure</label>
              <input type="time" name="heure" id="heure" required>
              <label for="nb_places">Nombre de places</label>
              <input type="number" name="nb_places" id="nb_places" min="1" max="100" value="10" required>
            </div>
            <div class="form-group">
              <label for="tags">Tags</label>
              <textarea name="tags" id="tags"></textarea>
              <label for="theme">Thème</label>
              <select name="theme" id="theme">
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
              <label for="competence">Compétences requises</label>
              <select name="competence[]" id="competence" multiple size="5">
                <option value="">Sélectionnez d'abord un thème</option>
              </select>
              <small class="form-help">Utilisez la touche Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs compétences.</small>
            </div>
            <div class="form-submit">
              <button type="submit">Créer l’Activité</button>
            </div>
          </form>
        </section>
      </main>
    </div>
  </div>
  <script>
    // Gère le chargement dynamique des compétences selon le thème
    document.addEventListener('DOMContentLoaded', function() {
      const themeSelect = document.getElementById('theme');
      const competenceSelect = document.getElementById('competence');
      function chargerCompetences(theme) {
        competenceSelect.innerHTML = '<option value="">Chargement en cours...</option>';
        fetch(`../../../backend/get_competences.php?categorie=${theme}`)
          .then(response => {
            if (!response.ok) {
              throw new Error('Erreur réseau');
            }
            return response.json();
          })
          .then(data => {
            competenceSelect.innerHTML = '';
            if (data.length === 0) {
              competenceSelect.innerHTML = '<option value="">Aucune compétence disponible pour ce thème</option>';
              return;
            }
            data.forEach(competence => {
              const option = document.createElement('option');
              option.value = competence.id_competence;
              option.textContent = competence.nom;
              competenceSelect.appendChild(option);
            });
          })
          .catch(error => {
            console.error('Erreur:', error);
            competenceSelect.innerHTML = '<option value="">Erreur de chargement</option>';
          });
      }
      themeSelect.addEventListener('change', function() {
        chargerCompetences(this.value);
      });
      chargerCompetences(themeSelect.value);
    });
  </script>
  <?php include_once('../../footer/footer.php'); ?>
</body>
</html>