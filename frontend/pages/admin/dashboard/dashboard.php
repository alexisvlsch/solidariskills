<?php
require_once('../admin_auth.php');

// Connexion à la base de données
require_once('../../../../backend/config.php');


$nbUtilisateurs = $pdo->query("SELECT COUNT(*) FROM utilisateur")->fetchColumn();
$nbActivites = $pdo->query("SELECT COUNT(*) FROM activite")->fetchColumn();
$nbFeedbacks = $pdo->query("SELECT COUNT(*) FROM feedback")->fetchColumn();
$nbInscriptions = $pdo->query("SELECT COUNT(*) FROM reserver")->fetchColumn();

?>




<!-- page Dashboard -->
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Administrateur</title>
  <link rel="stylesheet" href="dashboard.css">
  <link rel="stylesheet" href="../style-global-admin.css">
</head>

<body>


  <div class="conteneur">
    <!-- Barre latérale -->
    <?php include("../barreLaterale.php"); ?>


    <!-- Partie principale -->
    <div class="principal">

      <!-- Barre du haut -->
      <?php $pageTitle = "Dashboard"; ?>
      <?php include("../barreHaut.php"); ?>
 

      <!-- Section d'informations principales -->
      <section class="statistiques">
        <h2 class="titre-infos">Statistiques</h2>
      
        <div class="bloc-statistiques">
          <div class="grille-statistiques">
      
            <div class="carte-statistique">
              <div class="cercle-container">
                <div class="cercle-centre">
                  <span class="nombre"><?= $nbUtilisateurs ?></span>
                </div>
              </div>
              <p>Utilisateurs</p>
            </div>
      
            <div class="carte-statistique">
              <div class="cercle-container">
                <div class="cercle-centre">
                  <span class="nombre"><?= $nbActivites ?></span>
                </div>
              </div>
              <p>Activités planifiées</p>
            </div>
      
            <div class="carte-statistique">
              <div class="cercle-container">
                <div class="cercle-centre">
                  <span class="nombre"><?= $nbFeedbacks ?></span>
                </div>
              </div>
              <p>Feedbacks donnés</p>
            </div>
      
            <div class="carte-statistique">
              <div class="cercle-container">
                <div class="cercle-centre">
                  <span class="nombre"><?= $nbInscriptions ?></span>
                </div>
              </div>
              <p>Inscriptions/semaine</p>
            </div>
      
          </div>
        </div>
      </section>
            

    </div>
  </div>

   
</body>

</html>
