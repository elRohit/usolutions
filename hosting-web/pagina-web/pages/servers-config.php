<?php
$pageTitle = "Configure Your Server";
require_once '../includes/header.php';
requireLogin();

// Get service and plan IDs from URL
$serviceId = isset($_GET['service']) ? (int)$_GET['service'] : 0;
$planId = isset($_GET['plan']) ? (int)$_GET['plan'] : 0;

// Validate service and plan
global $conn;
$serviceQuery = "SELECT * FROM services WHERE id = $serviceId AND is_active = 1";
$serviceResult = $conn->query($serviceQuery);

$planQuery = "SELECT * FROM server_plans WHERE id = $planId AND is_active = 1";
$planResult = $conn->query($planQuery);

if ($serviceResult->num_rows === 0 || $planResult->num_rows === 0) {
    // Invalid service or plan
    echo '<div class="container error-container"><p>Invalid service or plan selected. <a href="servers.php">Go back to servers</a></p></div>';
    include_once '../includes/footer.php';
    exit;
}

$service = $serviceResult->fetch_assoc();
$plan = $planResult->fetch_assoc();

// Check if form was submitted
$formSubmitted = isset($_POST['submit']);
$errors = [];

if ($formSubmitted) {
    // Process form submission
    if ($service['type'] === 'unmanaged') {
        // Validate unmanaged server form
        $hostname = isset($_POST['hostname']) ? trim($_POST['hostname']) : '';
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
        
        // Validate hostname
        if (empty($hostname)) {
            $errors[] = 'Hostname is required';
        } elseif (!preg_match('/^[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?$/', $hostname)) {
            $errors[] = 'Hostname must contain only lowercase letters, numbers, and hyphens, and cannot start or end with a hyphen';
        }
        
        // Validate username
        if (empty($username)) {
            $errors[] = 'Username is required';
        } elseif (!preg_match('/^[a-z_]([a-z0-9_-]{0,31})$/', $username)) {
            $errors[] = 'Username must start with a letter or underscore, and contain only lowercase letters, numbers, underscores, and hyphens';
        }
        
        // Validate password
        if (empty($password)) {
            $errors[] = 'Password is required';
        } elseif (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        } elseif ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }
        
        if (empty($errors)) {
            // Store configuration in session for payment page
            $_SESSION['server_config'] = [
                'service_id' => $serviceId,
                'plan_id' => $planId,
                'hostname' => $hostname,
                'username' => $username,
                'password' => $password
            ];
            
            // Redirect to payment page
            header('Location: servers-payment.php');
            exit;
        }
    } else {
        // For managed servers, just store service and plan IDs and redirect to payment
        $_SESSION['server_config'] = [
            'service_id' => $serviceId,
            'plan_id' => $planId
        ];
        
        // Redirect to payment page
        header('Location: servers-payment.php');
        exit;
    }
}

// Calculate total price
$totalPrice = $service['base_price'] * $plan['price_multiplier'];
?>

<!-- Add Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<section class="page-header">
    <div class="container">
        <h1>Configure Your Server</h1>
        <p>Set up your server before proceeding to payment</p>
    </div>
</section>

<section class="server-config">
    <div class="container">
        <div class="config-summary">
            <h2>Order Summary</h2>
            <div class="summary-details">
                <div class="summary-row">
                    <span class="summary-label">Service:</span>
                    <span class="summary-value"><?php echo $service['name']; ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Type:</span>
                    <span class="summary-value"><?php echo ucfirst($service['type']); ?></span>
                </div>
                <?php if ($service['type'] === 'managed'): ?>
                <div class="summary-row">
                    <span class="summary-label">Application:</span>
                    <span class="summary-value"><?php echo ucfirst($service['managed_app']); ?></span>
                </div>
                <?php endif; ?>
                <div class="summary-row">
                    <span class="summary-label">Plan:</span>
                    <span class="summary-value"><?php echo $plan['name']; ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">CPU:</span>
                    <span class="summary-value"><?php echo $plan['cpu_cores']; ?> Cores</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">RAM:</span>
                    <span class="summary-value"><?php echo $plan['ram_gb']; ?> GB</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Storage:</span>
                    <span class="summary-value"><?php echo $plan['storage_gb']; ?> GB</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Bandwidth:</span>
                    <span class="summary-value"><?php echo $plan['bandwidth_tb']; ?> TB</span>
                </div>
                <div class="summary-row total">
                    <span class="summary-label">Total:</span>
                    <span class="summary-value"><?php echo formatCurrency($totalPrice); ?>/month</span>
                </div>
            </div>
        </div>
        
        <?php if (!empty($errors)): ?>
        <div class="error-messages">
            <ul>
                <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <div class="config-form">
            <?php if ($service['type'] === 'unmanaged'): ?>
            <h2>Server Configuration</h2>
            <p>Please provide the following information to set up your unmanaged server:</p>
            
            <form method="post" action="">
                <div class="form-group">
                    <label for="hostname">Hostname:</label>
                    <input type="text" id="hostname" name="hostname" value="<?php echo isset($_POST['hostname']) ? htmlspecialchars($_POST['hostname']) : ''; ?>" required>
                    <small>Example: myserver, web-server, etc.</small>
                </div>
                
                <div class="form-group">
                    <label for="username">Admin Username:</label>
                    <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                    <small>This user will have administrator privileges.</small>
                </div>
                
                <div class="form-group">
                    <label for="password">Admin Password:</label>
                    <input type="password" id="password" name="password" required>
                    <small>Must be at least 8 characters long.</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="submit" class="btn btn-primary"><i class="fas fa-check"></i> Proceed to Payment</button>
                    <a href="servers.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                </div>
            </form>
            <?php else: ?>
            <h2>Managed Server</h2>
            <p>Your <?php echo ucfirst($service['managed_app']); ?> server will be fully configured by our team. No additional configuration is required at this time.</p>
            
            <form method="post" action="">
                <div class="form-actions">
                    <button type="submit" name="submit" class="btn btn-primary"><i class="fas fa-check"></i> Proceed to Payment</button>
                    <a href="servers.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Include page-specific JavaScript -->
<script src="../assets/js/pages/servers-config.js"></script>

<?php include_once '../includes/footer.php'; ?>

