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

// Check if user has permission to manage RFID cards
if (!hasPermission('Gestionar usuarios')) {
  header('Location: panel.php');
  exit;
}

// Database connection

// Set page specific CSS and JS
$page_css = 'css/tarjetas.css';
$page_js = 'js/tarjetas.js';
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

// Handle RFID scanning
if (isset($_POST['scan_rfid'])) {
  $message = "📡 Bring an RFID/NFC card close to the reader...<br>";

  // Only use the Python script to read the RFID
  $result = executeRaspiCommand('python3 /home/ira/leer_rfid.py');

  if ($result['success']) {
    // Trim any whitespace from the output
    $rfid_id = trim($result['output']);
    
    // Check if the output is a valid numeric ID
    if (is_numeric($rfid_id)) {
      $message .= "✅ Card ID: " . htmlspecialchars($rfid_id);
      $messageType = 'success';
    } else {
      $message .= "❌ Error: The response is not a valid ID: " . htmlspecialchars($result['output']);
      $messageType = 'danger';
    }
    } else {
    $message .= "❌ Could not read the card: " . htmlspecialchars($result['message']);
    $messageType = 'danger';
  }
}

// Handle RFID registration with user selection
if (isset($_POST['register_rfid']) && isset($_POST['selected_user_id'])) {
  $selectedUserId = (int)$_POST['selected_user_id'];
  
  // Just scan the card using the Python script
  $result = executeRaspiCommand('python3 /home/ira/leer_rfid.py');
  
  if ($result['success']) {
    // Trim any whitespace from the output
    $rfid_id = trim($result['output']);
    
    // Check if the output is a valid numeric ID
    if (is_numeric($rfid_id)) {
      $message = "✅ Card ID: " . htmlspecialchars($rfid_id);
      $messageType = 'success';
    } else {
      $message = "❌ Error: The response is not a valid ID: " . htmlspecialchars($result['output']);
      $messageType = 'danger';
    }
    } else {
    $message = "❌ Could not read the card: " . htmlspecialchars($result['message']);
    $messageType = 'danger';
  }
}

// Add new RFID card
if (isset($_POST['add_card']) || isset($_REQUEST['add_card'])) {
  $rfid_code = isset($_REQUEST['rfid_code']) ? $conn->real_escape_string($_REQUEST['rfid_code']) : '';
  $usuario_id = !empty($_REQUEST['usuario_id']) ? (int)$_REQUEST['usuario_id'] : null;

  if (empty($rfid_code)) {
    $message = 'Error: No valid RFID code was provided.';
    $messageType = 'danger';
  } else {
    // Check if code already exists
    $checkCode = $conn->prepare("SELECT id FROM tarjetas WHERE rfid_code = ?");
    $checkCode->bind_param("s", $rfid_code);
    $checkCode->execute();
    $codeResult = $checkCode->get_result();

    if ($codeResult->num_rows > 0) {
      $message = 'The RFID code is already registered.';
      $messageType = 'danger';
    } else {
      if ($usuario_id) {
        // Check if user already has a card
        $checkUser = $conn->prepare("SELECT id FROM tarjetas WHERE usuario_id = ?");
        $checkUser->bind_param("i", $usuario_id);
        $checkUser->execute();
        $userResult = $checkUser->get_result();
        
        if ($userResult->num_rows > 0) {
          // User already has a card, unassign it first
          $unassignStmt = $conn->prepare("UPDATE tarjetas SET usuario_id = NULL WHERE usuario_id = ?");
          $unassignStmt->bind_param("i", $usuario_id);
          $unassignStmt->execute();
        }
        
        // Insert new card with user
        $stmt = $conn->prepare("INSERT INTO tarjetas (rfid_code, usuario_id) VALUES (?, ?)");
        $stmt->bind_param("si", $rfid_code, $usuario_id);
      } else {
        // Insert new card without user
        $stmt = $conn->prepare("INSERT INTO tarjetas (rfid_code) VALUES (?)");
        $stmt->bind_param("s", $rfid_code);
      }

      if ($stmt->execute()) {
        $message = 'RFID card added successfully.';
        $messageType = 'success';
      } else {
        $message = 'Error adding card: ' . $conn->error;
        $messageType = 'danger';
      }
    }
  }
}

// Assign card to user
if (isset($_POST['assign_card'])) {
  $card_id = (int)$_POST['card_id'];
  $usuario_id = (int)$_POST['usuario_id'];
  
  // Check if user already has a card
  $checkUser = $conn->prepare("SELECT id FROM tarjetas WHERE usuario_id = ?");
  $checkUser->bind_param("i", $usuario_id);
  $checkUser->execute();
  $userResult = $checkUser->get_result();
  
  if ($userResult->num_rows > 0) {
    // User already has a card, unassign it first
    $unassignStmt = $conn->prepare("UPDATE tarjetas SET usuario_id = NULL WHERE usuario_id = ?");
    $unassignStmt->bind_param("i", $usuario_id);
    $unassignStmt->execute();
  }
  
  // Now assign the new card
  $stmt = $conn->prepare("UPDATE tarjetas SET usuario_id = ? WHERE id = ?");
  $stmt->bind_param("ii", $usuario_id, $card_id);
  
  if ($stmt->execute()) {
    $message = 'RFID card assigned successfully.';
    $messageType = 'success';
  } else {
    $message = 'Error assigning card: ' . $conn->error;
    $messageType = 'danger';
  }
}

// Revoke card from user
if (isset($_POST['revoke_card'])) {
  $card_id = (int)$_POST['card_id'];
  
  $stmt = $conn->prepare("UPDATE tarjetas SET usuario_id = NULL WHERE id = ?");
  $stmt->bind_param("i", $card_id);
  
  if ($stmt->execute()) {
    $message = 'RFID card successfully revoked.';
    $messageType = 'success';
  } else {
    $message = 'Error revoking card: ' . $conn->error;
    $messageType = 'danger';
  }
}

// Delete card
if (isset($_POST['delete_card'])) {
  $card_id = (int)$_POST['card_id'];
  
  $stmt = $conn->prepare("DELETE FROM tarjetas WHERE id = ?");
  $stmt->bind_param("i", $card_id);
  
  if ($stmt->execute()) {
    $message = 'RFID card deleted successfully.';
    $messageType = 'success';
  } else {
    $message = 'Error deleting card: ' . $conn->error;
    $messageType = 'danger';
  }
}

// Get all RFID cards with user info
$cards = array();
$cardsQuery = "SELECT t.id, t.rfid_code, t.usuario_id, u.nombre, u.apellido 
             FROM tarjetas t 
             LEFT JOIN usuarios u ON t.usuario_id = u.id 
             ORDER BY t.rfid_code";
$cardsResult = $conn->query($cardsQuery);
if ($cardsResult) {
  while ($row = $cardsResult->fetch_assoc()) {
    $cards[] = $row;
  }
}

// Get users without RFID cards
$usersWithoutCards = array();
$usersQuery = "SELECT u.id, u.nombre, u.apellido 
             FROM usuarios u 
             LEFT JOIN tarjetas t ON u.id = t.usuario_id 
             WHERE t.id IS NULL 
             ORDER BY u.nombre, u.apellido";
$usersResult = $conn->query($usersQuery);
if ($usersResult) {
  while ($row = $usersResult->fetch_assoc()) {
    $usersWithoutCards[] = $row;
  }
}

// Include header
include 'includes/header.php';
?>

<div class="page-header">
  <h2>RFID Card Management</h2>
  <div class="header-actions">
    <button type="button" id="scanRfidBtn" class="btn btn-secondary">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="2" y="5" width="20" height="14" rx="2"></rect>
        <line x1="2" y1="10" x2="22" y2="10"></line>
      </svg>
      Scan Card
    </button>
    <button type="button" class="btn btn-primary" data-modal-target="registerRfidModal" style="margin-left: 10px;">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
      </svg>
      Register Card
    </button>
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

<div class="card">
  <div class="card-header">
    <div class="search-filter">
      <div class="search-box">
        <input type="text" id="searchRfid" class="form-control" placeholder="Search cards...">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
      </div>
      <div class="filter-options">
        <select id="filterStatus" class="form-control">
          <option value="">All statuses</option>
          <option value="assigned">Assigned</option>
          <option value="unassigned">Unassigned</option>
        </select>
      </div>
        </div>
      </div>
      
      <div class="table-container">
        <table id="rfidTable">
      <thead>
        <tr>
          <th>RFID Code</th>
          <th>Assigned User</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($cards)): ?>
          <tr>
            <td colspan="4" class="text-center">No RFID cards registered.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($cards as $card): ?>
          <tr data-status="<?php echo $card['usuario_id'] ? 'assigned' : 'unassigned'; ?>">
            <td data-label="Código RFID">
              <span class="rfid-code"><?php echo $card['rfid_code']; ?></span>
            </td>
            <td data-label="Usuario Asignado">
              <?php if ($card['usuario_id']): ?>
                <span class="user-name"><?php echo $card['nombre'] . ' ' . $card['apellido']; ?></span>
                <?php else: ?>
                <span class="no-user">Unassigned</span>
              <?php endif; ?>
            </td>
            <td data-label="Estado">
              <span class="badge <?php echo $card['usuario_id'] ? 'badge-success' : 'badge-warning'; ?>">
                <?php echo $card['usuario_id'] ? 'Assigned' : 'Unassigned'; ?>
              </span>
            </td>
            <td data-label="Acciones">
              <div class="action-buttons">
                <?php if (!$card['usuario_id']): ?>
                <button class="btn btn-sm btn-primary assign-card" data-card-id="<?php echo $card['id']; ?>">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <line x1="20" y1="8" x2="20" y2="14"></line>
                    <line x1="23" y1="11" x2="17" y2="11"></line>
                  </svg>
                  Assign
                </button>
                <?php else: ?>
                <button class="btn btn-sm btn-warning revoke-card" data-card-id="<?php echo $card['id']; ?>">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <line x1="18" y1="11" x2="23" y2="11"></line>
                  </svg>
                  Revoke
                </button>
                <?php endif; ?>
                <button class="btn btn-sm btn-danger delete-card" data-card-id="<?php echo $card['id']; ?>">
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

<div class="card">
  <div class="card-header">
    <h3 class="card-title">Users without RFID Card</h3>
  </div>
  
  <div class="table-container">
    <table id="usersWithoutRfidTable">
      <thead>
        <tr>
          <th>Name</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($usersWithoutCards)): ?>
          <tr>
        <td colspan="2" class="text-center">No users without RFID card.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($usersWithoutCards as $user): ?>
          <tr>
            <td data-label="Nombre"><?php echo $user['nombre'] . ' ' . $user['apellido']; ?></td>
            <td data-label="Acciones">
              <button class="btn btn-sm btn-primary assign-to-user" data-user-id="<?php echo $user['id']; ?>" data-user-name="<?php echo $user['nombre'] . ' ' . $user['apellido']; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                  <line x1="2" y1="10" x2="22" y2="10"></line>
                </svg>
                Assign Card
              </button>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Register RFID Modal -->
<div class="modal-backdrop" id="registerRfidModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Register New RFID Card</h3>
      <button class="modal-close" data-modal-close>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label for="selected_user_id" class="form-label">Select User</label>
        <select id="selected_user_id" name="selected_user_id" class="form-control" required>
            <option value="">Select user</option>
          <?php foreach ($usersWithoutCards as $user): ?>
          <option value="<?php echo $user['id']; ?>"><?php echo $user['nombre'] . ' ' . $user['apellido']; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <div class="form-text">
        <p>When you click "Register," the RFID reader on the device will be activated. Place the card on the reader when prompted.</p>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" data-modal-close>Cancel</button>
      <button class="btn btn-primary" id="confirmRegisterBtn">Register</button>
    </div>
  </div>
</div>

<!-- Scan Result Modal -->
<div class="modal-backdrop" id="scanResultModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title" id="scanModalTitle">Card Scanning</h3>
      <button class="modal-close" data-modal-close>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    </div>
    <div class="modal-body">
      <div id="scanResultContent">
        <!-- Content will be dynamically populated -->
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" data-modal-close>Close</button>
    </div>
  </div>
</div>

<!-- Register Confirmation Modal -->
<div class="modal-backdrop" id="registerConfirmModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Confirm Registration</h3>
      <button class="modal-close" data-modal-close>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    </div>
    <div class="modal-body">
      <div id="registerConfirmContent">
        <!-- Content will be dynamically populated -->
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" data-modal-close>Cancel</button>
      <button class="btn btn-primary" id="confirmFinalRegisterBtn">Confirm</button>
    </div>
  </div>
</div>

<!-- Add RFID Modal -->
<div class="modal-backdrop" id="addRfidModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Add RFID Card Manually</h3>
      <button class="modal-close" data-modal-close>
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="18" y1="6" x2="6" y2="18"></line>
        <line x1="6" y1="6" x2="18" y2="18"></line>
      </svg>
      </button>
    </div>
    <div class="modal-body">
      <form id="addRfidForm" method="POST" action="tarjetas.php">
      <div class="form-group">
        <label for="rfid_code" class="form-label">RFID Code</label>
        <input type="text" id="rfid_code" name="rfid_code" class="form-control" required>
        <div class="form-text">Enter the RFID code manually.</div>
      </div>
      
      <div class="form-group">
        <label for="usuario_id" class="form-label">Assign to User (optional)</label>
          <select id="usuario_id" name="usuario_id" class="form-control">
            <option value="">Do not assign now</option>
            <?php foreach ($usersWithoutCards as $user): ?>
            <option value="<?php echo $user['id']; ?>"><?php echo $user['nombre'] . ' ' . $user['apellido']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <input type="hidden" name="add_card" value="1">
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" data-modal-close>Cancel</button>
      <button class="btn btn-primary" id="saveRfidBtn" form="addRfidForm">Save</button>
    </div>
  </div>
</div>

<!-- Assign Card Modal -->
<div class="modal-backdrop" id="assignCardModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Assign RFID Card</h3>
      <button class="modal-close" data-modal-close>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    </div>
    <div class="modal-body">
      <form id="assignCardForm" method="POST" action="tarjetas.php">
        <input type="hidden" id="card_id" name="card_id">
        
        <div class="form-group">
            <label for="assign_usuario_id" class="form-label">User</label>
            <select id="assign_usuario_id" name="usuario_id" class="form-control" required>
            <option value="">Select user</option>
            <?php foreach ($usersWithoutCards as $user): ?>
            <option value="<?php echo $user['id']; ?>"><?php echo $user['nombre'] . ' ' . $user['apellido']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <input type="hidden" name="assign_card" value="1">
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" data-modal-close>Cancel</button>
      <button class="btn btn-primary" id="confirmAssignBtn" form="assignCardForm">Assign</button>
    </div>
  </div>
</div>

<!-- Revoke Card Modal -->
<div class="modal-backdrop" id="revokeCardModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Confirm Revocation</h3>
      <button class="modal-close" data-modal-close>
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="18" y1="6" x2="6" y2="18"></line>
        <line x1="6" y1="6" x2="18" y2="18"></line>
      </svg>
      </button>
    </div>
    <div class="modal-body">
      <p>Are you sure you want to revoke this RFID card from the assigned user?</p>
      <form id="revokeCardForm" method="POST" action="tarjetas.php">
      <input type="hidden" id="revoke_card_id" name="card_id">
      <input type="hidden" name="revoke_card" value="1">
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" data-modal-close>Cancel</button>
      <button class="btn btn-warning" id="confirmRevokeBtn" form="revokeCardForm">Revoke</button>
    </div>
  </div>
</div>

<!-- Delete Card Modal -->
<div class="modal-backdrop" id="deleteCardModal">
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
      <p>Are you sure you want to delete this RFID card? This action cannot be undone.</p>
      <form id="deleteCardForm" method="POST" action="tarjetas.php">
      <input type="hidden" id="delete_card_id" name="card_id">
      <input type="hidden" name="delete_card" value="1">
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" data-modal-close>Cancel</button>
      <button class="btn btn-danger" id="confirmDeleteBtn" form="deleteCardForm">Delete</button>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const scanRfidBtn = document.getElementById('scanRfidBtn');
  const scanResultModal = document.getElementById('scanResultModal');
  const scanResultContent = document.getElementById('scanResultContent');
  const scanModalTitle = document.getElementById('scanModalTitle');
  const registerConfirmModal = document.getElementById('registerConfirmModal');
  const registerConfirmContent = document.getElementById('registerConfirmContent');
  const confirmRegisterBtn = document.getElementById('confirmRegisterBtn');
  const confirmFinalRegisterBtn = document.getElementById('confirmFinalRegisterBtn');

  let selectedUserId = null;
  let selectedUserName = null;
  let scannedCardId = null;

  function showScanningModal(title = "Card Scanning") {
    scanModalTitle.textContent = title;
    scanResultContent.innerHTML = `
      <div class="text-center">
        <div style="margin-bottom: 20px;">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 64px; height: 64px; margin: 0 auto 15px; display: block; color: var(--primary);">
            <rect x="2" y="5" width="20" height="14" rx="2"></rect>
            <line x1="2" y1="10" x2="22" y2="10"></line>
          </svg>
            <h4 style="margin-bottom: 15px;">Bring the card close to the reader</h4>
          <div class="spinner" style="margin: 0 auto;"></div>
        </div>
        <p>Waiting for RFID/NFC card reading...</p>
      </div>
    `;
    scanResultModal.classList.add('show');
  }

  if (scanRfidBtn) {
    scanRfidBtn.addEventListener('click', function() {
      showScanningModal();
      const xhr = new XMLHttpRequest();
      xhr.open('POST', 'tarjetas.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      xhr.onload = function() {
        const parser = new DOMParser();
        const doc = parser.parseFromString(xhr.responseText, 'text/html');
        const alertElement = doc.querySelector('.alert');

        if (alertElement) {
          const messageType = alertElement.classList.contains('alert-success') ? 'success' : 'danger';
          const message = alertElement.innerHTML;

            scanModalTitle.textContent = "Scan Result";
          scanResultContent.innerHTML = `
            <div class="alert alert-${messageType}">${message}</div>
          `;
        } else {
          scanModalTitle.textContent = "Error";
          scanResultContent.innerHTML = `
            <div class="alert alert-danger">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
                Could not process the server response.
            </div>
          `;
        }
      };

      xhr.onerror = function() {
        scanModalTitle.textContent = "Error";
        scanResultContent.innerHTML = `
          <div class="alert alert-danger">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            Connection error. Please try again.
          </div>
        `;
      };

      xhr.send('scan_rfid=1');
    });
  }

  if (confirmRegisterBtn) {
    confirmRegisterBtn.addEventListener('click', function() {
      const userSelect = document.getElementById('selected_user_id');
      selectedUserId = userSelect.value;
      selectedUserName = userSelect.options[userSelect.selectedIndex].text;

      if (!selectedUserId) {
        alert("Please select a user to assign the card.");
        return;
      }

      document.getElementById('registerRfidModal').classList.remove('show');

      scanModalTitle.textContent = "Card Registration";
      scanResultContent.innerHTML = `
        <div class="text-center">
          <div style="margin-bottom: 20px;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 64px; height: 64px; margin: 0 auto 15px; display: block; color: var(--primary);">
              <rect x="2" y="5" width="20" height="14" rx="2"></rect>
              <line x1="2" y1="10" x2="22" y2="10"></line>
            </svg>
            <h4 style="margin-bottom: 15px;">User: ${selectedUserName}</h4>
            <div class="spinner" style="margin: 0 auto;"></div>
          </div>
            <p>Bring the card close to the reader to assign it to the user...</p>
        </div>
      `;
      scanResultModal.classList.add('show');

      const xhr = new XMLHttpRequest();
      xhr.open('POST', 'tarjetas.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      xhr.onload = function() {
        const parser = new DOMParser();
        const doc = parser.parseFromString(xhr.responseText, 'text/html');
        const alertElement = doc.querySelector('.alert');

        if (alertElement) {
          const messageType = alertElement.classList.contains('alert-success') ? 'success' : 'danger';
          const message = alertElement.innerHTML;

          if (messageType === 'success') {
            const idMatch = message.match(/Card ID: (\d+)/);
            if (idMatch && idMatch[1]) {
              scannedCardId = idMatch[1];
              scanResultModal.classList.remove('show');
              registerConfirmContent.innerHTML = `
                <div class="alert alert-info">
                    <p><strong>User:</strong> ${selectedUserName}</p>
                    <p><strong>Card ID:</strong> ${scannedCardId}</p>
                    <p>Do you want to confirm the registration of this card for this user?</p>
                </div>
              `;
              registerConfirmModal.classList.add('show');
              return;
            }
          }

            scanModalTitle.textContent = "Scan Result";
          scanResultContent.innerHTML = `<div class="alert alert-${messageType}">${message}</div>`;
        } else {
          scanModalTitle.textContent = "Error";
          scanResultContent.innerHTML = `
            <div class="alert alert-danger">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
                Could not process the server response.
            </div>
          `;
        }
      };

      xhr.onerror = function() {
        scanModalTitle.textContent = "Error";
        scanResultContent.innerHTML = `
          <div class="alert alert-danger">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            Connection error. Please try again.
          </div>
        `;
      };

      xhr.send('scan_rfid=1');
    });
  }

  if (confirmFinalRegisterBtn) {
    confirmFinalRegisterBtn.addEventListener('click', function() {
      this.disabled = true;
      this.innerHTML = '<span class="spinner"></span> Procesando...';

      const formData = new FormData();
      formData.append('add_card', '1');
      formData.append('usuario_id', selectedUserId);
      formData.append('rfid_code', scannedCardId);

      const xhr = new XMLHttpRequest();
      xhr.open('POST', 'tarjetas.php', true);

      xhr.onload = function() {
        confirmFinalRegisterBtn.disabled = false;
        confirmFinalRegisterBtn.innerHTML = 'Confirm';
        registerConfirmModal.classList.remove('show');

        scanModalTitle.textContent = "Registration Completed";
        scanResultContent.innerHTML = `
          <div class="alert alert-success">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
              <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <p>The card with ID <strong>${scannedCardId}</strong> has been successfully assigned to the user <strong>${selectedUserName}</strong>.</p>
            </div>
            <div class="text-center">
            <button class="btn btn-primary" onclick="window.location.reload()">Refresh page</button>
          </div>
        `;
        scanResultModal.classList.add('show');
      };

      xhr.onerror = function() {
        confirmFinalRegisterBtn.disabled = false;
        confirmFinalRegisterBtn.innerHTML = 'Confirm';
        registerConfirmModal.classList.remove('show');

        scanModalTitle.textContent = "Error";
        scanResultContent.innerHTML = `
          <div class="alert alert-danger">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            Connection error. Please try again.
          </div>
        `;
        scanResultModal.classList.add('show');
      };

      xhr.send(formData);
    });
  }
});
</script>

<?php
// Include footer
include 'includes/footer.php';
?>
