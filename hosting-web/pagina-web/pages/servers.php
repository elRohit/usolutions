<?php
$pageTitle = "Our Servers";
include_once '../includes/header.php';

$serverType = isset($_GET['type']) ? $_GET['type'] : 'all';

global $conn;
$query = "SELECT * FROM services WHERE is_active = 1";

if ($serverType === 'managed') {
    $query .= " AND type = 'managed'";
} elseif ($serverType === 'unmanaged') {
    $query .= " AND type = 'unmanaged'";
}

$result = $conn->query($query);
$services = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
}

$plansQuery = "SELECT * FROM server_plans WHERE is_active = 1 ORDER BY price_multiplier ASC";
$plansResult = $conn->query($plansQuery);
$plans = [];

if ($plansResult->num_rows > 0) {
    while ($row = $plansResult->fetch_assoc()) {
        $plans[] = $row;
    }
}
?>

<section class="page-header">
    <div class="container">
        <h1>Our Server Solutions</h1>
        <p>Reliable and secure hosting services tailored to your needs</p>
    </div>
</section>

<section class="server-types">
    <div class="container">
        <div class="server-tabs">
            <a href="servers.php" class="tab <?php echo $serverType === 'all' ? 'active' : ''; ?>">All Servers</a>
            <a href="servers.php?type=managed" class="tab <?php echo $serverType === 'managed' ? 'active' : ''; ?>">Managed Servers</a>
            <a href="servers.php?type=unmanaged" class="tab <?php echo $serverType === 'unmanaged' ? 'active' : ''; ?>">Unmanaged Servers</a>
        </div>
    </div>
</section>

<section class="servers-list">
    <div class="container">
        <?php if (empty($services)): ?>
            <div class="no-services">
                <p>No services available at the moment. Please check back later.</p>
            </div>
        <?php else: ?>
            <?php foreach ($services as $service): ?>
                <div class="server-card" id="<?php echo strtolower(str_replace(' ', '-', $service['name'])); ?>">
                    <div class="server-header">
                        <h2><?php echo $service['name']; ?></h2>
                        <div class="server-price">
                            <span class="price"><?php echo formatCurrency($service['base_price']); ?></span>
                            <span class="period">/month</span>
                        </div>
                    </div>
                    <div class="server-content">
                        <p class="server-description"><?php echo $service['description']; ?></p>
                        
                        <?php if ($service['type'] === 'managed'): ?>
                            <div class="managed-app">
                                <span class="badge">Managed App: <?php echo ucfirst($service['managed_app']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="server-features">
                            <h3>Features:</h3>
                            <ul>
                                <?php if ($service['type'] === 'managed'): ?>
                                    <li><i class="fas fa-check"></i> Fully managed environment</li>
                                    <li><i class="fas fa-check"></i> Automatic updates</li>
                                    <li><i class="fas fa-check"></i> Daily backups</li>
                                    <li><i class="fas fa-check"></i> Security monitoring</li>
                                    <li><i class="fas fa-check"></i> 24/7 expert support</li>
                                <?php else: ?>
                                    <li><i class="fas fa-check"></i> Full root access</li>
                                    <li><i class="fas fa-check"></i> Choice of OS</li>
                                    <li><i class="fas fa-check"></i> Custom software installation</li>
                                    <li><i class="fas fa-check"></i> SSH access</li>
                                    <li><i class="fas fa-check"></i> Technical support</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        
                        <div class="server-plans">
                            <h3>Choose a Plan:</h3>
                            <div class="plans-grid">
                                <?php foreach ($plans as $plan): ?>
                                    <div class="plan-option">
                                        <h4><?php echo $plan['name']; ?></h4>
                                        <ul>
                                            <li><strong>CPU:</strong> <?php echo $plan['cpu_cores']; ?> Cores</li>
                                            <li><strong>RAM:</strong> <?php echo $plan['ram_gb']; ?> GB</li>
                                            <li><strong>Storage:</strong> <?php echo $plan['storage_gb']; ?> GB</li>
                                            <li><strong>Bandwidth:</strong> <?php echo $plan['bandwidth_tb']; ?> TB</li>
                                        </ul>
                                        <div class="plan-price">
                                            <?php echo formatCurrency($service['base_price'] * $plan['price_multiplier']); ?>/month
                                        </div>
                                        <?php if (isLoggedIn()): ?>
                                            <a href="servers-config.php?service=<?php echo $service['id']; ?>&plan=<?php echo $plan['id']; ?>" class="btn btn-primary">Order Now</a>
                                        <?php else: ?>
                                            <a href="login.php?redirect=servers.php" class="btn btn-primary">Login to Order</a>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<section class="server-faq">
    <div class="container">
        <h2 class="section-title">Frequently Asked Questions</h2>
        <div class="faq-grid">
            <div class="faq-item">
                <h3>What is the difference between managed and unmanaged servers?</h3>
                <p>Managed servers come with pre-installed applications (WordPress or PrestaShop) and are fully managed by our team, including updates, security, and backups. Unmanaged servers provide you with root access to a clean machine, giving you complete control over the server configuration.</p>
            </div>
            <div class="faq-item">
                <h3>Can I upgrade my server plan later?</h3>
                <p>Yes, you can upgrade your server plan at any time. The upgrade process is seamless and typically takes just a few minutes to complete.</p>
            </div>
            <div class="faq-item">
                <h3>How do I access my server?</h3>
                <p>For managed servers, you'll receive access to a control panel. For unmanaged servers, you'll receive SSH credentials to access your server via terminal.</p>
            </div>
        </div>
    </div>
</section>

<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2>Need Help Choosing?</h2>
            <p>Our experts are ready to help you select the right server solution for your needs.</p>
            <a href="contact.php" class="btn btn-primary">Contact Us</a>
        </div>
    </div>
</section>

<?php include_once '../includes/footer.php'; ?>

