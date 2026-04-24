<?php
session_start();
require_once('../config.php');

// Récupère les compétences sélectionnées selon les IDs reçus
$data = json_decode(file_get_contents('php://input'), true);
$competenceIds = $data['competenceIds'] ?? [];

if (empty($competenceIds)) {
    echo json_encode([
        'success' => true,
        'competences' => []
    ]);
    exit;
}

try {
    $placeholders = implode(',', array_fill(0, count($competenceIds), '?'));

    $stmt = $pdo->prepare("
        SELECT id_competence, nom, categorie, niveau
        FROM competence
        WHERE id_competence IN ($placeholders)
    ");

    $stmt->execute($competenceIds);
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