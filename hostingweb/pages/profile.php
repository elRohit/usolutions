<?php
$pageTitle = "Account Settings";
include_once '../includes/header.php';

// Require login
requireLogin();

// Get user data
$userId = $_SESSION['user_id'];
$user = getUserProfile($userId);

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action === 'update_profile') {
        // Update profile information
        $firstName = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
        $lastName = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $profileImage = isset($_POST['profile_image']) ? trim($_POST['profile_image']) : '';
        
        if (empty($firstName) || empty($lastName)) {
            $error = "First name and last name are required.";
        } else {
            // Connect to database
            $conn = getDbConnection();
            
            // Prepare SQL statement
            $sql = "UPDATE users SET first_name = ?, last_name = ?, phone = ?, profile_image = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            
            if ($stmt) {
                // Bind parameters and execute
                $stmt->bind_param("ssssi", $firstName, $lastName, $phone, $profileImage, $userId);
                
                if ($stmt->execute()) {
                    $success = "Profile updated successfully.";
                    // Update session name
                    $_SESSION['user_name'] = $firstName . ' ' . $lastName;
                    // Refresh user data
                    $user = getUserProfile($userId);
                } else {
                    $error = "Failed to update profile: " . $conn->error;
                }
                
                $stmt->close();
            } else {
                $error = "Database error: " . $conn->error;
            }
            
            $conn->close();
        }
    } elseif ($action === 'change_password') {
        // Change password
        $currentPassword = isset($_POST['current_password']) ? $_POST['current_password'] : '';
        $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';
        $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
        
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $error = "All password fields are required.";
        } elseif ($newPassword !== $confirmPassword) {
            $error = "New passwords do not match.";
        } elseif (strlen($newPassword) < 8) {
            $error = "New password must be at least 8 characters long.";
        } else {
            if (updateUserPassword($userId, $currentPassword, $newPassword)) {
                $success = "Password changed successfully.";
            } else {
                $error = "Failed to change password. Please check your current password.";
            }
        }
    }
}
?>

<section class="profile">
    <div class="container">
        <div class="profile-header">
            <h1>Account Settings</h1>
            <p>Manage your personal information and security settings</p>
        </div>
        
        <div class="profile-grid">
            <div class="profile-sidebar">
                <div class="user-info">
                    <div class="user-avatar">
                        <?php if (!empty($user['profile_image'])): ?>
                            <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile Image">
                        <?php else: ?>
                            <i class="fas fa-user"></i>
                        <?php endif; ?>
                    </div>
                    <div class="user-details">
                        <h3><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
                        <p><?php echo htmlspecialchars($user['email']); ?></p>
                        <p>Balance: $<?php echo isset($user['balance']) ? number_format($user['balance'], 2) : '0.00'; ?></p>
                        <p>Status: <?php echo isset($user['status']) ? ucfirst($user['status']) : 'Active'; ?></p>
                    </div>
                </div>
                
                <nav class="profile-nav">
                    <ul>
                        <li><a href="#personal-info"><i class="fas fa-user"></i> Personal Information</a></li>
                        <li><a href="#change-password"><i class="fas fa-lock"></i> Change Password</a></li>
                        <li><a href="dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a></li>
                    </ul>
                </nav>
            </div>
            
            <div class="profile-content">
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <p><?php echo $success; ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-error">
                        <p><?php echo $error; ?></p>
                    </div>
                <?php endif; ?>
                
                <section id="personal-info" class="profile-section">
                    <h2>Personal Information</h2>
                    <form action="profile.php" method="post">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                            <small>Email cannot be changed. Contact support if you need to update your email.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                            <small>Optional. Used for account recovery and important notifications.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="profile_image">Profile Image URL</label>
                            <input type="url" id="profile_image" name="profile_image" value="<?php echo isset($user['profile_image']) ? htmlspecialchars($user['profile_image']) : ''; ?>">
                            <small>Enter a URL to your profile image. Leave empty to use the default avatar.</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Account Status</label>
                            <input type="text" value="<?php echo isset($user['status']) ? ucfirst($user['status']) : 'Active'; ?>" disabled>
                            <small>Your account status. Contact support if you need to change this.</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Account Balance</label>
                            <input type="text" value="$<?php echo isset($user['balance']) ? number_format($user['balance'], 2) : '0.00'; ?>" disabled>
                            <small>Your current account balance.</small>
                        </div>
                        
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </form>
                </section>
                
                <section id="change-password" class="profile-section">
                    <h2>Change Password</h2>
                    <form action="profile.php" method="post">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password" required>
                            <small>Password must be at least 8 characters long. Strong passwords include a mix of letters, numbers, and symbols.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-lock"></i> Change Password
                        </button>
                    </form>
                </section>
            </div>
        </div>
    </div>
</section>

<?php include_once '../includes/footer.php'; ?>
