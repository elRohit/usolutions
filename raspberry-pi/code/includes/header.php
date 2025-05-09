<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Include common functions
require_once 'includes/functions.php';

// Function to check if user is logged in
function isLoggedIn() {
  return isset($_SESSION['user_id']);
}

// Redirect to login if not logged in (except for login page)
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page !== 'login.php' && !isLoggedIn()) {
  header('Location: login.php');
  exit;
}

// Check role-based access for restricted pages
if (isLoggedIn()) {
  // Get role information
  if (isset($_SESSION['rol_id'])) {
    $rolId = $_SESSION['rol_id'];
    $roleName = getUserRoleName($rolId);
  } else {
    // If rol_id is not set, use user_role directly if available
    $roleName = $_SESSION['user_role'] ?? 'Usuario';
    
    // Try to get rol_id from database
    require_once 'includes/db_connection.php';
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT rol_id FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
      $_SESSION['rol_id'] = $row['rol_id']; // Set it for future use
      $rolId = $row['rol_id'];
    } else {
      $rolId = 0; // Default value if not found
    }
  }

  // Pages only accessible to Administrators
  $admin_only_pages = ['usuarios.php', 'tarjetas.php'];

  // Pages accessible to Administrators and HR
  $admin_hr_pages = ['estadisticas.php'];

  if (in_array($current_page, $admin_only_pages) && $roleName !== 'Administrador') {
    header('Location: panel.php');
    exit;
  }

  if (in_array($current_page, $admin_hr_pages) && $roleName !== 'Administrador' && $roleName !== 'Recursos Humanos') {
    header('Location: panel.php');
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Attendance Control System</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <!-- Page specific CSS -->
  <?php if (isset($page_css)): ?>
  <link rel="stylesheet" href="<?php echo $page_css; ?>">
  <?php endif; ?>
  <!-- Common CSS -->
  <link rel="stylesheet" href="css/common.css">
</head>
<body>
  <div id="tsparticles"></div>

  <?php if ($current_page !== 'login.php'): ?>
  <header class="app-header">
    <div class="container">
      <div class="header-content">
        <div class="header-top">
          <div class="logo-container">
            <div class="logo pulse">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
            </div>
            <div class="logo-text">
                <h1>Attendance Control</h1>
                <span class="subtitle">Entry and Exit Management System</span>
            </div>
          </div>
          
          <div class="current-time">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            <span id="current-time">
              <?php echo date('l, d F Y H:i:s'); ?>
            </span>
          </div>
        </div>
        
        <?php if (isset($show_stats) && $show_stats): ?>
        <div class="header-stats">
          <div class="stat-item">
            <div class="stat-value" id="total-registros"><?php echo isset($totalRegistros) ? $totalRegistros : '0'; ?></div>
            <div class="stat-label">Total Records</div>
          </div>
          <div class="stat-item">
            <div class="stat-value" id="registros-hoy"><?php echo isset($registrosHoy) ? $registrosHoy : '0'; ?></div>
            <div class="stat-label">Records Today</div>
          </div>
          <div class="stat-item">
            <div class="stat-value"><?php echo date('H:i'); ?></div>
            <div class="stat-label">Current Time</div>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <div class="container">
    <nav class="main-nav">
      <ul>
        <li><a href="index.php" class="<?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
          </svg>
          Time entries
        </a></li>
        <li><a href="panel.php" class="<?php echo $current_page === 'panel.php' ? 'active' : ''; ?>">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="3" y1="9" x2="21" y2="9"></line>
            <line x1="9" y1="21" x2="9" y2="9"></line>
          </svg>
          Panel
        </a></li>
        <?php if (hasPermission('Gestionar usuarios')): ?>
        <li><a href="usuarios.php" class="<?php echo $current_page === 'usuarios.php' ? 'active' : ''; ?>">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
          </svg>
          Users
        </a></li>
        <li><a href="tarjetas.php" class="<?php echo $current_page === 'tarjetas.php' ? 'active' : ''; ?>">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="5" width="20" height="14" rx="2"></rect>
            <line x1="2" y1="10" x2="22" y2="10"></line>
          </svg>
          RFID Cards
        </a></li>
        <?php endif; ?>
        <?php if (hasPermission('Ver todos los fichajes')): ?>
        <li><a href="estadisticas.php" class="<?php echo $current_page === 'estadisticas.php' ? 'active' : ''; ?>">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="20" x2="18" y2="10"></line>
            <line x1="12" y1="20" x2="12" y2="4"></line>
            <line x1="6" y1="20" x2="6" y2="14"></line>
          </svg>
          Statistics
        </a></li>
        <?php endif; ?>
        <li><a href="perfil.php" class="<?php echo $current_page === 'perfil.php' ? 'active' : ''; ?>">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
          </svg>
          My Profile
        </a></li>
        <li><a href="logout.php">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
          </svg>
          Logout
        </a></li>
      </ul>
    </nav>
    
    <main>
  <?php endif; ?>
