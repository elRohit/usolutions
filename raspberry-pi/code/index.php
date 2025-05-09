<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// Include common functions
require_once 'includes/functions.php';

// Database connection
require_once 'includes/db_connection.php';

// Set page specific CSS and JS
$page_css = 'css/index.css';
$page_js = 'js/index.js';
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

// Configurar zona horaria
date_default_timezone_set('Europe/Madrid');
$hoy = date('Y-m-d');

// Get filter parameters
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : $hoy;
$verTodo = isset($_GET['ver_todo']) && $_GET['ver_todo'] === '1';
$soloMios = isset($_GET['solo_mios']) && $_GET['solo_mios'] === '1';
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';

// Format date for display
if (strpos($fecha, '/') !== false) {
  $fecha_parts = explode('/', $fecha);
  if (count($fecha_parts) === 3) {
    $fecha_sql = $fecha_parts[2] . '-' . $fecha_parts[1] . '-' . $fecha_parts[0];
  } else {
    $fecha_sql = $hoy;
  }
} else {
  $fecha_sql = $fecha;
}

$fecha_mostrar = date('d/m/Y', strtotime($fecha_sql));

// Get statistics based on role and permissions
if (hasPermission('Ver todos los fichajes')) {
  // For admin and HR, get stats for all users
  $userFilter = "";
  $totalRegistros = contarRegistros($conn);
  $registrosHoy = contarRegistrosHoy($conn);
} else {
  // For regular employees, only get their own stats
  $userFilter = "AND usuario_id = $userId";
  $totalRegistros = contarRegistros($conn, "WHERE usuario_id = $userId");
  $registrosHoy = contarRegistrosHoy($conn, "AND usuario_id = $userId");
}

// Include header
include 'includes/header.php';
?>

<section class="card">
  <form method="GET" action="index.php">
    <div class="filter-section">
      <div class="filter-row">
        <label for="fecha" class="filter-label">Filter by date:</label>
        <div class="filter-controls">
          <input type="date" id="fecha" name="fecha" value="<?php echo $fecha_sql; ?>" />
            <button type="submit" class="btn btn-primary">Filter</button>
            <button type="submit" name="ver_todo" value="1" class="btn btn-secondary">View all</button>
        </div>
        <button id="ficharBtn" class="btn btn-success" data-modal-target="ficharModal">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px; margin-right: 5px;">
            <rect x="2" y="5" width="20" height="14" rx="2"></rect>
            <line x1="2" y1="10" x2="22" y2="10"></line>
          </svg>
            CLOCK-IN
        </button>
      </div>
      
      <div class="filter-bottom-row">
        <div class="search-container">
          <div class="search-box">
            <input type="text" id="busqueda" name="busqueda" placeholder="Search by..." value="<?php echo htmlspecialchars($busqueda); ?>" />
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
          </div>
        </div>
        
        <div class="filter-checkbox">
          <input type="checkbox" id="solo_mios" name="solo_mios" value="1" <?php echo $soloMios ? 'checked' : ''; ?> onchange="this.form.submit()">
            <label for="solo_mios">Only my records</label>
        </div>
      </div>
    </div>
  </form>
</section>

<section class="card">
  <div class="table-header">
    <h2 class="table-title">Clock-In Records</h2>
    <div id="filter-info" class="filter-info">
      <!-- This will be populated by JavaScript -->
    </div>
  </div>
  
  <div class="table-container" id="fichajes-container">
    <table>
      <thead>
        <tr>
            <th>Name</th>
            <th>Last Name</th>
            <th>Entry</th>
            <th>Exit</th>
            <th>Overtime</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $where = "";
        
        // If not viewing all records, filter by date
        if (!$verTodo) {
          $where = "WHERE DATE(fecha_entrada) = '" . $conn->real_escape_string($fecha_sql) . "'";
        } else {
          // If viewing all records, don't filter by date
          $where = "WHERE 1=1";
        }
        
        // Add user filter based on permissions and solo_mios parameter
        if (!hasPermission('Ver todos los fichajes') || $soloMios) {
          $where .= " AND usuario_id = $userId";
        }

        // Add search filter if provided
        if (!empty($busqueda)) {
          $busquedaEscaped = $conn->real_escape_string($busqueda);
          $where .= " AND (usuarios.nombre LIKE '%$busquedaEscaped%' OR usuarios.apellido LIKE '%$busquedaEscaped%')";
        }

        $sql = "SELECT usuarios.nombre, usuarios.apellido, sesiones_fichaje.fecha_entrada, sesiones_fichaje.fecha_salida, sesiones_fichaje.tiempo_extra 
                FROM sesiones_fichaje 
                JOIN usuarios ON sesiones_fichaje.usuario_id = usuarios.id 
                $where 
                ORDER BY sesiones_fichaje.fecha_entrada DESC";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
          $counter = 0;
          while ($row = $result->fetch_assoc()) {
            $delay = $counter * 50; // Staggered animation delay
            echo "<tr class='animate-on-scroll' style='animation-delay: {$delay}ms;'>";
            echo "<td data-label='Nombre'>" . htmlspecialchars($row['nombre']) . "</td>";
            echo "<td data-label='Apellido'>" . htmlspecialchars($row['apellido']) . "</td>";
            echo "<td data-label='Entrada'>" . date('d/m/Y H:i:s', strtotime($row['fecha_entrada'])) . "</td>";
            echo "<td data-label='Salida'>" . ($row['fecha_salida'] ? date('d/m/Y H:i:s', strtotime($row['fecha_salida'])) : '-') . "</td>";
            echo "<td data-label='Tiempo Extra'>" . htmlspecialchars($row['tiempo_extra'] ?: '-') . "</td>";
            echo "</tr>";
            $counter++;
          }
        } else {
            echo '<tr><td colspan="5" class="text-center">No clock-in records found for this date.</td></tr>';
        }
        ?>
      </tbody>
    </table>
  </div>
</section>

<!-- Auto-refresh script for today's records -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Only auto-refresh if viewing today's records
    const isViewingToday = <?php echo (!$verTodo && $fecha_sql === $hoy) ? 'true' : 'false'; ?>;
    
    if (isViewingToday) {
      // Auto-refresh every 30 seconds
      setInterval(function() {
        fetch('get_fichajes.php?fecha=<?php echo $fecha_sql; ?>&user_filter=<?php echo urlencode($userFilter); ?>')
          .then(response => response.json())
          .then(data => {
            const tableBody = document.querySelector('#fichajes-container table tbody');
            if (tableBody && data.fichajes) {
              // Clear existing rows
              tableBody.innerHTML = '';
              
              if (data.fichajes.length > 0) {
                // Add new rows
                data.fichajes.forEach((fichaje, index) => {
                  const delay = index * 50;
                  const row = document.createElement('tr');
                  row.className = 'animate-on-scroll';
                  row.style.animationDelay = delay + 'ms';
                  
                  row.innerHTML = `
                    <td data-label="Nombre">${fichaje.nombre}</td>
                    <td data-label="Apellido">${fichaje.apellido}</td>
                    <td data-label="Entrada">${fichaje.fecha_entrada}</td>
                    <td data-label="Salida">${fichaje.fecha_salida || '-'}</td>
                    <td data-label="Tiempo Extra">${fichaje.tiempo_extra || '-'}</td>
                  `;
                  
                  tableBody.appendChild(row);
                });
              } else {
                tableBody.innerHTML = '<tr><td colspan="5" class="text-center">No clock-in records found for this date.</td></tr>';
              }
              
              // Update stats if available
              if (data.stats) {
                if (document.getElementById('total-registros')) {
                  document.getElementById('total-registros').textContent = data.stats.total;
                }
                if (document.getElementById('registros-hoy')) {
                  document.getElementById('registros-hoy').textContent = data.stats.hoy;
                }
              }
            }
          })
          .catch(error => console.error('Error refreshing fichajes data:', error));
      }, 30000); // Refresh every 30 seconds
    }
  });
</script>

<?php
// Include footer
include 'includes/footer.php';
?>
