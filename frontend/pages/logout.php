<?php
// Déconnecte l'utilisateur et détruit la session
session_start();
$_SESSION = array();
session_destroy();
header("Location: /frontend/pages/connexion_inscription/auth.php");
exit();
?>