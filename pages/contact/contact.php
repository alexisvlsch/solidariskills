<?php

session_start();
require_once('../../../backend/config.php');

// Récupère les contacts et gère les demandes
if (!isset($_SESSION['user_id'])) {
    header('Location: ../connexion_inscription/auth.php');
    exit();
}
$id_user = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT u.nom, u.email, u.imagepdp
    FROM utilisateur u
    JOIN ajouter a ON (
        (a.id_user_source = :id_user AND a.id_user_cible = u.id)
        OR (a.id_user_cible = :id_user AND a.id_user_source = u.id)
    )
    WHERE a.statut = 'accepté' AND u.id != :id_user
");
$stmt->execute([':id_user' => $id_user]);
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $pdo->prepare("
    SELECT u.nom, u.email, a.statut
    FROM utilisateur u
    JOIN ajouter a ON a.id_user_cible = u.id
    WHERE a.id_user_source = :id_user AND u.id != :id_user
");
$stmt->execute([':id_user' => $id_user]);
$demandes_envoyees = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $pdo->prepare("
    SELECT u.id, u.nom, u.email, a.statut
    FROM utilisateur u
    JOIN ajouter a ON a.id_user_source = u.id
    WHERE a.id_user_cible = :id_user AND a.statut = 'en_attente'
");
$stmt->execute([':id_user' => $id_user]);
$demandes_recues = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Gère l'ajout d'un contact
$popup_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email_ajout'])) {
    $email = trim($_POST['email_ajout']);
    if ($email === '') {
        $popup_message = "L'adresse email ne peut pas être vide.";
    } elseif ($email === $_SESSION['email']) {
        $popup_message = "Vous ne pouvez pas vous ajouter vous-même.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM utilisateur WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            $popup_message = "Aucun utilisateur trouvé avec cet email.";
        } else {
            $id_cible = $user['id'];
            $stmt = $pdo->prepare("SELECT * FROM ajouter WHERE id_user_source = :me AND id_user_cible = :cible");
            $stmt->execute([':me' => $id_user, ':cible' => $id_cible]);
            if ($stmt->fetch()) {
                $popup_message = "Une demande existe déjà pour cet utilisateur.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO ajouter (id_user_source, id_user_cible, statut) VALUES (:me, :cible, 'en_attente')");
                $stmt->execute([':me' => $id_user, ':cible' => $id_cible]);
                $popup_message = "success";
            }
        }
    }
}

// Gère l'acceptation ou le refus d'une demande reçue
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_contact']) && isset($_POST['id_source'])) {
    $id_source = intval($_POST['id_source']);
    $action = $_POST['action_contact'];
    if ($action === 'accepter') {
        $stmt = $pdo->prepare("UPDATE ajouter SET statut = 'accepté' WHERE id_user_source = :src AND id_user_cible = :me");
        $stmt->execute([':src' => $id_source, ':me' => $id_user]);
    } elseif ($action === 'refuser') {
        $stmt = $pdo->prepare("UPDATE ajouter SET statut = 'refusé' WHERE id_user_source = :src AND id_user_cible = :me");
        $stmt->execute([':src' => $id_source, ':me' => $id_user]);
    }
    header("Location: contact.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Contact</title>
    <link rel="stylesheet" href="../../menu/menu.css">
    <link rel="stylesheet" href="styles-contact.css">
    <script src="../../menu/menu-script.js"></script>
</head>
<body>
<?php include_once('../../header/header.php'); ?>
<div class="main-layout">
    <?php include_once('../../menu/menu.php'); ?>
    <div class="container">
        <div class="main-content">
            <h1>Contact</h1>
            <div class="contact-container">
                <div class="contact-actions">
                    <button class="btn" onclick="openPopup()">Ajouter un contact</button>
                </div>
                <div class="contact-columns">
                    <div class="contact-col">
                        <h2>Mes contacts</h2>
                        <ul>
                            <?php foreach ($contacts as $c): ?>
                                <li>
                                    <span class="avatar-mini">
                                        <img src="<?= !empty($c['imagepdp']) ? htmlspecialchars($c['imagepdp']) : '../../images/photoProfil/default.png' ?>" alt="Avatar">
                                    </span>
                                    <?= htmlspecialchars($c['nom']) ?> <span class="email"><?= htmlspecialchars($c['email']) ?></span>
                                </li>
                            <?php endforeach; ?>
                            <?php if (empty($contacts)): ?>
                                <li>Aucun contact accepté.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="contact-col">
                        <h2>Demandes envoyées</h2>
                        <ul>
                            <?php foreach ($demandes_envoyees as $d): ?>
                                <li>
                                    <?= htmlspecialchars($d['email']) ?>
                                    <span class="statut <?= $d['statut'] ?>"><?= ucfirst($d['statut']) ?></span>
                                </li>
                            <?php endforeach; ?>
                            <?php if (empty($demandes_envoyees)): ?>
                                <li>Aucune demande envoyée.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="contact-col">
                        <h2>Demandes reçues</h2>
                        <ul>
                            <?php foreach ($demandes_recues as $d): ?>
                                <li>
                                    <?= htmlspecialchars($d['email']) ?>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="id_source" value="<?= $d['id'] ?>">
                                        <button type="submit" name="action_contact" value="accepter" class="btn btn-success">Accepter</button>
                                        <button type="submit" name="action_contact" value="refuser" class="btn btn-danger">Refuser</button>
                                    </form>
                                </li>
                            <?php endforeach; ?>
                            <?php if (empty($demandes_recues)): ?>
                                <li>Aucune demande reçue.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="popup-ajout" class="popup-bg" style="display:none;">
    <div class="popup">
        <div class="popup-header">
            <span>Ajout d'un contact</span>
            <button class="close-btn" onclick="closePopup()">&times;</button>
        </div>
        <?php if ($popup_message === 'success'): ?>
            <div class="popup-message success">
                Ton invitation a bien été envoyée !
                <button class="btn" onclick="closePopup()">Continuer</button>
            </div>
        <?php elseif ($popup_message): ?>
            <div class="popup-message error">
                <?= htmlspecialchars($popup_message) ?>
                <button class="btn" onclick="closePopup()">Fermer</button>
            </div>
        <?php else: ?>
            <form method="post" class="popup-form">
                <label for="email_ajout">Saisis l’adresse mail du contact à ajouter :</label>
                <input type="email" name="email_ajout" id="email_ajout" required placeholder="exemple@mail.com">
                <button type="submit" class="btn">Envoyer</button>
            </form>
        <?php endif; ?>
    </div>
</div>
<script>
// Gère l'ouverture et la fermeture de la popup d'ajout de contact
function openPopup() {
    document.getElementById('popup-ajout').style.display = 'flex';
}
function closePopup() {
    document.getElementById('popup-ajout').style.display = 'none';
    window.location.href = 'contact.php';
}
<?php if ($popup_message): ?>
    openPopup();
<?php endif; ?>
</script>
<?php include_once('../../footer/footer.php'); ?>
</body>
</html>