<?php
session_start();
require_once('../config.php');

// Gère la participation à une activité
if (!isset($_SESSION['user_id'])) {
    header('Location: /frontend/pages/connexion_inscription/auth.php');
    exit();
}
$id_user = $_SESSION['user_id'];
$id_act = isset($_POST['id_activite']) ? intval($_POST['id_activite']) : 0;
if ($id_act <= 0) {
    header('Location: /frontend/pages/activites/activites.php?error=no_id');
    exit();
}
if ($id_user > 0 && $id_act > 0) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM participer WHERE id = :id AND id_act = :id_act");
        $stmt->execute([
            ':id' => $id_user,
            ':id_act' => $id_act
        ]);
        $already = $stmt->fetchColumn();
        if ($already > 0) {
            header("Location: /frontend/pages/activites/activites.php?id=$id_act&info=1");
            exit();
        }
        $stmt = $pdo->prepare("SELECT nb_places FROM activite WHERE id_act = :id_act");
        $stmt->execute([':id_act' => $id_act]);
        $nb_places = $stmt->fetchColumn();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM participer WHERE id_act = :id_act");
        $stmt->execute([':id_act' => $id_act]);
        $nb_inscrits = $stmt->fetchColumn();
        if ($nb_inscrits >= $nb_places) {
            header("Location: /frontend/pages/activites/activites.php?id=$id_act&error=places");
            exit();
        }
        $stmt = $pdo->prepare("INSERT INTO participer (id, id_act) VALUES (:id, :id_act)");
        $stmt->execute([
            ':id' => $id_user,
            ':id_act' => $id_act
        ]);
        $date_activite = date('Y-m-d');
        $stmt = $pdo->prepare("INSERT INTO reserver (id, id_act, date_reservation) VALUES (:id, :id_act, :date_reservation)");
        $stmt->execute([
            ':id' => $id_user,
            ':id_act' => $id_act,
            ':date_reservation' => $date_activite
        ]);
        header("Location: /frontend/pages/activites/activites.php?id=$id_act&success=1");
        exit();
    } catch (PDOException $e) {
        header("Location: /frontend/pages/activites/activites.php?id=$id_act&error=1");
        exit();
    }
} else {
    header("Location: /frontend/pages/activites/activites.php?id=$id_act&error=1");
    exit();
}