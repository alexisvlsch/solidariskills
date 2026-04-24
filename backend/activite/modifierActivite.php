<?php
session_start();
require_once('../config.php');

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
            header('Location: /frontend/pages/mesActivites/modifierActivite.php?id_act=' . $id_act . '&message=modification_reussie');
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $message = "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    }
}

// Redirection vers la page de modification côté frontend
header('Location: /frontend/pages/mesActivites/modifierActivite.php?id_act=' . $id_act);
exit;
?>

<form action="/backend/activite/desinscrireActivity.php" method="POST">
    <!-- Contenu du formulaire pour se désinscrire d'une activité -->
</form>

<form action="/backend/activite/supprimerActivite.php" method="POST">
    <!-- Contenu du formulaire pour supprimer une activité -->
</form>

<form action="/backend/activite/modifierActivite.php" method="POST">
    <!-- Contenu du formulaire pour modifier une activité -->
</form>