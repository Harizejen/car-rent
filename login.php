<?php
include 'db/db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  try {
      if (empty($_POST['email']) || empty($_POST['password'])) {
          throw new Exception("Email and password are required");
      }

      $query = "SELECT CLIENT_ID, CLIENT_NAME, PASSWORD 
              FROM CARRENTAL.CLIENTS 
              WHERE EMAIL = :email";
      
      $stmt = oci_parse($conn, $query);
      oci_bind_by_name($stmt, ":email", $_POST['email']);
      oci_execute($stmt);

      if ($user = oci_fetch_assoc($stmt)) {
          if (password_verify($_POST['password'], $user['PASSWORD'])) {
              $_SESSION['client_id'] = $user['CLIENT_ID'];
              $_SESSION['client_name'] = $user['CLIENT_NAME'];
              header("Location: userDashboard.php");
              exit;
          } else {
              throw new Exception("Invalid credentials");
          }
      } else {
          throw new Exception("User  not found");
      }

  } catch (Exception $e) {
      $_SESSION['error'] = $e->getMessage();
      header("Location: index.php");
      exit;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
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
                            <img src="logo.png" alt="Cental Logo" class="mb-4" style="height: 50px;">
                            <h1 class="h2 fw-bold mb-3">Welcome Back</h1>
                            <p class="text-muted">Experience premium car rental services</p>
                        </div>

                        <?php if(isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>

                        <form method="POST">
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
            for(let i = 0; i < 50; i++) {
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