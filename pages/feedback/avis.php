<?php
session_start();
require_once('../../../backend/config.php');

// Gère la soumission d'un avis sur une activité
if (!isset($_SESSION['user_id'])) {
    echo "Erreur : utilisateur non connecté.";
    exit;
}

$id_user = $_SESSION['user_id'];
$message = '';
$activite = null;

if (!isset($_GET['id_act']) || empty($_GET['id_act'])) {
    header('Location: ../mesActivites/mesActivites.php');
    exit;
}

$id_act = intval($_GET['id_act']);

// Vérifier que l'utilisateur a bien participé à cette activité
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM participer 
    WHERE id = :id_user AND id_act = :id_act
");
$stmt->execute([':id_user' => $id_user, ':id_act' => $id_act]);
$a_participe = $stmt->fetchColumn() > 0;

if (!$a_participe) {
    header('Location: ../mesActivites/mesActivites.php?error=not_participant');
    exit;
}

// Récupérer les informations de l'activité
$stmt = $pdo->prepare("
    SELECT * FROM activite WHERE id_act = :id_act
");
$stmt->execute([':id_act' => $id_act]);
$activite = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$activite) {
    header('Location: ../mesActivites/mesActivites.php?error=activity_not_found');
    exit;
}

$now = new DateTime('now', new DateTimeZone('UTC'));
$now->modify('+2 hours');
$activite_date = new DateTime($activite['date_activite'], new DateTimeZone('UTC'));
$activite_passee = $now >= $activite_date;

if (!$activite_passee) {
    header('Location: ../mesActivites/mesActivites.php?error=activity_not_past');
    exit;
}

// Vérifier si un avis a déjà été donné
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM feedback 
    WHERE id_user = :id_user AND id_act = :id_act
");
$stmt->execute([':id_user' => $id_user, ':id_act' => $id_act]);
$avis_donne = $stmt->fetchColumn() > 0;

if ($avis_donne) {
    header('Location: ../mesActivites/mesActivites.php?error=feedback_already_given');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre_fb = trim($_POST['titre_fb'] ?? '');
    $note_fb = intval($_POST['note_fb'] ?? 0);
    $commentaire_fb = trim($_POST['commentaire_fb'] ?? '');
    if (empty($titre_fb) || $note_fb < 1 || $note_fb > 5 || empty($commentaire_fb)) {
        $message = "Tous les champs sont obligatoires et la note doit être entre 1 et 5.";
    } else {
        // Insertion de l'avis
        try {
            $stmt = $pdo->prepare("
                INSERT INTO feedback (titre_fb, note_fb, commentaire_fb, date_fb, id_user, id_act) 
                VALUES (:titre_fb, :note_fb, :commentaire_fb, CURRENT_DATE, :id_user, :id_act)
            ");
            $stmt->execute([
                ':titre_fb' => $titre_fb,
                ':note_fb' => $note_fb,
                ':commentaire_fb' => $commentaire_fb,
                ':id_user' => $id_user,
                ':id_act' => $id_act
            ]);
            header('Location: ../mesActivites/mesActivites.php?success=feedback_submitted');
            exit;
        } catch (PDOException $e) {
            $message = "Erreur lors de l'enregistrement : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Laisser un avis - <?= htmlspecialchars($activite['titre']) ?></title>
    <link rel="stylesheet" href="../accueil/styles-accueil.css">
    <link rel="stylesheet" href="../mesActivites/styles-mesActivites.css">
    <link rel="stylesheet" href="../../menu/menu.css">
    <link rel="stylesheet" href="styles-feedback.css">
    <script src="../../menu/menu-script.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include_once('../../header/header.php'); ?>
    <div class="main-layout">
        <?php include_once('../../menu/menu.php'); ?>
        <div class="container">
            <h2 class="main-title">Laisser un avis</h2>
            <?php if (!empty($message)): ?>
                <div class="error-message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <div class="form-feedback">
                <div class="activity-summary">
                    <h3><?= htmlspecialchars($activite['titre']) ?></h3>
                    <p><i class="fas fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($activite['date_activite'])) ?></p>
                    <p><?= htmlspecialchars($activite['description']) ?></p>
                </div>
                <form method="POST">
                    <div class="form-group">
                        <label for="titre_fb">Titre de votre avis</label>
                        <input type="text" id="titre_fb" name="titre_fb" required 
                               value="<?= isset($_POST['titre_fb']) ? htmlspecialchars($_POST['titre_fb']) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label>Votre note</label>
                        <div class="star-rating-container">
                            <input type="hidden" name="note_fb" id="note_fb" value="5">
                            <div class="star-rating" id="star-rating">
                                <span class="star active" data-value="1">★</span><span class="star active" data-value="2">★</span><span class="star active" data-value="3">★</span><span class="star active" data-value="4">★</span><span class="star active" data-value="5">★</span>
                            </div>
                            <div class="rating-value" id="rating-display">5 étoiles</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="commentaire_fb">Votre commentaire</label>
                        <textarea id="commentaire_fb" name="commentaire_fb" required><?= isset($_POST['commentaire_fb']) ? htmlspecialchars($_POST['commentaire_fb']) : '' ?></textarea>
                    </div>
                    <div class="form-actions">
                        <a href="../mesActivites/mesActivites.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                        <button type="submit" class="submit-feedback">
                            <i class="fas fa-paper-plane"></i> Envoyer mon avis
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        // Gère l'affichage dynamique des étoiles
        document.addEventListener('DOMContentLoaded', function() {
            const starRating = document.getElementById('star-rating');
            const ratingInput = document.getElementById('note_fb');
            const ratingDisplay = document.getElementById('rating-display');
            const stars = starRating.querySelectorAll('.star');
            function updateStars(rating) {
                stars.forEach(star => {
                    const value = parseInt(star.getAttribute('data-value'));
                    if (value <= rating) {
                        star.classList.add('active');
                    } else {
                        star.classList.remove('active');
                    }
                });
                ratingInput.value = rating;
                ratingDisplay.textContent = rating + (rating > 1 ? ' étoiles' : ' étoile');
            }
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = parseInt(this.getAttribute('data-value'));
                    updateStars(rating);
                });
                star.addEventListener('mouseenter', function() {
                    const rating = parseInt(this.getAttribute('data-value'));
                    stars.forEach(s => {
                        const val = parseInt(s.getAttribute('data-value'));
                        if (val <= rating) {
                            s.style.color = 'var(--feedback-light)';
                        } else {
                            s.style.color = '#ddd';
                        }
                    });
                });
            });
        });
    </script>
    <?php include_once('../../footer/footer.php'); ?>
</body>
</html>