<?php

require_once('../config.php');

// Récupère les compétences d'une catégorie donnée
$categorie = isset($_GET['categorie']) ? $_GET['categorie'] : '';

try {
    $stmt = $pdo->prepare("SELECT id_competence, nom FROM competence WHERE categorie = :categorie ORDER BY nom");
    $stmt->execute(['categorie' => $categorie]);
    $competences = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($competences);
} catch(PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => $e->getMessage()]);
}
?>