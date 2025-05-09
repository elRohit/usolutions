<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'usolutions_admin');
define('DB_PASS', 'P@ssw0rd');
define('DB_NAME', 'usolutions_db');

// Site configuration
define('SITE_NAME', 'USOLUTIONS');
define('SITE_URL', 'http://localhost/');

// Services configuration (used in home.php)
define('SERVICES', [
    'web-hosting' => [
        'name' => 'Web Hosting',
        'description' => 'Fast and reliable web hosting for your websites',
        'icon' => 'fas fa-globe'
    ],
    'managed-servers' => [
        'name' => 'Managed Servers',
        'description' => 'Fully managed WordPress and PrestaShop Hosting',
        'icon' => 'fas fa-server'
    ],
    'unmanaged-servers' => [
        'name' => 'Unmanaged Servers',
        'description' => 'Unmanaged Debian and Ubuntu servers with full privileges',
        'icon' => 'fas fa-terminal'
    ]
]);

// Initialize database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

