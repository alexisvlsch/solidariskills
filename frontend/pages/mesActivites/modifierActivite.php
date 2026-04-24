<?php
// filepath: /var/www/html/frontend/pages/mesActivites/modifierActivite.php

session_start();
require_once('../../../backend/config.php');

// Gère la modification d'une activité par le créateur
if (!isset($_SESSION['user_id'])) {
    echo "Erreur : utilisateur non connecté.";
    exit;
}

$id_user = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id_act'])) {
    $id_act = intval($_GET['id_act']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_act'])) {
    $id_act = intval($_POST['id_act']);
} else {
    echo "Erreur : aucun ID d'activité fourni.";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM activite WHERE id_act = :id_act AND id_createur = :id_user");
$stmt->execute([':id_act' => $id_act, ':id_user' => $id_user]);
$activite = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$activite) {
    echo "Erreur : activité introuvable ou vous n'avez pas les droits pour la modifier.";
    exit;
}

// Récupérer les compétences associées à l'activité
$stmt = $pdo->prepare("
    SELECT n.id_competence 
    FROM necessite n 
    WHERE n.id_act = :id_act
");
$stmt->execute([':id_act' => $id_act]);
$competences_requises = $stmt->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $localisation = trim($_POST['localisation'] ?? '');
    $conditions_req = trim($_POST['conditions_req'] ?? '');
    $date = $_POST['date'] ?? null;
    $nb_places = intval($_POST['nb_places'] ?? 0);
    $theme = trim($_POST['theme'] ?? '');
    $competences = isset($_POST['competence']) ? $_POST['competence'] : [];
    $heure = $_POST['heure'] ?? null;
    $date_activite = $date . ' ' . $heure;

    if (empty($titre) || empty($description) || empty($localisation) || empty($conditions_req) || empty($date_activite) || $nb_places <= 0 || empty($theme)) {
        $message = "Tous les champs sont obligatoires.";
    } else {
        try {
            $pdo->beginTransaction();
            
            // Mise à jour de l'activité
            $stmt = $pdo->prepare("UPDATE activite 
                SET titre = :titre, 
                    description = :description, 
                    localisation = :localisation, 
                    conditions_req = :conditions_req, 
                    date_activite = :date_activite, 
                    nb_places = :nb_places, 
                    theme = :theme 
                WHERE id_act = :id_act AND id_createur = :id_user");
            $stmt->execute([
                ':titre' => $titre,
                ':description' => $description,
                ':localisation' => $localisation,
                ':conditions_req' => $conditions_req,
                ':date_activite' => $date_activite,
                ':nb_places' => $nb_places,
                ':theme' => $theme,
                ':id_act' => $id_act,
                ':id_user' => $id_user
            ]);
            $stmt = $pdo->prepare("DELETE FROM necessite WHERE id_act = :id_act");
            $stmt->execute([':id_act' => $id_act]);
            if (!empty($competences)) {
                $stmt = $pdo->prepare("INSERT INTO necessite (id_act, id_competence) VALUES (:id_act, :id_competence)");
                foreach ($competences as $id_competence) {
                    $stmt->execute([
                        ':id_act' => $id_act,
                        ':id_competence' => $id_competence
                    ]);
                }
            }
            $pdo->commit();
            header('Location: modifierActivite.php?id_act=' . $id_act . '&message=modification_reussie');
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $message = "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une Activité</title>
    <link rel="stylesheet" href="../accueil/styles-accueil.css">
    <link rel="stylesheet" href="styles-mesActivites.css">
    <link rel="stylesheet" href="../../menu/menu.css">
    <script src="../../menu/menu-script.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include_once('../../header/header.php'); ?>
    <div class="main-layout">
        <?php include_once('../../menu/menu.php'); ?>
        <div class="container">
            <?php if (isset($_GET['message']) && $_GET['message'] === 'modification_reussie'): ?>
                <div class="success-message">Les modifications ont été enregistrées avec succès.</div>
            <?php endif; ?>
            <h1 class="main-title">Modifier l'Activité</h1>
            <?php if (isset($message)): ?>
                <p class="error-message"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
            <form action="modifierActivite.php" method="POST" class="form-container">
                <input type="hidden" name="id_act" value="<?php echo $id_act; ?>">

                <div class="form-group">
                    <label for="titre">Titre</label>
                    <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($activite['titre']); ?>" required minlength="3" maxlength="255">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="5" required minlength="10"><?php echo htmlspecialchars($activite['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="localisation">Localisation</label>
                    <input type="text" id="localisation" name="localisation" value="<?php echo htmlspecialchars($activite['localisation']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="conditions_req">Temps Nécessaire</label>
                    <select id="conditions_req" name="conditions_req" required>
                        <option value="Sec" <?= $activite['conditions_req'] === 'Sec' ? 'selected' : '' ?>>Sec</option>
                        <option value="Pluie" <?= $activite['conditions_req'] === 'Pluie' ? 'selected' : '' ?>>Pluie</option>
                        <option value="Neige" <?= $activite['conditions_req'] === 'Neige' ? 'selected' : '' ?>>Neige</option>
                        <option value="NULL" <?= empty($activite['conditions_req']) ? 'selected' : '' ?>>Aucune</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($activite['date_activite']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="heure">Heure</label>
                    <input type="time" id="heure" name="heure" value="<?php echo htmlspecialchars(date('H:i', strtotime($activite['date_activite']))); ?>" required>
                </div>

                <div class="form-group">
                    <label for="nb_places">Nombre de places</label>
                    <input type="number" id="nb_places" name="nb_places" value="<?php echo htmlspecialchars($activite['nb_places']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="theme">Thème</label>
                    <select id="theme" name="theme" required>
                        <option value="jardinage" <?= $activite['theme'] === 'jardinage' ? 'selected' : '' ?>>Jardinage</option>
                        <option value="programmation" <?= $activite['theme'] === 'programmation' ? 'selected' : '' ?>>Programmation</option>
                        <option value="poterie" <?= $activite['theme'] === 'poterie' ? 'selected' : '' ?>>Poterie</option>
                        <option value="sport" <?= $activite['theme'] === 'sport' ? 'selected' : '' ?>>Sport</option>
                        <option value="cuisine" <?= $activite['theme'] === 'cuisine' ? 'selected' : '' ?>>Cuisine</option>
                        <option value="musique" <?= $activite['theme'] === 'musique' ? 'selected' : '' ?>>Musique</option>
                        <option value="photo" <?= $activite['theme'] === 'photo' ? 'selected' : '' ?>>Photo</option>
                        <option value="bricolage" <?= $activite['theme'] === 'bricolage' ? 'selected' : '' ?>>Bricolage</option>
                        <option value="lecture" <?= $activite['theme'] === 'lecture' ? 'selected' : '' ?>>Lecture</option>
                        <option value="écriture" <?= $activite['theme'] === 'écriture' ? 'selected' : '' ?>>Écriture</option>
                        <option value="Autre" <?= $activite['theme'] === 'Autre' ? 'selected' : '' ?>>Autre</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="competence">Compétences requises</label>
                    <select id="competence" name="competence[]" multiple size="5">
                        <option value="">Sélectionnez d'abord un thème</option>
                    </select>
                    <small class="form-help">Utilisez la touche Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs compétences.</small>
                </div>

                <div class="form-submit">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer les modifications</button>
                    <a href="mesActivites.php" class="btn btn-secondary"><i class="fas fa-times"></i> Annuler</a>
                </div>
            </form>
        </div>
    </div>
    <script>
        // Gère le chargement dynamique des compétences selon le thème
        document.addEventListener('DOMContentLoaded', function() {
            const themeSelect = document.getElementById('theme');
            const competenceSelect = document.getElementById('competence');
            const selectedCompetences = <?php echo json_encode($competences_requises); ?>;
            function chargerCompetences(theme) {
                competenceSelect.innerHTML = '<option value="">Chargement en cours...</option>';
                fetch(`../../../backend/get_competences.php?categorie=${theme}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Erreur réseau');
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
                            if (selectedCompetences.includes(parseInt(competence.id_competence))) {
                                option.selected = true;
                            }
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