<?php
$pageTitle = "Home";
include_once '../includes/header.php';
?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Professional Hosting Solutions</h1>
            <p>Reliable and secure hosting services tailored to your needs</p>
            <div class="hero-buttons">
                <a href="servers.php" class="btn btn-primary">Explore Services</a>
                <a href="contact.php" class="btn btn-secondary">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<section class="features">
    <div class="container">
        <h2 class="section-title">Why Choose USOLUTIONS</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Secure & Reliable</h3>
                <p>State-of-the-art security measures and 99.9% uptime guarantee</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <h3>High Performance</h3>
                <p>Optimized infrastructure for maximum speed and efficiency</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>24/7 Support</h3>
                <p>Our expert team is always available to help you</p>
            </div>
        </div>
    </div>
</section>

<section class="services-preview">
    <div class="container">
        <h2 class="section-title">Our Services</h2>
        <div class="services-grid">
            <?php foreach (SERVICES as $id => $service): ?>
            <div class="service-card">
                <div class="service-icon">
                    <i class="<?php echo $service['icon']; ?>"></i>
                </div>
                <h3><?php echo $service['name']; ?></h3>
                <p><?php echo $service['description']; ?></p>
                <a href="servers.php#<?php echo $id; ?>" class="btn btn-outline">Learn More</a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Get Started?</h2>
            <p>Join thousands of satisfied customers who trust USOLUTIONS for their hosting needs.</p>
            <a href="register.php" class="btn btn-primary">Create Account</a>
        </div>
    </div>
</section>

<?php include_once '../includes/footer.php'; ?>

