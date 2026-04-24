<?php
session_start();
require_once('../../../backend/config.php');

// Récupère les contacts et discussions de l'utilisateur
if (!isset($_SESSION['user_id'])) {
    header('Location: ../connexion_inscription/auth.php');
    exit();
}

$id_user = $_SESSION['user_id'];

// Récupérer tous les contacts (statut accepté dans un sens ou l'autre)
$stmt = $pdo->prepare("
    SELECT u.id, u.nom, u.imagepdp, u.email
    FROM utilisateur u
    WHERE u.id != :id_user
      AND EXISTS (
        SELECT 1 FROM ajouter a
        WHERE a.statut = 'accepté'
          AND (
            (a.id_user_source = :id_user AND a.id_user_cible = u.id)
            OR
            (a.id_user_cible = :id_user AND a.id_user_source = u.id)
          )
      )
");
$stmt->execute([':id_user' => $id_user]);
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les discussions (utilisateurs avec qui il y a eu échange de messages)
$stmt = $pdo->prepare("
    SELECT u.id, u.nom, u.imagepdp, MAX(m.date_msg) as last_msg
    FROM utilisateur u
    JOIN message m ON ( (m.id_expediteur = :id_user AND m.id_destinataire = u.id) OR (m.id_destinataire = :id_user AND m.id_expediteur = u.id) )
    WHERE u.id != :id_user
    GROUP BY u.id, u.nom, u.imagepdp
    ORDER BY last_msg DESC
");
$stmt->execute([':id_user' => $id_user]);
$discussions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les messages d'une discussion si un contact est sélectionné
$selected_id = isset($_GET['contact']) ? intval($_GET['contact']) : 0;
$messages = [];
if ($selected_id > 0) {
    $stmt = $pdo->prepare("
        SELECT m.*, u.nom, u.imagepdp
        FROM message m
        JOIN utilisateur u ON u.id = m.id_expediteur
        WHERE (m.id_expediteur = :me AND m.id_destinataire = :other)
           OR (m.id_expediteur = :other AND m.id_destinataire = :me)
        ORDER BY m.date_msg ASC
    ");
    $stmt->execute([':me' => $id_user, ':other' => $selected_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récup info contact sélectionné
    $stmt = $pdo->prepare("SELECT nom, imagepdp FROM utilisateur WHERE id = :id");
    $stmt->execute([':id' => $selected_id]);
    $contact_info = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Messagerie</title>
    <link rel="stylesheet" href="../../menu/menu.css">
    <link rel="stylesheet" href="styles-messagerie.css">
    <script src="../../menu/menu-script.js"></script>
</head>
<body>
<?php include_once('../../header/header.php'); ?>
<div class="main-layout">
    <?php include_once('../../menu/menu.php'); ?>
    <div class="container">
        <div class="messagerie-layout">
            <aside class="messagerie-sidebar">
                <button class="toggle-messagerie-sidebar" onclick="toggleMessagerieSidebar()" title="Réduire/Étendre">&#9776;</button>
                <h2>Contacts</h2>
                <ul>
                    <?php foreach ($contacts as $contact): ?>
                        <li>
                            <a href="?contact=<?= $contact['id'] ?>" class="<?= $selected_id == $contact['id'] ? 'active' : '' ?>">
                                <img src="<?= !empty($contact['imagepdp']) ? htmlspecialchars($contact['imagepdp']) : '/frontend/images/photoProfil/default.png' ?>" class="avatar" alt="Avatar" >
                                <?= htmlspecialchars($contact['nom']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <h2>Discussions</h2>
                <ul>
                    <?php foreach ($discussions as $disc): ?>
                        <li>
                            <a href="?contact=<?= $disc['id'] ?>" class="<?= $selected_id == $disc['id'] ? 'active' : '' ?>">
                                <img src="<?= !empty($disc['imagepdp']) ? htmlspecialchars($disc['imagepdp']) : '/frontend/images/photoProfil/default.png' ?>" alt="Avatar" class="avatar">
                                <?= htmlspecialchars($disc['nom']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </aside>
            <main class="messagerie-main">
                <?php if ($selected_id > 0 && isset($contact_info)): ?>
                    <div class="discussion-header">
                        <img src="<?= !empty($contact_info['imagepdp']) ? htmlspecialchars($contact_info['imagepdp']) : '/frontend/images/photoProfil/default.png' ?>" class="avatar" alt="Avatar">
                        <span><?= htmlspecialchars($contact_info['nom']) ?></span>
                    </div>
                    <div class="messages-list">
                        <?php foreach ($messages as $msg): ?>
                            <div class="message <?= $msg['id_expediteur'] == $id_user ? 'sent' : 'received' ?>">
                                <div class="msg-content"><?= htmlspecialchars($msg['contenu_msg']) ?></div>
                                <div class="msg-date"><?= date('d/m/Y H:i', strtotime($msg['date_msg'])) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <form action="/backend/messagerie/send_message.php" method="POST" class="send-message-form">
                        <input type="hidden" name="destinataire" value="<?= $selected_id ?>">
                        <textarea name="contenu_msg" placeholder="Votre message..." required></textarea>
                        <button type="submit">Envoyer</button>
                    </form>
                <?php else: ?>
                    <div class="no-discussion">Sélectionnez un contact ou une discussion pour commencer à échanger.</div>
                <?php endif; ?>
            </main>
        </div>
    </div>
</div>
<script>
    // Gère l'ouverture/fermeture de la sidebar messagerie
    function toggleMessagerieSidebar() {
        const sidebar = document.querySelector('.messagerie-sidebar');
        sidebar.classList.toggle('collapsed');
    }
</script>
<?php include_once('../../footer/footer.php'); ?>
</body>
</html>