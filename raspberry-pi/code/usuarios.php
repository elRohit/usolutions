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

// Check if user has permission to manage users
if (!hasPermission('Gestionar usuarios')) {
  header('Location: panel.php');
  exit;
}


// Set page specific CSS and JS
$page_css = 'css/usuarios.css';
$page_js = 'js/usuarios.js';
$show_stats = false;

// Get user ID and check role
$userId = $_SESSION['user_id'];

// Check if rol_id exists, otherwise use user_role
if (!isset($_SESSION['rol_id'])) {
  // Try to get rol_id from database if needed
  $stmt = $conn->prepare("SELECT rol_id FROM usuarios WHERE id = ?");
  $stmt->bind_param("i", $userId);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($row = $result->fetch_assoc()) {
    $_SESSION['rol_id'] = $row['rol_id']; // Set it for future use
  }
}

// Process form submissions
$message = '';
$messageType = '';

// Add new user
if (isset($_POST['add_user'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $apellido = $conn->real_escape_string($_POST['apellido']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $rol_id = (int)$_POST['rol_id'];
    
    // Check if email already exists
    $checkEmail = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $emailResult = $checkEmail->get_result();
    
    if ($emailResult->num_rows > 0) {
        $message = 'The email address is already registered.';
        $messageType = 'danger';
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, email, password, rol_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $nombre, $apellido, $email, $password, $rol_id);
        
        if ($stmt->execute()) {
            $message = 'User added successfully.';
            $messageType = 'success';
        } else {
            $message = 'Error adding user: ' . $conn->error;
            $messageType = 'danger';
        }
    }
}

// Edit user
if (isset($_POST['edit_user'])) {
    $id = (int)$_POST['id'];
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $apellido = $conn->real_escape_string($_POST['apellido']);
    $email = $conn->real_escape_string($_POST['email']);
    $rol_id = (int)$_POST['rol_id'];
    
    // Check if password is being updated
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, email = ?, password = ?, rol_id = ? WHERE id = ?");
        $stmt->bind_param("ssssis", $nombre, $apellido, $email, $password, $rol_id, $id);
    } else {
        $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, email = ?, rol_id = ? WHERE id = ?");
        $stmt->bind_param("sssis", $nombre, $apellido, $email, $rol_id, $id);
    }
    
    if ($stmt->execute()) {
        $message = 'User updated successfully.';
        $messageType = 'success';
    } else {
        $message = 'Error updating user: ' . $conn->error;
        $messageType = 'danger';
    }
}

// Delete user
if (isset($_POST['delete_user'])) {
    $id = (int)$_POST['id'];
    
    // First, check if user has any RFID cards assigned
    $checkRfid = $conn->prepare("SELECT id FROM tarjetas WHERE usuario_id = ?");
    $checkRfid->bind_param("i", $id);
    $checkRfid->execute();
    $rfidResult = $checkRfid->get_result();
    
    if ($rfidResult->num_rows > 0) {
        // Update RFID cards to unassign them
        $unassignRfid = $conn->prepare("UPDATE tarjetas SET usuario_id = NULL WHERE usuario_id = ?");
        $unassignRfid->bind_param("i", $id);
        $unassignRfid->execute();
    }
    
    // Now delete the user
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $message = 'User deleted successfully.';
        $messageType = 'success';
    } else {
        $message = 'Error deleting user: ' . $conn->error;
        $messageType = 'danger';
    }
}

// Get all roles for dropdown
$roles = [];
$rolesQuery = "SELECT id, nombre FROM roles ORDER BY nombre";
$rolesResult = $conn->query($rolesQuery);
if ($rolesResult) {
    while ($row = $rolesResult->fetch_assoc()) {
        $roles[] = $row;
    }
}

// Get all users
$users = [];
$usersQuery = "SELECT u.id, u.nombre, u.apellido, u.email, r.nombre as role_name, r.id as role_id 
               FROM usuarios u 
               JOIN roles r ON u.rol_id = r.id 
               ORDER BY u.nombre, u.apellido";
$usersResult = $conn->query($usersQuery);
if ($usersResult) {
    while ($row = $usersResult->fetch_assoc()) {
        $users[] = $row;
    }
}

// Include header
include 'includes/header.php';
?>

<div class="page-header">
    <h2>User Management</h2>
    <button class="btn btn-primary" data-modal-target="addUserModal">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Add User
    </button>
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

<div class="card">
    <div class="card-header">
        <div class="search-filter">
            <div class="search-box">
                <input type="text" id="searchUsers" class="form-control" placeholder="Search users...">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>
            <div class="filter-options">
                <select id="filterRole" class="form-control">
                    <option value="">All roles</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo $role['id']; ?>"><?php echo $role['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    
    <div class="table-container">
        <table id="usersTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="4" class="text-center">No users registered.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                    <tr data-role="<?php echo $user['role_id']; ?>">
                        <td data-label="Nombre"><?php echo $user['nombre'] . ' ' . $user['apellido']; ?></td>
                        <td data-label="Email"><?php echo $user['email']; ?></td>
                        <td data-label="Rol">
                            <span class="badge <?php echo 'badge-' . ($user['role_name'] === 'Administrador' ? 'primary' : ($user['role_name'] === 'Recursos Humanos' ? 'info' : 'secondary')); ?>">
                                <?php echo $user['role_name']; ?>
                            </span>
                        </td>
                        <td data-label="Acciones">
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-secondary edit-user" data-user-id="<?php echo $user['id']; ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </button>
                                <button class="btn btn-sm btn-danger delete-user" data-user-id="<?php echo $user['id']; ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal-backdrop" id="addUserModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Add New User</h3>
            <button class="modal-close" data-modal-close>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <form id="addUserForm" method="POST" action="usuarios.php">
                <div class="form-group">
                    <label for="nombre" class="form-label">Name</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="apellido" class="form-label">Last Name</label>
                    <input type="text" id="apellido" name="apellido" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="rol_id" class="form-label">Rol</label>
                    <select id="rol_id" name="rol_id" class="form-control" required>
                        <option value="">Select role</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?php echo $role['id']; ?>"><?php echo $role['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <input type="hidden" name="add_user" value="1">
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" data-modal-close>Cancel</button>
            <button class="btn btn-primary" id="saveUserBtn" form="addUserForm">Save</button>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal-backdrop" id="editUserModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Edit User</h3>
            <button class="modal-close" data-modal-close>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <form id="editUserForm" method="POST" action="usuarios.php">
                <input type="hidden" id="edit_id" name="id">
                
                <div class="form-group">
                    <label for="edit_nombre" class="form-label">Name</label>
                    <input type="text" id="edit_nombre" name="nombre" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_apellido" class="form-label">Last Name</label>
                    <input type="text" id="edit_apellido" name="apellido" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_email" class="form-label">Email Address</label>
                    <input type="email" id="edit_email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_password" class="form-label">Password (leave blank to keep current)</label>
                    <input type="password" id="edit_password" name="password" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="edit_rol_id" class="form-label">Role</label>
                    <select id="edit_rol_id" name="rol_id" class="form-control" required>
                        <option value="">Select role</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?php echo $role['id']; ?>"><?php echo $role['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <input type="hidden" name="edit_user" value="1">
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" data-modal-close>Cancel</button>
            <button class="btn btn-primary" id="updateUserBtn" form="editUserForm">Update</button>
        </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal-backdrop" id="deleteUserModal">
        <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Confirm Deletion</h3>
            <button class="modal-close" data-modal-close>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
            </button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this user? This action cannot be undone.</p>
            <form id="deleteUserForm" method="POST" action="usuarios.php">
                <input type="hidden" id="delete_id" name="id">
                <input type="hidden" name="delete_user" value="1">
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" data-modal-close>Cancel</button>
            <button class="btn btn-danger" id="confirmDeleteBtn" form="deleteUserForm">Delete</button>
        </div>
    </div>
</div>

<?php
// Include footer
include 'includes/footer.php';
?>
