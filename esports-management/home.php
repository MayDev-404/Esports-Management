<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ESportify - Professional Esports Management Platform</title>
  <link rel="stylesheet" href="css/home.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>
<body>
  <header>
    <div class="logo">
      <img src="images/logo.png" alt="ESportify Logo">
    </div>
    <nav>
      <a href="#about"><i class="fas fa-info-circle"></i> About</a>
      <a href="#features"><i class="fas fa-star"></i> Features</a>
      <a href="#contact"><i class="fas fa-envelope"></i> Contact</a>
      <a href="login.php" class="login-btn"><i class="fas fa-sign-in-alt"></i> Login</a>
      <a href="signup.php" class="signup-btn pulse-btn"><i class="fas fa-user-plus"></i> Sign Up</a>
    </nav>
  </header>

  <section class="hero">
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <h1>ESportify</h1>
      <p class="tagline">The Ultimate Esports Management Platform</p>
      <p class="hero-description">Organize tournaments, manage teams, and elevate your competitive gaming experience</p>
      <div class="hero-buttons">
        <a href="signup.php" class="cta-btn primary-btn"><i class="fas fa-rocket"></i> Get Started</a>
        <a href="#features" class="cta-btn secondary-btn"><i class="fas fa-info-circle"></i> Learn More</a>
      </div>
    </div>
    <div class="hero-stats">
      <div class="stat">
        <span class="number">10+</span>
        <span class="label">Tournaments</span>
      </div>
      <div class="stat">
        <span class="number">10+</span>
        <span class="label">Teams</span>
      </div>
      <div class="stat">
        <span class="number">10+</span>
        <span class="label">Players</span>
      </div>
    </div>
  </section>

  <section id="about" class="about">
    <div class="section-header">
      <h2>About ESportify</h2>
      <div class="accent-line"></div>
    </div>
    <div class="about-content">
      <div class="about-image">
        <img src="images/esports-background.jpg" alt="Esports Team">
      </div>
      <div class="about-text">
        <h3>Revolutionizing Esports Management</h3>
        <p>
          ESportify is the premier platform designed specifically for the competitive gaming ecosystem. We empower tournament organizers, 
          team managers, players, and spectators with powerful tools to streamline every aspect of esports competition.
        </p>
        <p>
          Our platform combines cutting-edge technology with intuitive design to create a seamless experience 
          that allows the esports community to focus on what really matters - the games.
        </p>
        <div class="about-highlights">
          <div class="highlight">
            <i class="fas fa-users"></i>
            <span>For Everyone</span>
          </div>
          <div class="highlight">
            <i class="fas fa-lock"></i>
            <span>Secure</span>
          </div>
          <div class="highlight">
            <i class="fas fa-bolt"></i>
            <span>Fast</span>
          </div>
          <div class="highlight">
            <i class="fas fa-chart-line"></i>
            <span>Scalable</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="games">
    <div class="games-container">
      <div class="game-icon">
        <i class="fas fa-gamepad"></i>
        <span>BGMI</span>
      </div>
      <div class="game-icon">
        <i class="fas fa-crosshairs"></i>
        <span>CS:GO</span>
      </div>
      <div class="game-icon">
        <i class="fas fa-chess"></i>
        <span>Dota 2</span>
      </div>
      <div class="game-icon">
        <i class="fas fa-fist-raised"></i>
        <span>Valorant</span>
      </div>
      <div class="game-icon">
        <i class="fas fa-car-side"></i>
        <span>Rocket League</span>
      </div>
      <div class="game-icon">
        <i class="fas fa-futbol"></i>
        <span>FIFA</span>
      </div>
    </div>
  </section>

  <section id="features" class="features">
    <div class="section-header">
      <h2>Platform Features</h2>
      <div class="accent-line"></div>
    </div>
    <div class="feature-container">
      <div class="feature-item">
        <div class="feature-icon">
          <i class="fas fa-trophy"></i>
        </div>
        <h3>Tournament Management</h3>
        <p>Create and manage tournaments with customizable brackets, schedules, and prize pools.</p>
        <ul class="feature-list">
          <li>Automated bracketing</li>
          <li>Custom tournament formats</li>
          <li>Prize management</li>
        </ul>
      </div>
      <div class="feature-item">
        <div class="feature-icon">
          <i class="fas fa-users"></i>
        </div>
        <h3>Team Management</h3>
        <p>Organize teams, manage rosters, and track player performance statistics.</p>
        <ul class="feature-list">
          <li>Team profiles and branding</li>
          <li>Player stats tracking</li>
          <li>Role management</li>
        </ul>
      </div>
      <div class="feature-item">
        <div class="feature-icon">
          <i class="fas fa-video"></i>
        </div>
        <h3>Live Streaming</h3>
        <p>Integrate with streaming platforms to broadcast matches directly to your audience.</p>
        <ul class="feature-list">
          <li>Multi-platform integration</li>
          <li>Viewer analytics</li>
          <li>Match highlights</li>
        </ul>
      </div>
      <div class="feature-item">
        <div class="feature-icon">
          <i class="fas fa-chart-bar"></i>
        </div>
        <h3>Stats & Analytics</h3>
        <p>Track comprehensive statistics for players and teams across all tournaments.</p>
        <ul class="feature-list">
          <li>Performance metrics</li>
          <li>Historical data</li>
          <li>Visual reporting</li>
        </ul>
      </div>
      <div class="feature-item">
        <div class="feature-icon">
          <i class="fas fa-user-shield"></i>
        </div>
        <h3>Role-Based Access</h3>
        <p>Different dashboards and permissions for admins, team managers, players, and spectators.</p>
        <ul class="feature-list">
          <li>Custom permissions</li>
          <li>Secure authentication</li>
          <li>User management</li>
        </ul>
      </div>
      <div class="feature-item">
        <div class="feature-icon">
          <i class="fas fa-medal"></i>
        </div>
        <h3>Match Results</h3>
        <p>Record and display match outcomes with detailed scorecards and highlights.</p>
        <ul class="feature-list">
          <li>Real-time updates</li>
          <li>Match history</li>
          <li>Tournament progression</li>
        </ul>
      </div>
    </div>
  </section>

  <section class="user-types">
    <div class="section-header">
      <h2>Built For Everyone</h2>
      <div class="accent-line"></div>
    </div>
    <div class="user-types-container">
      <div class="user-type">
        <div class="user-icon admin-icon">
          <i class="fas fa-user-cog"></i>
        </div>
        <h3>Tournament Admins</h3>
        <p>Powerful tools to create and manage tournaments with complete control.</p>
      </div>
      <div class="user-type">
        <div class="user-icon manager-icon">
          <i class="fas fa-user-tie"></i>
        </div>
        <h3>Team Managers</h3>
        <p>Manage your roster, track performance, and register for tournaments.</p>
      </div>
      <div class="user-type">
        <div class="user-icon player-icon">
          <i class="fas fa-user-astronaut"></i>
        </div>
        <h3>Players</h3>
        <p>View your stats, match schedules, and communicate with your team.</p>
      </div>
      <div class="user-type">
        <div class="user-icon spectator-icon">
          <i class="fas fa-user"></i>
        </div>
        <h3>Spectators</h3>
        <p>Follow tournaments, teams, and players with live updates and streams.</p>
      </div>
    </div>
  </section>

  <section class="cta-section">
    <div class="cta-content">
      <h2>Ready to elevate your esports experience?</h2>
      <p>Join thousands of esports professionals already using ESportify</p>
      <div class="cta-buttons">
        <a href="signup.php" class="cta-btn primary-btn"><i class="fas fa-rocket"></i> Get Started Now</a>
        <a href="login.php" class="cta-btn secondary-btn"><i class="fas fa-sign-in-alt"></i> Login</a>
      </div>
    </div>
  </section>

  <section id="contact" class="contact">
    <div class="section-header">
      <h2>Contact Us</h2>
      <div class="accent-line"></div>
    </div>
    <div class="contact-content">
      <div class="contact-info">
        <div class="contact-item">
          <i class="fas fa-envelope"></i>
          <p>Email: <a href="mailto:adityaverma25616@gmail.com">adityaverma25616@gmail.com</a></p>
        </div>
        <div class="contact-item">
          <i class="fas fa-headset"></i>
          <p>Support available 24/7</p>
        </div>
        <div class="social-links">
          <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
          <a href="#" class="social-icon"><i class="fab fa-discord"></i></a>
          <a href="#" class="social-icon"><i class="fab fa-twitch"></i></a>
          <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
        </div>
      </div>
    </div>
  </section>

  <footer>
    <div class="footer-content">
      <div class="footer-logo">
        <img src="images/banner.jpg" alt="ESportify Logo">
        <p><span>ESportify</span> - Professional Esports Management</p>
      </div>
      <div class="footer-links">
        <a href="#about">About</a>
        <a href="#features">Features</a>
        <a href="#contact">Contact</a>
        <a href="privacy.php">Privacy Policy</a>
        <a href="terms.php">Terms of Service</a>
      </div>
      <div class="footer-icons">
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-discord"></i></a>
        <a href="#"><i class="fab fa-twitch"></i></a>
        <a href="#"><i class="fab fa-youtube"></i></a>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; 2025 ESportify. All Rights Reserved.</p>
    </div>
  </footer>

  <script src="js/script.js"></script>
</body>
</html>