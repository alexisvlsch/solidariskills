<?php
$previous = $_SERVER['HTTP_REFERER'] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Politique de Confidentialité – SolidariSkills</title>
  <link rel="icon" href="/frontend/images/logoBE.png" type="image/png">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, sans-serif;
      margin: 2rem auto;
      max-width: 900px;
      padding: 1.5rem;
      line-height: 1.7;
      background-color: #f8fdfd;
      color: #222;
    }
    header {
      text-align: center;
      margin-bottom: 2rem;
    }
    header img {
      width: 70px;
      margin-bottom: 10px;
    }
    h1 {
      color: #006D6F;
      font-size: 28px;
      margin-bottom: 1rem;
    }
    h2 {
      color: #006D6F;
      margin-top: 2rem;
      font-size: 20px;
    }
    ul {
      margin-left: 1.5rem;
      list-style: disc;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.95rem;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 0.6rem;
      text-align: left;
    }
    th {
      background-color: #e6f2f3;
    }
    a {
      color: #006D6F;
      text-decoration: none;
    }
    a:hover {
      text-decoration: underline;
    }
    .updated {
      text-align: center;
      margin-top: 3rem;
      font-size: 14px;
      color: #555;
    }
    .btn-wrapper {
      text-align: center;
      margin-top: 2.5rem;
    }
    .back-button {
      display: inline-block;
      padding: 10px 18px;
      background-color: #006D6F;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 14px;
      cursor: pointer;
      text-decoration: none;
    }
    .back-button:hover {
      background-color: #004f50;
    }
  </style>
</head>
<body>
  <header>
    <img src="/frontend/images/logoBE.png" alt="Logo Solidariskills">
    <h1>Politique de Confidentialité</h1>
  </header>

  <p>
    Cette politique de confidentialité explique comment <strong>SolidariSkills</strong> collecte, utilise, stocke et protège les données personnelles des utilisateurs. Elle s'inscrit dans le respect du Règlement Général sur la Protection des Données (RGPD) et de la loi Informatique & Libertés.
  </p>

  <h2>1. Responsable de traitement</h2>
  <p>Association SolidariSkills – <a href="mailto:solidariskills@gmail.com">solidariskills@gmail.com</a></p>

  <h2>2. Données collectées</h2>
  <ul>
    <li><strong>Identité :</strong> nom d'utilisateur, adresse e-mail, photo de profil.</li>
    <li><strong>Profil utilisateur :</strong> bio, compétences renseignées, badges obtenus, contacts ajoutés.</li>
    <li><strong>Activités :</strong> créées ou rejointes, messages échangés, avis publiés.</li>
    <li><strong>Navigation :</strong> adresse IP, type de navigateur, durée de session, pages consultées.</li>
    <li><strong>Localisation :</strong> données de ville, code postal ou zone, uniquement pour proposer des activités proches géographiquement.</li>
  </ul>

  <h2>3. Finalités et bases légales</h2>
  <table>
    <tr>
      <th>Finalité du traitement</th>
      <th>Base légale</th>
    </tr>
    <tr>
      <td>Permettre la création et la gestion du compte utilisateur, ainsi que les fonctionnalités liées aux activités (inscriptions, badges, échanges, messagerie)</td>
      <td>Exécution du contrat (acceptation des CGU)</td>
    </tr>
    <tr>
      <td>Envoyer des messages liés à l’activité (confirmation, rappel, annulation)</td>
      <td>Intérêt légitime</td>
    </tr>
    <tr>
      <td>Produire des statistiques d’usage anonymisées pour améliorer la plateforme</td>
      <td>Intérêt légitime</td>
    </tr>
    <tr>
      <td>Assurer la sécurité des comptes, détecter les comportements abusifs ou suspects</td>
      <td>Intérêt légitime</td>
    </tr>
  </table>


  <h2>4. Durée de conservation</h2>
  <ul>
    <li>Compte : tant qu’il est actif, puis 12 mois après suppression.</li>
    <li>Historique des activités, messages, avis : 3 ans après la dernière connexion.</li>
    <li>Logs de connexion : 12 mois.</li>
  </ul>

  <h2>5. Vos droits</h2>
  <p>
    Vous pouvez accéder à vos données, les rectifier, les supprimer, en limiter le traitement, vous y opposer ou demander leur portabilité. Pour cela, contactez : <a href="mailto:solidariskills@gmail.com">solidariskills@gmail.com</a>
  </p>

  <h2>6. Transfert de données hors UE</h2>
  <p>
    Vos données sont stockées sur des serveurs sécurisés situés en France. Aucun transfert hors de l'Union européenne n'est effectué. En cas de changement, les clauses contractuelles types (CCT) de la Commission européenne seront appliquées.
  </p>

  <h2>7. Réclamations</h2>
  <p>
    Si vous estimez que vos droits ne sont pas respectés, vous pouvez introduire une réclamation auprès de la <strong>CNIL</strong> (www.cnil.fr) ou de votre autorité nationale de contrôle.
  </p>

  <p class="updated">Dernière mise à jour : <?= date('d/m/Y'); ?></p>

  <div class="btn-wrapper">
    <?php if ($previous): ?>
      <a href="<?= htmlspecialchars($previous) ?>" class="back-button">&larr; Retour à la page précédente</a>
    <?php endif; ?>
  </div>
</body>
</html>
