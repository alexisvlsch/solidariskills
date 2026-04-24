<?php

/**
 * @file connexion.php
 * @brief Gestion de la connexion utilisateur
 * @ingroup Backend
 * @details
 * - Vérification des identifiants dans les tables admin et utilisateur
 * - Gestion de session sécurisée
 * - Protection CSRF
 */

include '../postgre.php';
include '../fonctions.php';

session_start();

function verifierConnexion($mdp, $hash_bdd) {
    // Vérifie la correspondance du mot de passe
    if (password_verify($mdp, $hash_bdd)) {
        return true;
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        envoieDonnees("erreur", "Token de sécurité invalide");
        exit;
    }

    // Récupération des données
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $mdp = $_POST['password'] ?? '';

    // Validation basique
    if (empty($email) || empty($mdp)) {
        envoieDonnees("erreur", "Tous les champs sont requis");
        exit;
    }

    try {
        // Vérifie d'abord dans la table admin
        $stmt = $pdo->prepare("SELECT id_admin, mdp FROM admin WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if (verifierConnexion($mdp, $admin['mdp'])) {
                // Régénération de l'ID de session
                session_regenerate_id(true);

                // Configuration de la session pour admin
                $_SESSION['admin_id'] = $admin['id_admin'];
                $_SESSION['user_type'] = 'admin';
                $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
                $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

                envoieDonnees("succes", [
                    "message" => "Connexion administrateur réussie",
                    "redirect" => "/frontend/pages/admin/dashboard/dashboard.php"
                ]);
                exit;
            }
        }

        // Sinon, vérifie dans la table utilisateur
        $stmt = $pdo->prepare("SELECT id, password_hash, statut FROM utilisateur WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (verifierConnexion($mdp, $user['password_hash'])) {
                // Régénération de l'ID de session
                session_regenerate_id(true);

                // Configuration de la session pour utilisateur
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_type'] = 'utilisateur';
                $_SESSION['user_statut'] = $user['statut'];
                $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
                $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

                // Redirection basée sur le statut de l'utilisateur
                $redirect = $user['statut'] === 'Admin' ? 
                    '/frontend/pages/admin/dashboard/dashboard.php' : 
                    '/frontend/pages/accueil/accueil.php';

                envoieDonnees("succes", [
                    "message" => "Connexion utilisateur réussie",
                    "redirect" => $redirect
                ]);
                exit;
            }
        }

        // Si on arrive ici, c'est que ni admin ni utilisateur n'a été trouvé ou mot de passe incorrect
        envoieDonnees("erreur", "Identifiants incorrects");

    } catch (PDOException $e) {
        error_log("PDO Error: " . $e->getMessage());
        envoieDonnees("erreur", "Erreur système");
    }
}