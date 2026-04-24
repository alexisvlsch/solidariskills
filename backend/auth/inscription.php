<?php

/**
 * @file inscription.php
 * @brief Gestion de l'inscription utilisateur
 * @ingroup Backend
 * @details
 * - Validation des données
 * - Protection CSRF
 * - Hashage BCrypt
 */

include '../postgre.php';
include '../fonctions.php';

// Démarrer la session pour le token CSRF
session_start();

function validerMotDePasse($mdp) {
    // Vérifie la complexité du mot de passe
    if (strlen($mdp) < 12) {
        envoieDonnees("erreur", "Le mot de passe doit contenir au moins 12 caractères");
    }
    if (!preg_match('/[A-Z]/', $mdp) || 
        !preg_match('/[a-z]/', $mdp) || 
        !preg_match('/[0-9]/', $mdp) || 
        !preg_match('/[\W_]/', $mdp)) {
        envoieDonnees("erreur", "Le mot de passe doit contenir majuscules, minuscules, chiffres et caractères spéciaux");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Vérifie le token CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            envoieDonnees("erreur", "Token de sécurité invalide", 403);
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $mdp = $_POST['password'] ?? '';
        $confirmation = $_POST['confirm_password'] ?? '';
        $username = isset($_POST['username']) ? htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8') : 'Utilisateur';

        if (empty($email) || empty($mdp) || empty($confirmation) || empty($username)) {
            envoieDonnees("erreur", "Tous les champs sont obligatoires");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            envoieDonnees("erreur", "Format d'email invalide");
        }

        if ($mdp !== $confirmation) {
            envoieDonnees("erreur", "Les mots de passe ne correspondent pas");
        }

        validerMotDePasse($mdp);

        $stmt = $pdo->prepare("SELECT id FROM utilisateur WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            envoieDonnees("erreur", "Cet email est déjà utilisé");
        }

        $hash = password_hash($mdp, PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $pdo->prepare("INSERT INTO utilisateur (email, password_hash, nom, statut) VALUES (:email, :hash, :username, 'Membre')");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':hash', $hash);
        $stmt->bindParam(':username', $username);

        if ($stmt->execute()) {
            envoieDonnees("succes", [
                "message" => "Compte créé avec succès",
                "next" => "/connexion.php"
            ]);
        }

    } catch (PDOException $e) {
        error_log("[INSCRIPTION ERROR] " . date('Y-m-d H:i:s') . " - " . $e->getMessage());
        envoieDonnees("erreur", "Erreur lors de l'inscription", 500);
    }
} else {
    envoieDonnees("erreur", "Méthode non autorisée", 405);
}