<?php
// Bannière affichée uniquement si l'utilisateur n'a pas encore accepté les cookies
if (!isset($_COOKIE['cookies_accepted'])):
?>
<div class="cookie-banner">
  <form method="POST" action="">
    <p class="cookie-message">
      Ce site utilise des cookies pour améliorer votre expérience utilisateur.  
      En cliquant sur « Accepter », vous consentez à leur utilisation.
    </p>
    <div class="cookie-actions">
      <button type="submit" name="accept_cookies" class="btn-accept">Accepter</button>
      <button type="submit" name="refuse_cookies" class="btn-refuse">Refuser</button>
      <a href="/frontend/pages/confidentialite.php" class="cookie-link">En savoir plus</a>
    </div>
  </form>
</div>
<?php endif; ?>
