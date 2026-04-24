<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

$id_user = $_SESSION['user_id'];

try {
    // Récupère les compétences de l'utilisateur connecté
    $stmt = $pdo->prepare("
        SELECT c.id_competence, c.nom
        FROM competence c
        JOIN gerer_cpu g ON c.id_competence = g.id_competence
        WHERE g.id = :id_user
    ");
    $stmt->execute(['id_user' => $id_user]);
    $competences = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'competences' => $competences
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des compétences: ' . $e->getMessage()
    ]);
}