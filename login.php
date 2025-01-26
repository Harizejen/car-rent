<?php
session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax'
]);

include 'db/db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Rest of the HTML remains the same -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cental - Next-Gen Car Rental</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    :root {
      --gradient-1: #6366f1;
      --gradient-2: #8b5cf6;
      --gradient-3: #d946ef;
    }

    .auth-container {
      min-height: 100vh;
      background: linear-gradient(135deg, var(--gradient-1), var(--gradient-2), var(--gradient-3));
      position: relative;
      overflow: hidden;
    }

    .particle {
      position: absolute;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      pointer-events: none;
    }

    .auth-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
    }

    .auth-card:hover {
      transform: translateY(-5px);
    }

    .form-control {
      border-radius: 0.75rem;
      padding: 1rem;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
    }

    .auth-switch {
      color: var(--gradient-2);
      transition: all 0.3s ease;
    }

    .auth-switch:hover {
      color: var(--gradient-3);
      transform: translateX(5px);
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--gradient-2), var(--gradient-3));
      border: none;
      padding: 1rem 2rem;
      border-radius: 0.75rem;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
  </style>
</head>

<body>
  <div class="auth-container">
    <div class="container">
      <div class="row min-vh-100 align-items-center justify-content-center">
        <div class="col-md-8 col-lg-6 col-xl-5">
          <div class="auth-card p-4 p-md-5">
            <div class="text-center mb-5">
              <h1 class="h2 fw-bold mb-3">Welcome Back</h1>
              <p class="text-muted">Experience premium car rental services</p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
              <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form action="controller/userLogin.php" method="POST">
            <input type="hidden" name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
              <!-- Rest of the form remains the same -->
              <div class="mb-4">
                <label class="form-label">Email</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                  <input type="email" name="email" class="form-control" required>
                </div>
              </div>

              <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-lock"></i></span>
                  <input type="password" name="password" class="form-control" required>
                </div>
              </div>

              <button type="submit" class="btn btn-primary w-100 mb-4">Sign In</button>

              <div class="text-center">
                <a href="#!" class="text-decoration-none auth-switch">
                  New to Cental? Create Account <i class="fas fa-arrow-right ms-2"></i>
                </a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Particle animation
    function createParticles() {
      const container = document.querySelector('.auth-container');
      for (let i = 0; i < 50; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.width = Math.random() * 5 + 2 + 'px';
        particle.style.height = particle.style.width;
        particle.style.left = Math.random() * 100 + '%';
        particle.style.top = Math.random() * 100 + '%';
        particle.style.animation = `float ${Math.random() * 10 + 5}s infinite`;
        container.appendChild(particle);
      }
    }
    createParticles();
  </script>
</body>

</html>