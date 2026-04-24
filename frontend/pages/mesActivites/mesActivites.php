<?php
session_start();
require_once('../../../backend/config.php');

// Function to debug date comparisons with correct timezone
function debugDate($title, $date_string) {
    // Créer la date en UTC et ajouter 2 heures
    $now = new DateTime('now', new DateTimeZone('UTC'));
    $now->modify('+2 hours');
    
    // Garder la date d'activité en UTC comme dans la BD
    $date = new DateTime($date_string, new DateTimeZone('UTC'));
    $is_past = $now >= $date;
    
    echo "<script>";
    echo "console.group('Debug: $title');";
    echo "console.log('Raw date string:', " . json_encode($date_string) . ");";
    echo "console.log('Current time (UTC+2):', " . json_encode($now->format('Y-m-d H:i:s')) . ");";
    echo "console.log('Date object (UTC):', " . json_encode($date->format('Y-m-d H:i:s')) . ");";
    echo "console.log('Is date in the past:', " . ($is_past ? 'true' : 'false') . ");";
    echo "console.log('Current timestamp:', " . $now->getTimestamp() . ");";
    echo "console.log('Date timestamp:', " . $date->getTimestamp() . ");";
    echo "console.log('Difference (seconds):', " . ($now->getTimestamp() - $date->getTimestamp()) . ");";
    echo "console.groupEnd();";
    echo "</script>";
    
    return $is_past;
}

if (!isset($_SESSION['user_id'])) {
    echo "Erreur : utilisateur non connecté.";
    exit;
}

$id_user = $_SESSION['user_id'];

// Désinscription multiple si POST reçu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['desinscrire_id_act'])) {
    $ids_act = $_POST['desinscrire_id_act']; // Récupère les IDs des activités sélectionnées
    if (is_array($ids_act) && count($ids_act) > 0) {
        // Prépare une requête pour supprimer plusieurs participations
        $placeholders = implode(',', array_fill(0, count($ids_act), '?'));
        $stmt = $pdo->prepare("DELETE FROM participer WHERE id = ? AND id_act IN ($placeholders)");
        $params = array_merge([$id_user], $ids_act);
        $stmt->execute($params);

        $message = "Vous avez été désinscrit des activités sélectionnées.";
    }
}

// Tableau de correspondance thème → image
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
    'ski'            => '../../images/ski.png'
];

// Activités créées par l'utilisateur
$stmt = $pdo->prepare("SELECT id_act, titre, description, theme, date_activite FROM activite WHERE id_createur = :id_user ORDER BY id_act DESC");
$stmt->execute([':id_user' => $id_user]);
$activites_creees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Activités auxquelles l'utilisateur est inscrit (hors créateur)
$stmt = $pdo->prepare("
    SELECT a.id_act, a.titre, a.description, a.theme, a.date_activite
    FROM activite a
    JOIN participer p ON a.id_act = p.id_act
    WHERE p.id = :id_user
    ORDER BY a.id_act DESC
");
$stmt->execute([':id_user' => $id_user]);
$activites_inscrites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Activités</title>
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
            <?php if (!empty($message)): ?>
                <div class="success-message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success']) && $_GET['success'] === 'feedback_submitted'): ?>
                <div class="success-message">Votre avis a été enregistré avec succès. Merci pour votre participation !</div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php 
                        switch ($_GET['error']) {
                            case 'not_participant':
                                echo "Vous n'avez pas participé à cette activité.";
                                break;
                            case 'activity_not_found':
                                echo "L'activité demandée n'existe pas.";
                                break;
                            case 'activity_not_past':
                                echo "Vous ne pouvez laisser un avis que sur une activité passée.";
                                break;
                            case 'feedback_already_given':
                                echo "Vous avez déjà donné votre avis pour cette activité.";
                                break;
                            default:
                                echo "Une erreur s'est produite.";
                        }
                    ?>
                </div>
            <?php endif; ?>

            <!-- Ajout de la classe main-title au titre principal -->
            <h2 class="main-title">Mes Activités créées</h2>
            
            <!-- Section : Mes Activités créées -->
            <div class="section">
                <div class="card-grid">
                    <?php if (count($activites_creees)): ?>
                        <?php foreach ($activites_creees as $activite): ?>
                            <div class="card">
                                <div class="card-content">
                                    <!-- Affichage de l'image -->
                                    <div class="card-image">
                                        <?php
                                        $theme = $activite['theme'] ?? 'Autre';
                                        $imagePath = $themeImages[$theme] ?? '../../images/logoBE.png';
                                        ?>
                                        <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($theme) ?>">
                                    </div>
                                    <h3><?php echo htmlspecialchars($activite['titre']); ?></h3>
                                    <p><?php echo htmlspecialchars($activite['description']); ?></p>
                                    <!-- Affichage de la date et heure -->
                                    <p class="activity-date">
                                        <i class="fas fa-calendar-alt"></i> 
                                        <?= date('d/m/Y à H:i', strtotime($activite['date_activite'])) ?>
                                        <?php 
                                        // Créer directement la date en UTC avec l'ajustement
                                        $now = new DateTime('now', new DateTimeZone('UTC'));
                                        // Ajouter 2 heures pour refléter l'heure de Paris
                                        $now->modify('+2 hours');
                                        
                                        // Date d'activité déjà en UTC
                                        $activite_date = new DateTime($activite['date_activite'], new DateTimeZone('UTC'));
                                        $activite_passee = $now >= $activite_date;
                                        
                                        // Ajouter ces valeurs en attributs data pour les logs
                                        $now_formatted = $now->format('Y-m-d H:i:s');
                                        $activite_date_formatted = $activite_date->format('Y-m-d H:i:s');
                                        $raw_date_from_db = $activite['date_activite'];
                                        ?>
                                        <script>
                                            console.group("Analyse de date pour l'activité créée: <?= htmlspecialchars($activite['titre']) ?>");
                                            console.log("Date brute de la BD:", "<?= $raw_date_from_db ?>");
                                            console.log("Date actuelle (UTC+2):", "<?= $now_formatted ?>");
                                            console.log("Date activité (UTC):", "<?= $activite_date_formatted ?>");
                                            console.log("Date actuelle > Date activité:", <?= $activite_passee ? 'true' : 'false' ?>);
                                            console.log("Timestamp actuel:", <?= $now->getTimestamp() ?>);
                                            console.log("Timestamp activité:", <?= $activite_date->getTimestamp() ?>);
                                            console.log("Différence (secondes):", <?= $now->getTimestamp() - $activite_date->getTimestamp() ?>);
                                            console.groupEnd();
                                        </script>
                                    </p>
                                    <div class="card-actions">
                                        <form action="/backend/activite/modifierActivite.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="id_act" value="<?php echo $activite['id_act']; ?>">
                                            <button type="submit" class="btn btn-primary"><i class="fa fa-edit"></i> Modifier</button>
                                        </form>
                                        <form action="/backend/activite/supprimerActivite.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="id_act" value="<?php echo $activite['id_act']; ?>">
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette activité ?');"><i class="fa fa-trash"></i> Supprimer</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Vous n'avez créé aucune activité.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Section : Mes Activités inscrites -->
            <div class="section">
                <h2>Mes Activités inscrites</h2>
                <form method="POST">
                    <div class="card-grid">
                        <?php if (count($activites_inscrites)): ?>
                            <?php foreach ($activites_inscrites as $activite): 
                                // Vérifier si l'activité est passée avec DateTime pour une précision exacte
                                // Utiliser la timezone Europe/Paris pour la date actuelle
                                $now = new DateTime('now', new DateTimeZone('Europe/Paris'));
                                // Garder la date d'activité en UTC comme dans la BD
                                $activite_date = new DateTime($activite['date_activite'], new DateTimeZone('UTC'));
                                
                                // Comparison plus explicite pour debugging
                                $activite_passee = $now >= $activite_date;
                                
                                // Vérifier si l'utilisateur a déjà laissé un avis
                                $stmt_avis = $pdo->prepare("SELECT COUNT(*) FROM feedback WHERE id_user = :id_user AND id_act = :id_act");
                                $stmt_avis->execute([':id_user' => $id_user, ':id_act' => $activite['id_act']]);
                                $avis_deja_donne = $stmt_avis->fetchColumn() > 0;
                            ?>
                                <div class="card">
                                    <div class="card-content">
                                        <!-- Affichage de l'image -->
                                        <div class="card-image">
                                            <?php
                                            $theme = $activite['theme'] ?? 'Autre';
                                            $imagePath = $themeImages[$theme] ?? '../../images/logoBE.png';
                                            ?>
                                            <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($theme) ?>">
                                        </div>
                                        <!-- Affichage des informations de l'activité -->
                                        <h3><?php echo htmlspecialchars($activite['titre']); ?></h3>
                                        <p><?php echo htmlspecialchars($activite['description']); ?></p>
                                        <!-- Affichage de la date et heure -->
                                        <p class="activity-date">
                                            <i class="fas fa-calendar-alt"></i> 
                                            <?= date('d/m/Y à H:i', strtotime($activite['date_activite'])) ?>
                                            <?php 
                                            $now = new DateTime('now', new DateTimeZone('UTC'));
                                            // Ajouter 2 heures pour refléter l'heure de Paris
                                            $now->modify('+2 hours');
                                            
                                            $activite_date = new DateTime($activite['date_activite'], new DateTimeZone('UTC'));
                                            $activite_passee = $now >= $activite_date;
                                            
                                            // Ajouter ces valeurs en attributs data pour les logs JavaScript
                                            $now_formatted = $now->format('Y-m-d H:i:s');
                                            $activite_date_formatted = $activite_date->format('Y-m-d H:i:s');
                                            $raw_date_from_db = $activite['date_activite'];
                                            $activite_passee_text = $activite_passee ? 'true' : 'false';
                                            ?>
                                            <script>
                                                console.group("Analyse de date pour l'activité: <?= htmlspecialchars($activite['titre']) ?>");
                                                console.log("Date brute de la BD:", "<?= $raw_date_from_db ?>");
                                                console.log("Date actuelle (UTC):", "<?= $now_formatted ?>");
                                                console.log("Date activité (UTC):", "<?= $activite_date_formatted ?>");
                                                console.log("Date actuelle > Date activité:", "<?= $activite_passee_text ?>");
                                                console.log("Timestamp actuel:", <?= $now->getTimestamp() ?>);
                                                console.log("Timestamp activité:", <?= $activite_date->getTimestamp() ?>);
                                                console.log("Différence (secondes):", <?= $now->getTimestamp() - $activite_date->getTimestamp() ?>);
                                                console.groupEnd();
                                            </script>
                                            
                                            <?php if ($activite_passee): ?>
                                                <span class="badge-past">Terminée</span>
                                            <?php else: ?>
                                                <span class="badge-upcoming">À venir</span>
                                            <?php endif; ?>
                                        </p>
                                        <div class="card-actions">
                                            <?php if ($activite_passee): ?>
                                                <?php if (!$avis_deja_donne): ?>
                                                    <a href="../feedback/avis.php?id_act=<?= $activite['id_act'] ?>" class="btn btn-review">
                                                        <i class="fas fa-star"></i> Laisser un avis
                                                    </a>
                                                <?php else: ?>
                                                    <span class="feedback-already">Avis déjà donné</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <label>
                                                    <input type="checkbox" name="desinscrire_id_act[]" value="<?php echo $activite['id_act']; ?>">
                                                    Sélectionner
                                                </label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Vous n'êtes inscrit à aucune activité.</p>
                        <?php endif; ?>
                    </div>
                    <?php 
                    // Compter les activités non passées pour afficher le bouton de désinscription
                    $now = new DateTime('now', new DateTimeZone('UTC'));
                    // Ajouter 2 heures pour refléter l'heure de Paris
                    $now->modify('+2 hours');
                    
                    $activites_a_venir = array_filter($activites_inscrites, function($a) use ($now) {
                        $activite_date = new DateTime($a['date_activite'], new DateTimeZone('UTC'));
                        return $activite_date > $now;
                    });
                    
                    if (count($activites_a_venir)): 
                    ?>
                        <div class="form-submit">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir vous désinscrire des activités sélectionnées ?');">
                                <i class="fa fa-sign-out-alt"></i> Se désinscrire des activités sélectionnées
                            </button>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <script>
        // Check for saved state on page load
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