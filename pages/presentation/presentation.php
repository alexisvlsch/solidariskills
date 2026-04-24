<?php
// Gère le consentement cookies
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['accept_cookies'])) {
    setcookie('cookies_accepted', 'yes', time() + 365 * 24 * 60 * 60, '/');
    header("Refresh:0");
    exit;
  } elseif (isset($_POST['refuse_cookies'])) {
    setcookie('cookies_accepted', 'no', time() + 365 * 24 * 60 * 60, '/');
    header("Refresh:0");
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solidariskills - Plateforme de solidarité et compétences</title>
    <link rel="stylesheet" href="/frontend/header/header.css">
    <link rel="stylesheet" href="/frontend/footer/styles-footer.css">
    <link rel="stylesheet" href="/frontend/pages/presentation/styles-presentation.css">
    <link rel="stylesheet" href="/frontend/cookies/cookies-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include '../../cookies/cookies_banner.php'; ?>

    <nav class="navbar">
        <div class="navbar-content">
            <div class="logo-container">
                <img src="/frontend/images/logoBE.png" alt="Logo Solidariskills" class="logo-img">
                <span class="site-title">Solidariskills</span>
            </div>
            <div>
                <a href="/frontend/pages/connexion_inscription/auth.php" class="btn-primary">
                    <i class="fas fa-sign-in-alt icon-left"></i> Connexion / Inscription
                </a>
            </div>
        </div>
    </nav>

    <section class="hero-gradient hero-section">
        <div class="hero-content">
            <div class="hero-text">
                <h1>
                    Ensemble, partageons nos compétences <span class="highlight">solidairement</span>
                </h1>
                <p>
                    Solidariskills connecte les personnes désireuses d'échanger des services et des compétences dans un esprit d'entraide et de solidarité.
                </p>
                <div class="hero-btns">
                    <a href="#features" class="btn-primary btn-secondary">
                        <i class="fas fa-info-circle icon-left"></i> En savoir plus
                    </a>
                    <a href="/frontend/pages/connexion_inscription/auth.php" class="btn-primary">
                        <i class="fas fa-rocket icon-left"></i> Commencer maintenant
                    </a>
                </div>
            </div>
            <div class="hero-image">
                <img src="/frontend/images/logoBE.png" alt="Solidarité" class="hero-logo">
            </div>
        </div>
    </section>

    <section id="features" class="features-section">
        <div class="features-container">
            <h2>Comment ça marche ?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h3>1. S'inscrire</h3>
                    <p>Créez votre profil en quelques minutes et décrivez vos centres d'intérêt et compétences.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>2. Trouver ou créer</h3>
                    <p>Parcourez les activités disponibles ou proposez vos propres activités à la communauté.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3>3. Échanger</h3>
                    <p>Discutez avec les organisateurs pour obtenir plus de détails sur les activités.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>4. Suivre ses stats</h3>
                    <p>Consultez votre tableau de bord pour voir votre participation et impact.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="testimonials-section">
        <div class="testimonials-container">
            <h2>Témoignages</h2>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Marie">
                        <div>
                            <h4>Marie L.</h4>
                            <p class="testimonial-date">Membre depuis 2022</p>
                        </div>
                    </div>
                    <p class="testimonial-text">
                        "Grâce à Solidariskills, j'ai pu apprendre à jardiner en échange de cours d'anglais. C'est incroyable de voir comment on peut s'entraider sans argent !"
                    </p>
                    <div class="testimonial-stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Pierre">
                        <div>
                            <h4>Pierre D.</h4>
                            <p class="testimonial-date">Membre depuis 2023</p>
                        </div>
                    </div>
                    <p class="testimonial-text">
                        "J'ai aidé à réparer un vélo et en retour on m'a donné des cours de cuisine italienne. La communauté est bienveillante et les échanges très enrichissants."
                    </p>
                    <div class="testimonial-stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="cta-container">
            <h2>Prêt à rejoindre notre communauté solidaire ?</h2>
            <p>
                Des milliers de membres attendent de partager leurs compétences avec vous. Inscrivez-vous maintenant, c'est gratuit !
            </p>
            <a href="/frontend/pages/connexion_inscription/auth.php" class="btn-primary btn-cta">
                <i class="fas fa-user-plus icon-left"></i> S'inscrire gratuitement
            </a>
        </div>
    </section>

    <footer class="presentation-footer">
    <div class="footer-container">
        <div class="footer-brand">
            <img src="/frontend/images/logoBE.png" alt="Logo Solidariskills" class="footer-logo">
            <span class="footer-title">Solidariskills</span>
        </div>
        <div class="footer-links">
            <a href="/frontend/pages/conditions.php">Conditions Générales d'Utilisation</a>
            <a href="/frontend/pages/confidentialite.php">Politique de confidentialité</a>
        </div>
        <div class="footer-social">
            <a href="https://www.instagram.com/solidariskills" target="_blank" title="Instagram">
                <i class="fab fa-instagram"></i>
            </a>
        </div>
    </div>
    <div class="footer-copy">
        &copy; 2025 Solidariskills. Tous droits réservés.
    </div>
</footer>
</body>
</html>
<script>
  // Gère le scroll fluide sur la page de présentation
  if (window.location.hash === "#features") {
    history.replaceState("", document.title, window.location.pathname + window.location.search);
  }
  document.querySelectorAll('.scroll-link').forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({ behavior: 'smooth' });
      }
    });
  });
</script>
