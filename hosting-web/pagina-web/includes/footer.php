<footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <h2><?php echo SITE_NAME; ?></h2>
                    <p>Professional Hosting Solutions</p>
                </div>
                <div class="footer-links">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="../pages/home.php">Home</a></li>
                        <li><a href="../pages/about.php">About Us</a></li>
                        <li><a href="../pages/servers.php">Servers</a></li>
                        <li><a href="../pages/contact.php">Contact Us</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h3>Services</h3>
                    <ul>
                        <li><a href="../pages/servers.php?type=managed">Managed Servers</a></li>
                        <li><a href="../pages/servers.php?type=unmanaged">Unmanaged Servers</a></li>
                        <li><a href="../pages/servers.php">All Services</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h3>Contact Us</h3>
                    <p><i class="fas fa-envelope"></i> support@usolutions.com</p>
                    <p><i class="fas fa-phone"></i> +36 693 43 01 36</p>
                    <p><i class="fas fa-map-marker-alt"></i> Carrer Vilar Petit, 24, 17300 Blanes, Girona</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/pages/<?php echo basename($_SERVER['PHP_SELF'], '.php'); ?>.js"></script>
</body>
</html>
