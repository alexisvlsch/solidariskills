<?php
session_start();
require_once('../../../backend/config.php');

// Récupère les avis reçus et laissés par l'utilisateur connecté
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header('Location: /frontend/login.php');
    exit;
}

$id_user = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM information_schema.tables 
        WHERE table_schema = 'public' 
        AND table_name = 'feedback'
    ");
    $stmt->execute();
    $table_exists = ($stmt->fetchColumn() > 0);
    
    if (!$table_exists) {
        $error_message = "La fonctionnalité d'avis n'est pas encore disponible.";
        $avis_recus = [];
        $avis_laisses = [];
    } else {
        // Récupérer les avis reçus sur les activités que j'ai créées
        $stmt = $pdo->prepare("
            SELECT f.*, a.titre as titre_activite, a.date_activite, 
                  u.nom as nom_utilisateur, u.imagepdp
            FROM feedback f
            JOIN activite a ON f.id_act = a.id_act
            JOIN utilisateur u ON f.id_user = u.id
            WHERE a.id_createur = :id_user
            ORDER BY f.date_fb DESC
        ");
        $stmt->execute([':id_user' => $id_user]);
        $avis_recus = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Récupérer les avis que j'ai laissés
        $stmt = $pdo->prepare("
            SELECT f.*, a.titre as titre_activite, a.date_activite, 
                  u.nom as nom_createur
            FROM feedback f
            JOIN activite a ON f.id_act = a.id_act
            JOIN utilisateur u ON a.id_createur = u.id
            WHERE f.id_user = :id_user
            ORDER BY f.date_fb DESC
        ");
        $stmt->execute([':id_user' => $id_user]);
        $avis_laisses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $error_message = "Erreur de base de données: " . $e->getMessage();
    if (strpos($e->getMessage(), "column a.id_createur") !== false) {
        $error_message = "La colonne id_createur n'existe pas dans la table activite. Veuillez vérifier votre schéma de base de données.";
    }
    $avis_recus = [];
    $avis_laisses = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Avis</title>
    <link rel="stylesheet" href="../accueil/styles-accueil.css">
    <link rel="stylesheet" href="styles-mesAvis.css">
    <link rel="stylesheet" href="../../menu/menu.css">
    <script src="../../menu/menu-script.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include_once('../../header/header.php'); ?>
    <div class="main-layout">
        <?php include_once('../../menu/menu.php'); ?>
        <div class="container">
            <h1 class="main-title">Mes Avis</h1>
            <?php if (isset($error_message)): ?>
                <div class="error-message">
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>
            <div class="section">
                <h2>Avis reçus sur mes activités</h2>
                <?php if (!empty($avis_recus)): ?>
                    <div class="avis-grid">
                        <?php foreach ($avis_recus as $avis): ?>
                            <div class="avis-card">
                                <div class="avis-header">
                                    <div class="avis-user">
                                        <img src="<?= !empty($avis['imagepdp']) ? htmlspecialchars($avis['imagepdp']) : '/frontend/images/photoProfil/default.png' ?>" 
                                             alt="Photo de profil" class="avatar-mini">
                                        <div>
                                            <span class="user-name"><?= htmlspecialchars($avis['nom_utilisateur']) ?></span>
                                            <span class="avis-date"><?= date('d/m/Y', strtotime($avis['date_fb'])) ?></span>
                                        </div>
                                    </div>
                                    <div class="avis-rating">
                                        <?php for($i = 0; $i < 5; $i++): ?>
                                            <?php if($i < $avis['note_fb']): ?>
                                                <i class="fas fa-star"></i>
                                            <?php else: ?>
                                                <i class="far fa-star"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <div class="avis-content">
                                    <h3><?= htmlspecialchars($avis['titre_fb']) ?></h3>
                                    <p class="avis-activity">
                                        Pour l'activité: <strong><?= htmlspecialchars($avis['titre_activite']) ?></strong> 
                                        (<?= date('d/m/Y', strtotime($avis['date_activite'])) ?>)
                                    </p>
                                    <p class="avis-text"><?= nl2br(htmlspecialchars($avis['commentaire_fb'])) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-data">Vous n'avez pas encore reçu d'avis sur vos activités.</p>
                <?php endif; ?>
            </div>
            <div class="section">
                <h2>Avis que j'ai laissés</h2>
                <?php if (!empty($avis_laisses)): ?>
                    <div class="avis-grid">
                        <?php foreach ($avis_laisses as $avis): ?>
                            <div class="avis-card">
                                <div class="avis-header">
                                    <div class="avis-activity-info">
                                        <h3><?= htmlspecialchars($avis['titre_fb']) ?></h3>
                                        <div class="avis-rating">
                                            <?php for($i = 0; $i < 5; $i++): ?>
                                                <?php if($i < $avis['note_fb']): ?>
                                                    <i class="fas fa-star"></i>
                                                <?php else: ?>
                                                    <i class="far fa-star"></i>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <span class="avis-date"><?= date('d/m/Y', strtotime($avis['date_fb'])) ?></span>
                                </div>
                                <div class="avis-content">
                                    <p class="avis-activity">
                                        Pour l'activité: <strong><?= htmlspecialchars($avis['titre_activite']) ?></strong> 
                                        (<?= date('d/m/Y', strtotime($avis['date_activite'])) ?>) 
                                        par <?= htmlspecialchars($avis['nom_createur']) ?>
                                    </p>
                                    <p class="avis-text"><?= nl2br(htmlspecialchars($avis['commentaire_fb'])) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-data">Vous n'avez pas encore laissé d'avis.</p>
                <?php endif; ?>
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
</html>