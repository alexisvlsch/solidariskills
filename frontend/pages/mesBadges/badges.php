<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Récupère les badges de l'utilisateur connecté
if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour voir vos badges.");
}

require_once('../../../backend/config.php'); 
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);

$allBadges = [
    'ambassadeur'      => 'Ambassadeur',
    'explorateur'      => 'Explorateur',
    'feedbacker'       => 'Feedbacker',
    'fidèle'          => 'Fidèle',
    'meteo_hiver'      => 'Météo Hiver',
    'organisateur'     => 'Organisateur',
    'pillier'          => 'Pilier',
    'soutien_meteo'    => 'Soutien Météo',
    'super_actif'      => 'Super Actif',
    'volontaire'       => 'Volontaire'
];

// Récupération des badges de l'utilisateur connecté
try {
    $stmt = $pdo->prepare("
        SELECT b.nom_badge AS badge
        FROM attribuer a
        INNER JOIN badge b ON a.id_badge = b.id_badge
        WHERE a.id = :user_id
    ");
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $userBadges = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des données : " . $e->getMessage());
}

// Normalise le nom du badge pour la comparaison
function normalizeBadgeName($name) {
    return strtolower(
        str_replace(
            [' ', 'é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'î', 'ï', 'ô', 'ö', 'ù', 'û', 'ü', 'ç', "'", '’'],
            ['_', 'e', 'e', 'e', 'e', 'a', 'a', 'a', 'i', 'i', 'o', 'o', 'u', 'u', 'u', 'c', '', ''],
            $name
        )
    );
}

$userBadgesNorm = array_map('normalizeBadgeName', $userBadges);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Badges</title>
    <link rel="stylesheet" href="../../header/header.css">
    <link rel="stylesheet" href="../../menu/menu.css">
    <link rel="stylesheet" href="../accueil/styles-accueil.css">
    <script src="../../menu/menu-script.js"></script>
    <style>
        .badges-table { width: 100%; max-width: 700px; margin: 0 auto; border-collapse: separate; border-spacing: 30px 10px; }
        .badges-table td { text-align: center; vertical-align: bottom; }
        .badge-logo { width: 80px; height: 80px; object-fit: contain; display: block; margin: 0 auto 8px auto; transition: filter 0.2s, opacity 0.2s; }
        .badge-gris { filter: grayscale(1); opacity: 0.4; }
        .badge-title { font-size: 1rem; color: var(--text-color, #333); margin-top: 4px; }
    </style>
</head>
<body>
    <?php include_once('../../header/header.php'); ?>
    <div class="main-layout">
        <?php include_once('../../menu/menu.php'); ?>
        <div class="container">
            <h1>Mes Badges</h1>
            <table class="badges-table">
                <tr>
                <?php
                $i = 0;
                foreach ($allBadges as $file => $title):
                    $normalizedFile = normalizeBadgeName($file);
                    $isOwned = in_array($normalizedFile, $userBadgesNorm);
                    $imgPath = "../../images/photobadge/$file.png";
                ?>
                    <td>
                        <img 
                            src="<?= htmlspecialchars($imgPath) ?>" 
                            alt="<?= htmlspecialchars($title) ?>" 
                            class="badge-logo<?= $isOwned ? '' : ' badge-gris' ?>"
                        >
                        <div class="badge-title"><?= htmlspecialchars($title) ?></div>
                    </td>
                <?php
                    $i++;
                    if ($i % 5 === 0) echo '</tr><tr>';
                endforeach;
                ?>
                </tr>
            </table>
        </div>
    </div>
    <?php include_once('../../footer/footer.php'); ?>
</body>
</html>