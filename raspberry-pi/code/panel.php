<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// Include common functions
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';

// Database connection

// Set page specific CSS and JS
$page_css = 'css/panel.css';
$page_js = 'js/panel.js';
$show_stats = true;

// Get user role and ID
$userId = $_SESSION['user_id'];
// Check if rol_id exists, otherwise use user_role
if (isset($_SESSION['rol_id'])) {
  $rolId = $_SESSION['rol_id'];
  $roleName = getUserRoleName($rolId);
} else {
  // If rol_id is not set, use user_role directly if available
  $roleName = $_SESSION['user_role'] ?? 'Usuario';
  // Try to get rol_id from database if needed
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

// Get user stats based on role
$totalUsers = 0;
$totalRegistros = 0;
$registrosHoy = 0;
$horasTrabajadas = 0;

// Current date
$today = date('Y-m-d');

// Get stats based on role and permissions
if (hasPermission('Ver todos los fichajes')) {
  // Get total users
  $userQuery = "SELECT COUNT(*) as total FROM usuarios";
  $userResult = $conn->query($userQuery);
  if ($userResult && $row = $userResult->fetch_assoc()) {
    $totalUsers = $row['total'];
  }

  // Get total registros
  $registrosQuery = "SELECT COUNT(*) as total FROM sesiones_fichaje";
  $registrosResult = $conn->query($registrosQuery);
  if ($registrosResult && $row = $registrosResult->fetch_assoc()) {
    $totalRegistros = $row['total'];
  }

  // Get registros today
  $hoyQuery = "SELECT COUNT(*) as total FROM sesiones_fichaje WHERE DATE(fecha_entrada) = '$today'";
  $hoyResult = $conn->query($hoyQuery);
  if ($hoyResult && $row = $hoyResult->fetch_assoc()) {
    $registrosHoy = $row['total'];
  }
} else {
  // For regular employees, only get their own stats
  $registrosQuery = "SELECT COUNT(*) as total FROM sesiones_fichaje WHERE usuario_id = $userId";
  $registrosResult = $conn->query($registrosQuery);
  if ($registrosResult && $row = $registrosResult->fetch_assoc()) {
    $totalRegistros = $row['total'];
  }

  // Get employee's registros today
  $hoyQuery = "SELECT COUNT(*) as total FROM sesiones_fichaje WHERE usuario_id = $userId AND DATE(fecha_entrada) = '$today'";
  $hoyResult = $conn->query($hoyQuery);
  if ($hoyResult && $row = $hoyResult->fetch_assoc()) {
    $registrosHoy = $row['total'];
  }

  // Get employee's total hours worked this month
  $mesActual = date('Y-m');
  $horasQuery = "SELECT SUM(TIMESTAMPDIFF(HOUR, fecha_entrada, fecha_salida)) as total_horas 
              FROM sesiones_fichaje 
              WHERE usuario_id = $userId 
              AND DATE_FORMAT(fecha_entrada, '%Y-%m') = '$mesActual' 
              AND fecha_salida IS NOT NULL";
  $horasResult = $conn->query($horasQuery);
  if ($horasResult && $row = $horasResult->fetch_assoc()) {
    $horasTrabajadas = $row['total_horas'] ?: 0;
  }
}

// Get recent activity
$recentActivity = array();
if (hasPermission('Ver todos los fichajes')) {
  $activityQuery = "SELECT sf.fecha_entrada, sf.fecha_salida, u.nombre, u.apellido 
                 FROM sesiones_fichaje sf 
                 JOIN usuarios u ON sf.usuario_id = u.id 
                 ORDER BY sf.fecha_entrada DESC LIMIT 5";
} else {
  $activityQuery = "SELECT sf.fecha_entrada, sf.fecha_salida, u.nombre, u.apellido 
                 FROM sesiones_fichaje sf 
                 JOIN usuarios u ON sf.usuario_id = u.id 
                 WHERE sf.usuario_id = $userId 
                 ORDER BY sf.fecha_entrada DESC LIMIT 5";
}

$activityResult = $conn->query($activityQuery);
if ($activityResult) {
  while ($row = $activityResult->fetch_assoc()) {
    $recentActivity[] = $row;
  }
}

// Get today's attendance summary
$todayAttendance = array();
if (hasPermission('Ver todos los fichajes')) {
  $todayQuery = "SELECT u.nombre, u.apellido, sf.fecha_entrada, sf.fecha_salida
                FROM sesiones_fichaje sf
                JOIN usuarios u ON sf.usuario_id = u.id
                WHERE DATE(sf.fecha_entrada) = '$today'
                ORDER BY sf.fecha_entrada DESC";
  $todayResult = $conn->query($todayQuery);
  if ($todayResult) {
    while ($row = $todayResult->fetch_assoc()) {
      $todayAttendance[] = $row;
    }
  }
}

// Include header
include 'includes/header.php';
?>

<section class="welcome-section">
  <div class="card">
    <div class="card-header">
      <h2 class="card-title">Welcome, <?php echo $_SESSION['user_name']; ?></h2>
      <span class="badge badge-primary"><?php echo $roleName; ?></span>
    </div>
    <div class="welcome-content">
      <p>Welcome to the Attendance Control System dashboard. From here, you can manage all functions related to clock-in and clock-out records.</p>
      
      <div class="quick-stats">
        <div class="stat-card animate-on-scroll">
          <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
              <line x1="16" y1="2" x2="16" y2="6"></line>
              <line x1="8" y1="2" x2="8" y2="6"></line>
              <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
          </div>
          <div class="stat-info">
            <h3>Today</h3>
            <p class="stat-value"><?php echo $registrosHoy; ?></p>
            <p class="stat-label">Records</p>
          </div>
        </div>
        
        <div class="stat-card animate-on-scroll" style="animation-delay: 100ms;">
          <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
            </svg>
          </div>
          <div class="stat-info">
            <h3>Total</h3>
            <p class="stat-value"><?php echo $totalRegistros; ?></p>
            <p class="stat-label">Records</p>
          </div>
        </div>
        
        <div class="stat-card animate-on-scroll" style="animation-delay: 200ms;">
          <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
          </div>
          <div class="stat-info">
            <h3><?php echo $roleName === 'Administrador' ? 'Users' : 'Hours'; ?></h3>
            <p class="stat-value"><?php echo $roleName === 'Administrador' ? $totalUsers : $horasTrabajadas; ?></p>
            <p class="stat-label"><?php echo $roleName === 'Administrador' ? 'Registered' : 'This month'; ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="dashboard-widgets">
  <?php if (hasPermission('Gestionar usuarios')): ?>
  <!-- Admin and HR specific widgets -->
  <div class="widget-row">
    <?php if ($roleName === 'Administrador'): ?>
    <div class="card widget animate-on-scroll">
      <div class="widget-header">
        <h3>User Management</h3>
        <a href="usuarios.php" class="widget-action">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </a>
      </div>
      <div class="widget-content">
        <div class="widget-stat">
          <span class="widget-stat-value"><?php echo $totalUsers; ?></span>
            <span class="widget-stat-label">Active Users</span>
          </div>
          <div class="widget-actions">
            <a href="usuarios.php" class="btn btn-sm btn-primary">View Users</a>
            <a href="usuarios.php?action=new" class="btn btn-sm btn-secondary">Add User</a>
        </div>
      </div>
    </div>
    
    <div class="card widget animate-on-scroll" style="animation-delay: 100ms;">
      <div class="widget-header">
        <h3>RFID Cards</h3>
        <a href="tarjetas.php" class="widget-action">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </a>
      </div>
      <div class="widget-content">
        <div class="widget-stat">
          <?php
          // Get count of assigned RFID cards
          $rfidQuery = "SELECT COUNT(*) as total FROM tarjetas WHERE usuario_id IS NOT NULL";
          $rfidResult = $conn->query($rfidQuery);
          $assignedCards = 0;
          if ($rfidResult && $row = $rfidResult->fetch_assoc()) {
            $assignedCards = $row['total'];
          }
          ?>
          <span class="widget-stat-value"><?php echo $assignedCards; ?></span>
            <span class="widget-stat-label">Assigned Cards</span>
          </div>
          <div class="widget-actions">
            <a href="tarjetas.php" class="btn btn-sm btn-primary">View Cards</a>
            <a href="tarjetas.php?action=assign" class="btn btn-sm btn-secondary">Assign Card</a>
        </div>
      </div>
    </div>
    <?php endif; ?>
    
    <?php if ($roleName === 'Recursos Humanos'): ?>
    <div class="card widget animate-on-scroll">
      <div class="widget-header">
        <h3>Resumen Diario</h3>
        <a href="index.php" class="widget-action">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </a>
      </div>
      <div class="widget-content">
        <div class="widget-stat">
          <span class="widget-stat-value"><?php echo $registrosHoy; ?></span>
            <span class="widget-stat-label">Today's Records</span>
          </div>
          <div class="widget-actions">
            <a href="index.php" class="btn btn-sm btn-primary">View Records</a>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <!-- Widgets for all users -->
  <div class="widget-row">
    <div class="card widget animate-on-scroll" style="animation-delay: 200ms;">
      <div class="widget-header">
        <h3>My Records</h3>
        <a href="index.php" class="widget-action">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </a>
      </div>
      <div class="widget-content">
        <div class="widget-stat">
          <span class="widget-stat-value"><?php echo $totalRegistros; ?></span>
            <span class="widget-stat-label">Total Records</span>
          </div>
          <div class="widget-actions">
            <a href="index.php" class="btn btn-sm btn-primary">View My Records</a>
        </div>
      </div>
    </div>
    
    <?php if (hasPermission('Ver todos los fichajes')): ?>
    <div class="card widget animate-on-scroll" style="animation-delay: 300ms;">
      <div class="widget-header">
        <h3>Statistics</h3>
        <a href="estadisticas.php" class="widget-action">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </a>
      </div>
      <div class="widget-content">
        <div class="widget-stat">
          <span class="widget-stat-value"><?php echo $horasTrabajadas; ?>h</span>
            <span class="widget-stat-label">Hours worked</span>
          </div>
          <div class="widget-actions">
            <a href="estadisticas.php" class="btn btn-sm btn-primary">View Statistics</a>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- Today's Attendance Summary -->
<?php if (hasPermission('Ver todos los fichajes') && !empty($todayAttendance)): ?>
<section class="today-attendance">
  <div class="card">
    <div class="card-header">
      <h2 class="card-title">Today's Attendance</h2>
      <span class="refresh-info" id="last-refresh">Last updated: <?php echo date('H:i:s'); ?></span>
    </div>
    <div class="table-container">
      <table id="todayAttendanceTable">
      <thead>
        <tr>
        <th>Name</th>
        <th>Entry</th>
        <th>Exit</th>
        <th>Status</th>
        </tr>
        </thead>
        <tbody>
          <?php foreach ($todayAttendance as $record): ?>
          <tr>
            <td data-label="Nombre"><?php echo $record['nombre'] . ' ' . $record['apellido']; ?></td>
            <td data-label="Entrada"><?php echo date('H:i:s', strtotime($record['fecha_entrada'])); ?></td>
            <td data-label="Salida"><?php echo $record['fecha_salida'] ? date('H:i:s', strtotime($record['fecha_salida'])) : '-'; ?></td>
            <td data-label="Estado">
              <span class="badge <?php echo $record['fecha_salida'] ? 'badge-success' : 'badge-warning'; ?>">
                <?php echo $record['fecha_salida'] ? 'Completo' : 'En curso'; ?>
              </span>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="recent-activity">
  <div class="card">
    <div class="card-header">
      <h2 class="card-title">Recent Activity</h2>
    </div>
    <div class="activity-list">
      <?php if (empty($recentActivity)): ?>
      <div class="empty-state">
        <p>There is no recent activity to display.</p>
        </div>
      <?php else: ?>
        <?php foreach ($recentActivity as $index => $activity): ?>
          <div class="activity-item animate-on-scroll" style="animation-delay: <?php echo $index * 100; ?>ms;">
            <div class="activity-icon">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <?php if ($activity['fecha_salida']): ?>
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                  <polyline points="14 2 14 8 20 8"></polyline>
                  <line x1="16" y1="13" x2="8" y2="13"></line>
                  <line x1="16" y1="17" x2="8" y2="17"></line>
                  <polyline points="10 9 9 9 8 9"></polyline>
                <?php else: ?>
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                <?php endif; ?>
              </svg>
            </div>
            <div class="activity-content">
              <div class="activity-header">
                <span class="activity-title">
                    <?php echo $activity['fecha_salida'] ? 'Exit recorded' : 'Entry recorded'; ?>
                  <?php if (hasPermission('Ver todos los fichajes')): ?>
                    for <?php echo $activity['nombre'] . ' ' . $activity['apellido']; ?>
                  <?php endif; ?>
                </span>
                <span class="activity-time">
                  <?php 
                  $date = $activity['fecha_salida'] ? new DateTime($activity['fecha_salida']) : new DateTime($activity['fecha_entrada']);
                  echo $date->format('d/m/Y H:i:s'); 
                  ?>
                </span>
              </div>
              <p class="activity-description">
                <?php if ($activity['fecha_salida']): ?>
                  The exit has been successfully recorded.
                <?php else: ?>
                  The entry has been successfully recorded.
                <?php endif; ?>
              </p>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Auto-refresh script for today's attendance -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh today's attendance table every 60 seconds
    setInterval(function() {
      fetch('get_today_attendance.php')
        .then(response => response.json())
        .then(data => {
          const tableBody = document.querySelector('#todayAttendanceTable tbody');
          if (tableBody) {
            // Clear existing rows
            tableBody.innerHTML = '';
            
            // Add new rows
            data.forEach(record => {
              const row = document.createElement('tr');
              
              const nameCell = document.createElement('td');
              nameCell.setAttribute('data-label', 'Nombre');
              nameCell.textContent = record.nombre + ' ' + record.apellido;
              
              const entryCell = document.createElement('td');
              entryCell.setAttribute('data-label', 'Entrada');
              entryCell.textContent = new Date(record.fecha_entrada).toLocaleTimeString();
              
              const exitCell = document.createElement('td');
              exitCell.setAttribute('data-label', 'Salida');
              exitCell.textContent = record.fecha_salida ? new Date(record.fecha_salida).toLocaleTimeString() : '-';
              
              const statusCell = document.createElement('td');
              statusCell.setAttribute('data-label', 'Estado');
              const badge = document.createElement('span');
              badge.className = 'badge ' + (record.fecha_salida ? 'badge-success' : 'badge-warning');
                badge.textContent = record.fecha_salida ? 'Complete' : 'In progress';
              statusCell.appendChild(badge);
              
              row.appendChild(nameCell);
              row.appendChild(entryCell);
              row.appendChild(exitCell);
              row.appendChild(statusCell);
              
              tableBody.appendChild(row);
            });
            
            // Update last refresh time
            document.getElementById('last-refresh').textContent = 'Last updated: ' + new Date().toLocaleTimeString();
          }
        })
        .catch(error => console.error('Error refreshing attendance data:', error));
    }, 60000); // Refresh every 60 seconds
  });
</script>

<?php
// Include footer
include 'includes/footer.php';
?>
