<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// Database connection
require_once 'includes/db_connection.php';
// Include common functions
require_once 'includes/functions.php';

// Check if user has permission to view statistics
if (!hasPermission('Ver todos los fichajes') && !hasPermission('Ver fichajes propios')) {
  header('Location: panel.php');
  exit;
}


// Set page specific CSS and JS
$page_css = 'css/estadisticas.css';
$page_js = 'js/estadisticas.js';
$show_stats = false;

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

// Get date range from request or use default (current month)
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// Format dates for display
$startDateDisplay = date('d/m/Y', strtotime($startDate));
$endDateDisplay = date('d/m/Y', strtotime($endDate));

// Get statistics based on role and permissions
if (hasPermission('Ver todos los fichajes')) {
    // For admin and HR, get stats for all users
    $whereClause = "";
    $userFilter = "";
} else {
    // For regular employees, only get their own stats
    $whereClause = "WHERE sf.usuario_id = $userId";
    $userFilter = "AND sf.usuario_id = $userId";
}

// Get monthly hours worked
$monthlyHours = array();
$monthlyLabels = array();

$monthlyQuery = "SELECT 
                    DATE_FORMAT(sf.fecha_entrada, '%Y-%m') as month,
                    DATE_FORMAT(sf.fecha_entrada, '%M %Y') as month_name,
                    SUM(TIMESTAMPDIFF(HOUR, sf.fecha_entrada, sf.fecha_salida)) as total_hours
                FROM 
                    sesiones_fichaje sf
                WHERE 
                    sf.fecha_entrada BETWEEN ? AND DATE_ADD(?, INTERVAL 1 YEAR)
                    AND sf.fecha_salida IS NOT NULL
                    $userFilter
                GROUP BY 
                    DATE_FORMAT(sf.fecha_entrada, '%Y-%m')
                ORDER BY 
                    month ASC";

$stmt = $conn->prepare($monthlyQuery);
$stmt->bind_param("ss", $startDate, $startDate);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $monthlyLabels[] = $row['month_name'];
    $monthlyHours[] = (int)$row['total_hours'];
}

// Get extra hours worked
$extraHours = array();

$extraQuery = "SELECT 
                DATE_FORMAT(sf.fecha_entrada, '%Y-%m') as month,
                DATE_FORMAT(sf.fecha_entrada, '%M %Y') as month_name,
                SUM(TIME_TO_SEC(sf.tiempo_extra)) / 3600 as extra_hours
              FROM 
                sesiones_fichaje sf
              WHERE 
                sf.fecha_entrada BETWEEN ? AND DATE_ADD(?, INTERVAL 1 YEAR)
                AND sf.tiempo_extra IS NOT NULL
                $userFilter
              GROUP BY 
                DATE_FORMAT(sf.fecha_entrada, '%Y-%m')
              ORDER BY 
                month ASC";

$stmt = $conn->prepare($extraQuery);
$stmt->bind_param("ss", $startDate, $startDate);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $extraHours[] = (float)$row['extra_hours'];
}

// Get attendance by day of week
$attendanceByDay = array();
$attendanceDays = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
$attendanceValues = array(0, 0, 0, 0, 0, 0, 0);

$dayQuery = "SELECT 
                WEEKDAY(sf.fecha_entrada) as day_of_week,
                COUNT(*) as attendance_count
             FROM 
                sesiones_fichaje sf
             WHERE 
                sf.fecha_entrada BETWEEN ? AND ?
                $userFilter
             GROUP BY 
                WEEKDAY(sf.fecha_entrada)
             ORDER BY 
                day_of_week ASC";

$stmt = $conn->prepare($dayQuery);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $dayIndex = (int)$row['day_of_week'];
    $attendanceValues[$dayIndex] = (int)$row['attendance_count'];
}

// Get punctuality ranking (only for admin and HR)
$punctualityRanking = array();

if (hasPermission('Ver todos los fichajes')) {
    $rankingQuery = "SELECT 
                        u.id,
                        u.nombre,
                        u.apellido,
                        COUNT(CASE WHEN TIME(sf.fecha_entrada) <= '09:00:00' THEN 1 END) as on_time,
                        COUNT(*) as total_entries,
                        (COUNT(CASE WHEN TIME(sf.fecha_entrada) <= '09:00:00' THEN 1 END) / COUNT(*)) * 100 as punctuality
                     FROM 
                        usuarios u
                     LEFT JOIN 
                        sesiones_fichaje sf ON u.id = sf.usuario_id
                     WHERE 
                        sf.fecha_entrada BETWEEN ? AND ?
                     GROUP BY 
                        u.id, u.nombre, u.apellido
                     HAVING 
                        total_entries > 0
                     ORDER BY 
                        punctuality DESC
                     LIMIT 5";

    $stmt = $conn->prepare($rankingQuery);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $punctualityRanking[] = array(
            'id' => $row['id'],
            'name' => $row['nombre'] . ' ' . $row['apellido'],
            'punctuality' => round($row['punctuality'], 2)
        );
    }
}

// Get summary statistics
$totalHours = getTotalHours($conn, $startDate, $endDate, $userFilter);
$extraHoursTotal = 0;
$punctualityRate = getPunctualityRate($conn, $startDate, $endDate, $userFilter);
$daysWorked = getDaysWorked($conn, $startDate, $endDate, $userFilter);

// Total extra hours
$extraHoursQuery = "SELECT 
                        SUM(TIME_TO_SEC(sf.tiempo_extra)) / 3600 as extra_hours
                    FROM 
                        sesiones_fichaje sf
                    WHERE 
                        sf.fecha_entrada BETWEEN ? AND ?
                        AND sf.tiempo_extra IS NOT NULL
                        $userFilter";

$stmt = $conn->prepare($extraHoursQuery);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $extraHoursTotal = round((float)$row['extra_hours'], 2);
}

// Convert data to JSON for JavaScript
$monthlyHoursJson = json_encode($monthlyHours);
$monthlyLabelsJson = json_encode($monthlyLabels);
$extraHoursJson = json_encode($extraHours);
$attendanceDaysJson = json_encode($attendanceDays);
$attendanceValuesJson = json_encode($attendanceValues);

// Include header
include 'includes/header.php';
?>

<div class="page-header">
    <h2>Attendance Statistics</h2>
    <div class="date-range-picker">
        <button class="btn btn-secondary <?php echo (isset($_GET['range']) && $_GET['range'] === 'month') || !isset($_GET['range']) ? 'active' : ''; ?>" data-range="month">Month</button>
        <button class="btn btn-secondary <?php echo isset($_GET['range']) && $_GET['range'] === 'quarter' ? 'active' : ''; ?>" data-range="quarter">Quarter</button>
        <button class="btn btn-secondary <?php echo isset($_GET['range']) && $_GET['range'] === 'year' ? 'active' : ''; ?>" data-range="year">Year</button>
        <button class="btn btn-secondary <?php echo isset($_GET['range']) && $_GET['range'] === 'custom' ? 'active' : ''; ?>" data-range="custom">Custom</button>
    </div>
</div>

<div class="stats-summary">
    <div class="card stat-card animate-on-scroll">
        <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
        </div>
        <div class="stat-content">
            <h3>Hours Worked</h3>
            <div class="stat-value"><?php echo $totalHours; ?></div>
            <div class="stat-label"><?php echo $startDateDisplay . ' - ' . $endDateDisplay; ?></div>
        </div>
    </div>
    
    <div class="card stat-card animate-on-scroll" style="animation-delay: 100ms;">
        <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
            </svg>
        </div>
        <div class="stat-content">
            <h3>Extra Hours</h3>
            <div class="stat-value"><?php echo $extraHoursTotal; ?></div>
            <div class="stat-label"><?php echo $startDateDisplay . ' - ' . $endDateDisplay; ?></div>
        </div>
    </div>
    
    <div class="card stat-card animate-on-scroll" style="animation-delay: 200ms;">
        <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
        </div>
        <div class="stat-content">
            <h3>Punctuality</h3>
            <div class="stat-value"><?php echo $punctualityRate; ?>%</div>
            <div class="stat-label"><?php echo $startDateDisplay . ' - ' . $endDateDisplay; ?></div>
        </div>
    </div>
    
    <div class="card stat-card animate-on-scroll" style="animation-delay: 300ms;">
        <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
        </div>
        <div class="stat-content">
            <h3>Days Worked</h3>
            <div class="stat-value"><?php echo $daysWorked; ?></div>
            <div class="stat-label"><?php echo $startDateDisplay . ' - ' . $endDateDisplay; ?></div>
        </div>
        </div>
    </div>

    <div class="charts-container">
        <div class="card chart-card">
        <div class="card-header">
            <h3 class="card-title">Hours Worked by Month</h3>
        </div>
        <div class="chart-container">
            <canvas id="hoursChart"></canvas>
        </div>
        </div>
        
        <div class="card chart-card">
        <div class="card-header">
            <h3 class="card-title">Extra Hours by Month</h3>
        </div>
        <div class="chart-container">
            <canvas id="extraHoursChart"></canvas>
        </div>
    </div>
</div>

<div class="charts-container">
    <div class="card chart-card">
        <div class="card-header">
            <h3 class="card-title">Attendance by Day of the Week</h3>
        </div>
        <div class="chart-container">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>
    
    <?php if (hasPermission('Ver todos los fichajes') && !empty($punctualityRanking)): ?>
    <div class="card ranking-card">
        <div class="card-header">
            <h3 class="card-title">Punctuality Ranking</h3>
        </div>
        <div class="ranking-container">
            <ul class="ranking-list">
                <?php foreach ($punctualityRanking as $index => $employee): ?>
                <li class="ranking-item animate-on-scroll" style="animation-delay: <?php echo $index * 100; ?>ms;">
                    <div class="ranking-position"><?php echo $index + 1; ?></div>
                    <div class="ranking-avatar">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="ranking-info">
                        <div class="ranking-name"><?php echo $employee['name']; ?></div>
                    </div>
                    <div class="ranking-score">
                        <div class="progress">
                            <div class="progress-bar" style="width: <?php echo $employee['punctuality']; ?>%"></div>
                        </div>
                        <div class="ranking-percentage"><?php echo $employee['punctuality']; ?>%</div>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Custom Date Range Modal -->
<div class="modal-backdrop" id="dateRangeModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Select Date Range</h3>
            <button class="modal-close" data-modal-close>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
            </button>
        </div>
        <div class="modal-body">
            <form id="dateRangeForm" method="GET" action="estadisticas.php">
            <div class="form-group">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo $startDate; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo $endDate; ?>" required>
            </div>
            
            <input type="hidden" name="range" value="custom">
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" data-modal-close>Cancel</button>
            <button class="btn btn-primary" id="applyDateRange" form="dateRangeForm">Apply</button>
        </div>
    </div>
</div>

<!-- Pass data to JavaScript -->
<script>
    // Pass PHP data to JavaScript
    var monthlyHours = <?php echo !empty($monthlyHours) ? $monthlyHoursJson : '[]'; ?>;
    var monthlyLabels = <?php echo !empty($monthlyLabels) ? $monthlyLabelsJson : '[]'; ?>;
    var extraHours = <?php echo !empty($extraHours) ? $extraHoursJson : '[]'; ?>;
    var attendanceDays = <?php echo $attendanceDaysJson; ?>;
    var attendanceValues = <?php echo $attendanceValuesJson; ?>;
</script>

<!-- Chart.js library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Initialize charts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hours Chart
    const hoursCtx = document.getElementById('hoursChart').getContext('2d');
    new Chart(hoursCtx, {
        type: 'bar',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'Horas Trabajadas',
                data: monthlyHours,
                backgroundColor: 'rgba(37, 99, 235, 0.7)',
                borderColor: 'rgba(37, 99, 235, 1)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Extra Hours Chart
    const extraHoursCtx = document.getElementById('extraHoursChart').getContext('2d');
    new Chart(extraHoursCtx, {
        type: 'line',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'Extra Hours',
                data: extraHours,
                backgroundColor: 'rgba(96, 165, 250, 0.2)',
                borderColor: 'rgba(96, 165, 250, 1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true,
                pointBackgroundColor: 'rgba(96, 165, 250, 1)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Attendance Chart
    const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(attendanceCtx, {
        type: 'radar',
        data: {
            labels: attendanceDays,
            datasets: [{
                label: 'Attendance by Day',
                data: attendanceValues,
                backgroundColor: 'rgba(37, 99, 235, 0.2)',
                borderColor: 'rgba(37, 99, 235, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(37, 99, 235, 1)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    beginAtZero: true,
                    min: 0,
                    max: Math.max(...attendanceValues) + 5,
                    ticks: {
                        stepSize: 5
                    }
                }
            }
        }
    });

    // Date range picker functionality
    const dateRangeButtons = document.querySelectorAll('.date-range-picker .btn');
    const dateRangeModal = document.getElementById('dateRangeModal');

    dateRangeButtons.forEach(button => {
        button.addEventListener('click', () => {
            const range = button.getAttribute('data-range');

            if (range === 'custom') {
                // Show date range modal
                dateRangeModal.classList.add('show');
            } else {
                // Calculate date range based on selection
                let startDate, endDate;
                const today = new Date();

                switch (range) {
                    case 'month':
                        // Current month
                        startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                        endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                        break;
                    case 'quarter':
                        // Current quarter
                        const quarter = Math.floor(today.getMonth() / 3);
                        startDate = new Date(today.getFullYear(), quarter * 3, 1);
                        endDate = new Date(today.getFullYear(), (quarter + 1) * 3, 0);
                        break;
                    case 'year':
                        // Current year
                        startDate = new Date(today.getFullYear(), 0, 1);
                        endDate = new Date(today.getFullYear(), 11, 31);
                        break;
                }

                // Format dates for URL
                const formatDate = (date) => {
                    return date.toISOString().split('T')[0];
                };

                // Redirect to the page with the new date range
                window.location.href = `estadisticas.php?start_date=${formatDate(startDate)}&end_date=${formatDate(endDate)}&range=${range}`;
            }
        });
    });

    // Close modal when clicking close button or outside
    document.querySelectorAll('[data-modal-close]').forEach(button => {
        button.addEventListener('click', () => {
            const modal = button.closest('.modal-backdrop');
            if (modal) {
                modal.classList.remove('show');
            }
        });
    });

    // Close modal when clicking outside
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal-backdrop')) {
            e.target.classList.remove('show');
        }
    });
});
</script>

<?php
// Include footer
include 'includes/footer.php';
?>
