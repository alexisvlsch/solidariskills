<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

$id_user = $_SESSION['user_id'];

$data = json_decode(file_get_contents('php://input'), true);
$competenceIds = $data['competenceIds'] ?? [];

try {
    // Met à jour les compétences de l'utilisateur
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("DELETE FROM gerer_cpu WHERE id = :id_user");
    $stmt->execute(['id_user' => $id_user]);

    if (!empty($competenceIds)) {
        $stmt = $pdo->prepare("INSERT INTO gerer_cpu (id, id_competence) VALUES (:id_user, :id_competence)");
        foreach ($competenceIds as $id_competence) {
            $stmt->execute([
                'id_user' => $id_user,
                'id_competence' => $id_competence
            ]);
        }
    }

    $placeholders = !empty($competenceIds) ? implode(',', array_fill(0, count($competenceIds), '?')) : '0';
    $stmt = $pdo->prepare("SELECT nom FROM competence WHERE id_competence IN ($placeholders)");
    if (!empty($competenceIds)) {
        $stmt->execute($competenceIds);
        $competenceNames = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } else {
        $competenceNames = [];
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'competences' => $competenceNames
    ]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la mise à jour des compétences: ' . $e->getMessage()
    ]);
}