<?php
session_start();
require_once('../../../backend/config.php');

// Récupère les infos et avis d'un utilisateur donné
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_utilisateur = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_utilisateur <= 0) {
    header('Location: ../accueil/accueil.php');
    exit;
}

try {
    // Récupérer les informations de l'utilisateur
    $stmt = $pdo->prepare("
        SELECT id, nom, imagepdp, description 
        FROM utilisateur 
        WHERE id = :id
    ");
    $stmt->execute([':id' => $id_utilisateur]);
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$utilisateur) {
        header('Location: ../accueil/accueil.php');
        exit;
    }
    
    // Calculer la note moyenne de l'utilisateur
    $stmt_note = $pdo->prepare("
        SELECT AVG(f.note_fb) AS note_moyenne, COUNT(f.note_fb) AS nombre_avis
        FROM feedback f
        JOIN activite a ON f.id_act = a.id_act
        WHERE a.id_createur = :id_createur
    ");
    $stmt_note->bindParam(':id_createur', $id_utilisateur, PDO::PARAM_INT);
    $stmt_note->execute();
    $result_note = $stmt_note->fetch(PDO::FETCH_ASSOC);
    
    $note_moyenne = null;
    $nb_avis = 0;
    
    if ($result_note && $result_note['nombre_avis'] > 0) {
        $note_moyenne = round($result_note['note_moyenne'], 1);
        $nb_avis = $result_note['nombre_avis'];
    }
    
    // Récupérer les avis reçus par l'utilisateur
    $stmt = $pdo->prepare("
        SELECT f.*, a.titre as titre_activite, a.date_activite, 
              u.nom as nom_utilisateur, u.imagepdp
        FROM feedback f
        JOIN activite a ON f.id_act = a.id_act
        JOIN utilisateur u ON f.id_user = u.id
        WHERE a.id_createur = :id_createur
        ORDER BY f.date_fb DESC
    ");
    $stmt->execute([':id_createur' => $id_utilisateur]);
    $avis = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error_message = "Erreur de base de données: " . $e->getMessage();
    $utilisateur = null;
    $avis = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil utilisateur</title>
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
            <?php if (isset($error_message)): ?>
                <div class="error-message">
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>
            <?php if ($utilisateur): ?>
                <a href="javascript:history.back()" class="back-link">Retour à la page précédente</a>
                <div class="user-profile">
                    <div class="user-avatar">
                        <img src="<?= !empty($utilisateur['imagepdp']) ? htmlspecialchars($utilisateur['imagepdp']) : '/frontend/images/photoProfil/default.png' ?>" 
                             alt="Photo de profil">
                    </div>
                    <div class="user-info">
                        <h1 class="profile-name"><?= htmlspecialchars($utilisateur['nom']) ?></h1>
                        <div class="user-rating">
                            <?php if ($note_moyenne !== null): ?>
                                <div class="stars-large">
                                    <?php for($i = 0; $i < 5; $i++): ?>
                                        <?php if($i < $note_moyenne): ?>
                                            <i class="fas fa-star"></i>
                                        <?php else: ?>
                                            <i class="far fa-star"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                                <span class="rating-value"><?= $note_moyenne ?>/5 (<?= $nb_avis ?> avis)</span>
                            <?php else: ?>
                                <span class="no-rating-large">Pas encore d'avis</span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($utilisateur['description'])): ?>
                            <p class="user-description"><?= nl2br(htmlspecialchars($utilisateur['description'])) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="section">
                    <h2>Avis reçus</h2>
                    <?php if (!empty($avis)): ?>
                        <div class="avis-grid">
                            <?php foreach ($avis as $a): ?>
                                <div class="avis-card">
                                    <div class="avis-header">
                                        <div class="avis-user">
                                            <img src="<?= !empty($a['imagepdp']) ? htmlspecialchars($a['imagepdp']) : '/frontend/images/photoProfil/default.png' ?>" 
                                                 alt="Photo de profil" class="avatar-mini">
                                            <div>
                                                <span class="user-name"><?= htmlspecialchars($a['nom_utilisateur']) ?></span>
                                                <span class="avis-date"><?= date('d/m/Y', strtotime($a['date_fb'])) ?></span>
                                            </div>
                                        </div>
                                        <div class="avis-rating">
                                            <?php for($i = 0; $i < 5; $i++): ?>
                                                <?php if($i < $a['note_fb']): ?>
                                                    <i class="fas fa-star"></i>
                                                <?php else: ?>
                                                    <i class="far fa-star"></i>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <div class="avis-content">
                                        <h3><?= htmlspecialchars($a['titre_fb']) ?></h3>
                                        <p class="avis-activity">
                                            Pour l'activité: <strong><?= htmlspecialchars($a['titre_activite']) ?></strong> 
                                            (<?= date('d/m/Y', strtotime($a['date_activite'])) ?>)
                                        </p>
                                        <p class="avis-text"><?= nl2br(htmlspecialchars($a['commentaire_fb'])) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="avis-message">Cet utilisateur n'a pas encore reçu d'avis.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
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