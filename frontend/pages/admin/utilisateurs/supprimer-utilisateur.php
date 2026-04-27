<?php
require_once('../admin_auth.php');

// Connexion à la base de données
require_once('../../../../backend/config.php'); 

// Vérification de la session

// Vérifie que l’ID est présent dans l’URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];


    try {
    
        // Supprimer l'utilisateur
        $pdo->prepare("DELETE FROM utilisateur WHERE id = :id")->execute([':id' => $id]);
    
        echo "Utilisateur et dépendances supprimés.";
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}

// Redirection
header("Location: utilisateurs.php");
exit;

?>