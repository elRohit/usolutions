<?php
$pageTitle = "Payment";
require_once '../includes/header.php';
requireLogin();

if (!isset($_SESSION['server_config']) || empty($_SESSION['server_config'])) {
    header('Location: servers.php');
    exit;
}

$config = $_SESSION['server_config'];

global $conn;
$serviceId = $config['service_id'];
$planId = $config['plan_id'];


$serviceQuery = "SELECT * FROM services WHERE id = $serviceId AND is_active = 1";
$serviceResult = $conn->query($serviceQuery);

$planQuery = "SELECT * FROM server_plans WHERE id = $planId AND is_active = 1";
$planResult = $conn->query($planQuery);

if ($serviceResult->num_rows === 0 || $planResult->num_rows === 0) {
    echo '<div class="container error-container"><p>Invalid service or plan selected. <a href="servers.php">Go back to servers</a></p></div>';
    include_once '../includes/footer.php';
    exit;
}

$service = $serviceResult->fetch_assoc();
$plan = $planResult->fetch_assoc();

$totalPrice = $service['base_price'] * $plan['price_multiplier'];

$formSubmitted = isset($_POST['submit']);
$errors = [];

if ($formSubmitted) {
    $paymentMethod = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
    
    if (empty($paymentMethod)) {
        $errors[] = 'Please select a payment method';
    } elseif (!in_array($paymentMethod, ['card', 'bank', 'paypal'])) {
        $errors[] = 'Invalid payment method selected';
    }
    
    if (empty($errors)) {
        $_SESSION['server_config']['payment_method'] = $paymentMethod;
        
        if ($paymentMethod === 'paypal') {
            $_SESSION['server_config']['payment_status'] = 'completed';
        } else {
            $_SESSION['server_config']['payment_status'] = 'completed';
        }
        
        header('Location: /pages/servers-creation.php');
        exit;
    }
}
?>

<section class="page-header">
    <div class="container">
        <h1>Complete Your Order</h1>
        <p>Choose your payment method and complete your server order</p>
    </div>
</section>

<section class="payment-section">
    <div class="container">
        <div class="payment-container">
            <div class="order-summary">
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
                    <?php
                    $_SESSION['server_config']['service'] = $service['name'];
                    $_SESSION['server_config']['type'] = $service['type'];
                    $_SESSION['server_config']['cpu_cores'] = $plan['cpu_cores'];
                    $_SESSION['server_config']['ram_gb'] = $plan['ram_gb'];
                    $_SESSION['server_config']['storage_gb'] = $plan['storage_gb'];

                    if ($service['type'] === 'managed') {
                        $_SESSION['server_config']['managed_app'] = $service['managed_app'];
                    }
                    ?>
                    <?php if ($service['type'] === 'unmanaged'): ?>
                    <div class="summary-row">
                        <span class="summary-label">Hostname:</span>
                        <span class="summary-value"><?php echo htmlspecialchars($config['hostname']); ?></span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Username:</span>
                        <span class="summary-value"><?php echo htmlspecialchars($config['username']); ?></span>
                    </div>
                    <?php endif; ?>
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
            
            <div class="payment-form">
                <h2>Payment Method</h2>
                <p>Please select your preferred payment method:</p>
                
                <form method="post" action="">
                    <div class="payment-methods">
                        <div class="payment-method">
                            <input type="radio" id="card" name="payment_method" value="card" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] === 'card') ? 'checked' : ''; ?>>
                            <label for="card">
                                <div class="payment-icon card-icon">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div class="payment-info">
                                    <h3>Credit/Debit Card</h3>
                                    <p>Pay securely with your credit or debit card</p>
                                </div>
                            </label>
                        </div>
                        
                        <div class="payment-method">
                            <input type="radio" id="bank" name="payment_method" value="bank" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] === 'bank') ? 'checked' : ''; ?>>
                            <label for="bank">
                                <div class="payment-icon bank-icon">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div class="payment-info">
                                    <h3>Bank Transfer</h3>
                                    <p>Pay directly from your bank account</p>
                                </div>
                            </label>
                        </div>
                        
                        <div class="payment-method">
                            <input type="radio" id="paypal" name="payment_method" value="paypal" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] === 'paypal') ? 'checked' : ''; ?>>
                            <label for="paypal">
                                <div class="payment-icon paypal-icon">
                                    <i class="fab fa-paypal"></i>
                                </div>
                                <div class="payment-info">
                                    <h3>PayPal</h3>
                                    <p>Pay securely with PayPal (Free for testing)</p>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <div class="payment-notice">
                        <p><strong>Note:</strong> This is a testing environment. No actual payments will be processed.</p>
                        <p>For testing purposes, PayPal is free to use.</p>
                    </div>
                    
                    <div class="form-actions">
                        <a href="servers-creation.php" class="btn btn-primary">Complete Payment</a>
                        <a href="servers-config.php?service=<?php echo $serviceId; ?>&plan=<?php echo $planId; ?>" class="btn btn-secondary">Back to Configuration</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include_once '../includes/footer.php'; ?>

