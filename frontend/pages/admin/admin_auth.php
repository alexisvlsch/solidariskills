<?php
// Vérification centralisée des droits d'accès aux pages d'administration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_is_admin = (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') ||
             (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'utilisateur' &&
              isset($_SESSION['user_statut']) && $_SESSION['user_statut'] === 'Admin');

if (!$_is_admin) {
    header('Location: /frontend/pages/connexion_inscription/auth.php');
    exit;
}
