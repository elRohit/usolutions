<?php
// Start session
session_start();

// Check if already logged in
if (isset($_SESSION['user_id'])) {
  header('Location: panel.php');
  exit;
}

// Database connection
require_once 'includes/db_connection.php';

// Set page specific CSS and JS
$page_css = 'css/login.css';
$page_js = 'js/login.js';

// Process login form
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';

  // Simple validation
  if (empty($email) || empty($password)) {
    $error = 'Please fill in all the fields.';
  } else {
    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, nombre, apellido, password, rol_id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
      $user = $result->fetch_assoc();
      
      // Verify password (using password_verify for secure comparison)
      if (password_verify($password, $user['password'])) {
        // Get role name
        $role_stmt = $conn->prepare("SELECT nombre FROM roles WHERE id = ?");
        $role_stmt->bind_param("i", $user['rol_id']);
        $role_stmt->execute();
        $role_result = $role_stmt->get_result();
        $role = $role_result->fetch_assoc();
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['user_apellido'] = $user['apellido'];
        $_SESSION['user_email'] = $email;
        $_SESSION['rol_id'] = $user['rol_id'];
        $_SESSION['user_role'] = $role['nombre'];
        
        // Redirect to panel
        header('Location: panel.php');
        exit;
      } else {
        $error = 'Invalid credentials. Please try again.';
      }
        } else {
      $error = 'Invalid credentials. Please try again.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Attendance Control System</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
  <link rel="stylesheet" href="css/common.css">
  <link rel="stylesheet" href="<?php echo $page_css; ?>">
</head>
<body>
  <div id="tsparticles"></div>
  
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <div class="logo pulse">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
        </div>
        <h1>Attendance Control</h1>
        <p class="subtitle">Login</p>
      </div>
      
      <?php if (!empty($error)): ?>
      <div class="alert alert-danger">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="8" x2="12" y2="12"></line>
          <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <?php echo $error; ?>
      </div>
      <?php endif; ?>
      
      <form method="POST" action="login.php" class="login-form" id="loginForm">
        <div class="form-group">
          <label for="email" class="form-label">Email Address</label>
          <input type="email" id="email" name="email" class="form-control" placeholder="email@example.com" required>
          <div class="form-error" id="emailError"></div>
        </div>
        
        <div class="form-group">
          <label for="password" class="form-label">Password</label>
          <div class="password-input">
            <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
            <button type="button" id="togglePassword" class="toggle-password">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-off-icon hidden">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
              </svg>
            </button>
          </div>
          <div class="form-error" id="passwordError"></div>
        </div>
        
        <div class="form-group form-check">
          <input type="checkbox" id="remember" name="remember" class="form-check-input">
            <label for="remember" class="form-check-label">Remember Me</label>
          </div>
          
          <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
              <polyline points="10 17 15 12 10 7"></polyline>
              <line x1="15" y1="12" x2="3" y2="12"></line>
            </svg>
            Login
          </button>
        </div>
        
        <div class="login-footer">
            <a href="#" class="forgot-password">Forgot your password?</a>
        </div>
      </form>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/tsparticles-engine"></script>
  <script src="https://cdn.jsdelivr.net/npm/tsparticles"></script>
  <script src="js/common.js"></script>
  <script src="<?php echo $page_js; ?>"></script>
</body>
</html>
