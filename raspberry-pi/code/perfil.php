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
$page_css = 'css/perfil.css';
$page_js = 'js/perfil.js';
$show_stats = false;

// Get user ID
$userId = $_SESSION['user_id'];

// Process form submission for profile update
$message = '';
$messageType = '';

if (isset($_POST['update_profile'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $apellido = $conn->real_escape_string($_POST['apellido']);
    $email = $conn->real_escape_string($_POST['email']);
    
    // Check if email already exists for another user
    $checkEmail = $conn->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
    $checkEmail->bind_param("si", $email, $userId);
    $checkEmail->execute();
    $emailResult = $checkEmail->get_result();
    
    if ($emailResult->num_rows > 0) {
        $message = 'The email address is already registered by another user.';
        $messageType = 'danger';
    } else {
        // Update user profile
        $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, email = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nombre, $apellido, $email, $userId);
        
        if ($stmt->execute()) {
            $message = 'Profile updated successfully.';
            $messageType = 'success';
            
            // Update session variables
            $_SESSION['user_name'] = $nombre;
            $_SESSION['user_apellido'] = $apellido;
            $_SESSION['user_email'] = $email;
        } else {
            $message = 'Error updating profile: ' . $conn->error;
            $messageType = 'danger';
        }
    }
}

// Process form submission for password change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate passwords
    if ($new_password !== $confirm_password) {
        $message = 'The new passwords do not match.';
        $messageType = 'danger';
    } else {
        // Get current password from database
        $stmt = $conn->prepare("SELECT password FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify current password
            if (password_verify($current_password, $user['password'])) {
                // Hash new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Update password
                $updateStmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
                $updateStmt->bind_param("si", $hashed_password, $userId);
                
                if ($updateStmt->execute()) {
                    $message = 'Password updated successfully.';
                    $messageType = 'success';
                } else {
                    $message = 'Error updating password: ' . $conn->error;
                    $messageType = 'danger';
                }
            } else {
                $message = 'The current password is incorrect.';
                $messageType = 'danger';
            }
        } else {
            $message = 'Error verifying user.';
            $messageType = 'danger';
        }
    }
}

// Get user data
$user = null;
$stmt = $conn->prepare("SELECT id, nombre, apellido, email, rol_id FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Get role name
    $roleStmt = $conn->prepare("SELECT nombre FROM roles WHERE id = ?");
    $roleStmt->bind_param("i", $user['rol_id']);
    $roleStmt->execute();
    $roleResult = $roleStmt->get_result();
    
    if ($roleResult->num_rows === 1) {
        $role = $roleResult->fetch_assoc();
        $user['role_name'] = $role['nombre'];
    } else {
        $user['role_name'] = 'Desconocido';
    }
    
    // Check if user has RFID card
    $rfidStmt = $conn->prepare("SELECT rfid_code FROM tarjetas WHERE usuario_id = ?");
    $rfidStmt->bind_param("i", $userId);
    $rfidStmt->execute();
    $rfidResult = $rfidStmt->get_result();
    
    if ($rfidResult->num_rows === 1) {
        $rfid = $rfidResult->fetch_assoc();
        $user['rfid'] = $rfid['rfid_code'];
    } else {
        $user['rfid'] = null;
    }
} else {
    // User not found, redirect to login
    header('Location: logout.php');
    exit;
}

// Get recent attendance history
$attendanceHistory = [];
$historyQuery = "SELECT fecha_entrada, fecha_salida, tiempo_extra 
                FROM sesiones_fichaje 
                WHERE usuario_id = ? 
                ORDER BY fecha_entrada DESC 
                LIMIT 10";

$historyStmt = $conn->prepare($historyQuery);
$historyStmt->bind_param("i", $userId);
$historyStmt->execute();
$historyResult = $historyStmt->get_result();

while ($row = $historyResult->fetch_assoc()) {
    // Calculate total hours if both entry and exit times exist
    $totalHours = null;
    if ($row['fecha_entrada'] && $row['fecha_salida']) {
        $entry = new DateTime($row['fecha_entrada']);
        $exit = new DateTime($row['fecha_salida']);
        $interval = $entry->diff($exit);
        $totalHours = $interval->format('%H:%I:%S');
    }
    
    $attendanceHistory[] = [
        'date' => date('Y-m-d', strtotime($row['fecha_entrada'])),
        'entry_time' => date('H:i:s', strtotime($row['fecha_entrada'])),
        'exit_time' => $row['fecha_salida'] ? date('H:i:s', strtotime($row['fecha_salida'])) : null,
        'total_hours' => $totalHours,
        'extra_hours' => $row['tiempo_extra'],
        'status' => $row['fecha_salida'] ? 'complete' : 'incomplete'
    ];
}

// Include header
include 'includes/header.php';
?>

<div class="profile-container">
    <div class="profile-header">
        <div class="profile-avatar">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
        </div>
        <div class="profile-info">
            <h1><?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido']); ?></h1>
            <div class="profile-meta">
                <span class="badge badge-primary"><?php echo htmlspecialchars($user['role_name']); ?></span>
            </div>
        </div>
    </div>
    
    <?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <?php if ($messageType === 'success'): ?>
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            <?php else: ?>
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            <?php endif; ?>
        </svg>
        <?php echo $message; ?>
    </div>
    <?php endif; ?>
    
    <div class="profile-tabs">
        <button class="tab-button active" data-tab="info">Personal Information</button>
        <button class="tab-button" data-tab="password">Change Password</button>
        <button class="tab-button" data-tab="attendance">Attendance History</button>
    </div>
    
    <div class="tab-content active" id="info-tab">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Personal Information</h3>
            </div>
            <div class="card-body">
                <form id="profileForm" method="POST" action="perfil.php">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nombre" class="form-label">First Name</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($user['nombre']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="apellido" class="form-label">Last Name</label>
                            <input type="text" id="apellido" name="apellido" class="form-control" value="<?php echo htmlspecialchars($user['apellido']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="role" class="form-label">Role</label>
                            <input type="text" id="role" class="form-control" value="<?php echo htmlspecialchars($user['role_name']); ?>" disabled>
                        </div>
                        
                        <div class="form-group">
                            <label for="rfid" class="form-label">RFID Card</label>
                            <input type="text" id="rfid" class="form-control" value="<?php echo $user['rfid'] ? htmlspecialchars($user['rfid']) : 'Not assigned'; ?>" disabled>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <input type="hidden" name="update_profile" value="1">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="tab-content" id="password-tab">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Change Password</h3>
            </div>
            <div class="card-body">
                <form id="passwordForm" method="POST" action="perfil.php">
                    <div class="form-group">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" id="current_password" name="current_password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" id="new_password" name="new_password" class="form-control" required>
                        <div class="form-text">The password must be at least 6 characters long.</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    </div>
                    
                    <div class="form-actions">
                        <input type="hidden" name="change_password" value="1">
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="tab-content" id="attendance-tab">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Attendance History</h3>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Entry</th>
                                <th>Exit</th>
                                <th>Total Hours</th>
                                <th>Extra Hours</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($attendanceHistory)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No attendance records available.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($attendanceHistory as $record): ?>
                                <tr>
                                    <td data-label="Fecha"><?php echo date('d/m/Y', strtotime($record['date'])); ?></td>
                                    <td data-label="Entrada"><?php echo $record['entry_time']; ?></td>
                                    <td data-label="Salida"><?php echo $record['exit_time'] ?: '-'; ?></td>
                                    <td data-label="Horas Totales"><?php echo $record['total_hours'] ?: '-'; ?></td>
                                    <td data-label="Horas Extra"><?php echo $record['extra_hours'] ?: '-'; ?></td>
                                    <td data-label="Estado">
                                        <span class="badge <?php echo $record['status'] === 'complete' ? 'badge-success' : 'badge-warning'; ?>">
                                            <?php echo $record['status'] === 'complete' ? 'Complete' : 'Incomplete'; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include 'includes/footer.php';
?>
