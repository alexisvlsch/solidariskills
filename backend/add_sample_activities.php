<?php
require_once('config.php');

// Ajoute une activité exemple si aucune n'existe
$stmt = $pdo->prepare("SELECT COUNT(*) FROM activite");
$stmt->execute();
$count = $stmt->fetchColumn();

if($count == 0) {
    $stmt = $pdo->prepare("INSERT INTO activite (titre, description, localisation, nb_places, conditions_req) 
                        VALUES ('Activité exemple', 'Une description d''exemple', 'Paris', 10, 'Sec')");
    $stmt->execute();
    echo "Sample activity added!";
} else {
    echo "Database already has $count activities.";
}
?>