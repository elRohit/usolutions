<?php
$pageTitle = "Creating Your Server";
require_once '../includes/header.php';
session_start();

// Verificar si la configuración del servidor está en la sesión
if (!isset($_SESSION['server_config']) || empty($_SESSION['server_config'])) {
    echo '<div class="container error-container"><p>No server configuration found. <a href="servers-config.php">Go back to configuration</a></p></div>';
    include_once '../includes/footer.php';
    exit;
}

$config = $_SESSION['server_config'];
?>

<!-- Main Content -->
<style>
/* Server Creation Page Styles */

/* Using the site's CSS variables for consistency */
:root {
    --primary-color: #0066cc;
    --primary-dark: #0056b3;
    --primary-light: #e6f0ff;
    --secondary-color: #6c757d;
    --secondary-dark: #5a6268;
    --success-color: #28a745;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
    --info-color: #17a2b8;
    --light-color: #f8f9fa;
    --dark-color: #343a40;
    --body-bg: #f8f9fa;
    --body-color: #333;
    --border-color: #e9ecef;
    --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
    --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
    --border-radius: 0.25rem;
    --border-radius-lg: 0.5rem;
    --transition: all 0.3s ease;
    --font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    --font-size-base: 1rem;
    --font-size-sm: 0.875rem;
    --font-size-lg: 1.25rem;
    --font-weight-normal: 400;
    --font-weight-medium: 500;
    --font-weight-bold: 700;
    --line-height: 1.6;
  }
  
  /* Page Header */
  .page-header {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: #fff;
    padding: 4rem 0;
    text-align: center;
    position: relative;
    overflow: hidden;
    margin-bottom: 4rem;
  }
  
  .page-header::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url("../images/pattern.svg");
    opacity: 0.1;
  }
  
  .page-header h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: #fff;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    animation: fadeInDown 1s ease-out;
  }
  
  .page-header p {
    font-size: 1.25rem;
    opacity: 0.9;
    max-width: 700px;
    margin: 0 auto;
    animation: fadeInUp 1s ease-out 0.3s forwards;
    opacity: 0;
  }
  
  /* Success and Error Containers */
  .success-container,
  .error-container {
    max-width: 800px;
    margin: 4rem auto;
    padding: 2.5rem;
    border-radius: var(--border-radius-lg);
    text-align: center;
    box-shadow: var(--shadow);
    font-family: var(--font-family);
    transition: var(--transition);
  }
  
  .success-container:hover,
  .error-container:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-5px);
  }
  
  .success-container {
    background-color: #e7f7ed;
    border: 1px solid var(--success-color);
  }
  
  .error-container {
    background-color: #f8d7da;
    border: 1px solid var(--danger-color);
  }
  
  .success-container h2,
  .error-container h2 {
    margin-top: 0;
    margin-bottom: 1.5rem;
    font-size: 2rem;
    font-weight: var(--font-weight-bold);
    position: relative;
    padding-bottom: 0.75rem;
  }
  
  .success-container h2::after,
  .error-container h2::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 50px;
    height: 3px;
    border-radius: 1.5px;
  }
  
  .success-container h2 {
    color: var(--success-color);
  }
  
  .success-container h2::after {
    background-color: var(--success-color);
  }
  
  .error-container h2 {
    color: var(--danger-color);
  }
  
  .error-container h2::after {
    background-color: var(--danger-color);
  }
  
  .success-container p,
  .error-container p {
    margin-bottom: 1.25rem;
    font-size: var(--font-size-base);
    line-height: var(--line-height);
    color: var(--body-color);
  }
  
  .success-container a,
  .error-container a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-top: 1.5rem;
    padding: 0.75rem 1.5rem;
    background-color: var(--primary-color);
    color: #fff;
    text-decoration: none;
    border-radius: var(--border-radius);
    font-weight: var(--font-weight-medium);
    transition: var(--transition);
    box-shadow: 0 2px 4px rgba(0, 102, 204, 0.3);
    position: relative;
    overflow: hidden;
  }
  
  .success-container a::after,
  .error-container a::after {
    content: "";
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: -100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: 0.5s;
  }
  
  .success-container a:hover::after,
  .error-container a:hover::after {
    left: 100%;
  }
  
  .success-container a:hover,
  .error-container a:hover {
    background-color: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 102, 204, 0.4);
  }
  
  /* Loading Container */
  .loading-container {
    max-width: 800px;
    margin: 4rem auto;
    padding: 2.5rem;
    border-radius: var(--border-radius-lg);
    text-align: center;
    background-color: #fff;
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow);
    font-family: var(--font-family);
    transition: var(--transition);
  }
  
  .loading-container:hover {
    box-shadow: var(--shadow-lg);
  }
  
  .loading-title {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: var(--primary-color);
    font-weight: var(--font-weight-bold);
    position: relative;
    padding-bottom: 0.75rem;
  }
  
  .loading-title::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 50px;
    height: 3px;
    background-color: var(--primary-color);
    border-radius: 1.5px;
  }
  
  .loading-message {
    font-size: var(--font-size-base);
    margin-bottom: 2rem;
    color: var(--secondary-color);
    line-height: var(--line-height);
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
  }
  
  /* Loading Spinner */
  .loading-spinner {
    display: inline-block;
    width: 60px;
    height: 60px;
    border: 5px solid rgba(0, 102, 204, 0.2);
    border-radius: 50%;
    border-top-color: var(--primary-color);
    animation: spin 1s ease-in-out infinite;
    margin: 1.5rem auto;
  }
  
  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }
  
  /* Loading Bar */
  .loading-bar-container {
    height: 20px;
    background-color: var(--light-color);
    border-radius: 10px;
    margin: 2.5rem 0;
    overflow: hidden;
    position: relative;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
  }
  
  .loading-bar {
    height: 100%;
    width: 30%;
    background: linear-gradient(90deg, var(--primary-color), var(--info-color));
    border-radius: 10px;
    position: absolute;
    animation: loading-bar-animation 2.5s infinite ease-in-out;
    box-shadow: 0 0 10px rgba(0, 102, 204, 0.3);
  }
  
  @keyframes loading-bar-animation {
    0% {
      left: -30%;
      width: 30%;
    }
    50% {
      width: 40%;
    }
    100% {
      left: 100%;
      width: 30%;
    }
  }
  
  .estimated-time {
    font-size: var(--font-size-sm);
    color: var(--secondary-color);
    margin-top: 1rem;
    font-style: italic;
  }
  
  .time-counter {
    display: inline-block;
    background-color: var(--light-color);
    padding: 0.25rem 0.5rem;
    border-radius: var(--border-radius);
    font-family: monospace;
    font-size: var(--font-size-base);
    color: var(--dark-color);
    margin-left: 0.25rem;
    border: 1px solid var(--border-color);
  }
  
  /* Loading Steps */
  .loading-steps {
    text-align: left;
    max-width: 600px;
    margin: 2.5rem auto;
    padding: 1.5rem;
    background-color: #fff;
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
  }
  
  .loading-steps h3 {
    margin-top: 0;
    margin-bottom: 1.25rem;
    font-size: var(--font-size-lg);
    color: var(--primary-color);
    font-weight: var(--font-weight-bold);
    position: relative;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--border-color);
  }
  
  /* Creation Steps */
  .creation-steps {
    max-width: 600px;
    margin: 2rem auto;
    padding: 0;
    list-style: none;
  }
  
  .creation-steps li {
    position: relative;
    padding: 1rem 0 1rem 3.5rem;
    border-left: 2px solid var(--border-color);
    margin-left: 1.25rem;
    transition: var(--transition);
    opacity: 0.6;
  }
  
  .creation-steps li:last-child {
    border-left: 2px solid transparent;
  }
  
  .creation-steps li::before {
    content: "";
    position: absolute;
    left: -11px;
    top: 1rem;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background-color: var(--border-color);
    transition: var(--transition);
    z-index: 1;
  }
  
  .creation-steps li.step {
    opacity: 0.6;
  }
  
  .creation-steps li.completed {
    opacity: 1;
    border-left-color: var(--success-color);
  }
  
  .creation-steps li.completed::before {
    background-color: var(--success-color);
    box-shadow: 0 0 0 4px rgba(40, 167, 69, 0.2);
  }
  
  .creation-steps li.current {
    opacity: 1;
    border-left-color: var(--warning-color);
  }
  
  .creation-steps li.current::before {
    background-color: var(--warning-color);
    animation: pulse 1.5s infinite;
  }
  
  @keyframes pulse {
    0% {
      box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
    }
    70% {
      box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
    }
    100% {
      box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
    }
  }
  
  .creation-steps h3 {
    margin: 0 0 0.5rem;
    font-size: 1.1rem;
    font-weight: var(--font-weight-medium);
    color: var(--dark-color);
  }
  
  .creation-steps p {
    margin: 0;
    color: var(--secondary-color);
    font-size: var(--font-size-sm);
    line-height: var(--line-height);
  }
  
  /* Redirect Message */
  .redirect-message {
    margin-top: 2.5rem;
    padding: 1rem;
    background-color: var(--light-color);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
    font-size: var(--font-size-base);
    color: var(--secondary-color);
  }
  
  .redirect-countdown {
    font-weight: var(--font-weight-bold);
    color: var(--primary-color);
    font-size: 1.25rem;
  }
  
  /* Success Container Enhancements */
  .success-header {
    margin-bottom: 2rem;
  }
  
  .success-icon {
    font-size: 4rem;
    color: var(--success-color);
    margin-bottom: 1rem;
    animation: scaleIn 0.5s ease-out;
  }
  
  @keyframes scaleIn {
    0% {
      transform: scale(0);
      opacity: 0;
    }
    100% {
      transform: scale(1);
      opacity: 1;
    }
  }
  
  .server-details {
    background-color: rgba(255, 255, 255, 0.7);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    margin-bottom: 2rem;
    border: 1px solid rgba(40, 167, 69, 0.2);
  }
  
  .detail-item {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.5rem;
  }
  
  .detail-label {
    font-weight: var(--font-weight-medium);
    margin-right: 0.5rem;
    color: var(--secondary-color);
  }
  
  .detail-value {
    font-family: monospace;
    font-size: 1.1rem;
    color: var(--dark-color);
    background-color: var(--light-color);
    padding: 0.25rem 0.75rem;
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
  }
  
  .redirect-info {
    margin-bottom: 2rem;
  }
  
  .progress-bar {
    height: 8px;
    background-color: var(--light-color);
    border-radius: 4px;
    overflow: hidden;
    margin-top: 0.75rem;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
  }
  
  .progress-fill {
    height: 100%;
    width: 0;
    background-color: var(--success-color);
    border-radius: 4px;
    transition: width 1s linear;
  }
  
  .action-buttons {
    margin-top: 1.5rem;
  }
  
  .action-buttons .btn {
    padding: 0.75rem 2rem;
    font-size: 1.1rem;
    transition: all 0.3s ease;
  }
  
  .action-buttons .btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  }
  
  /* Animations */
  @keyframes fadeInDown {
    from {
      opacity: 0;
      transform: translateY(-30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  /* Responsive Styles */
  @media (max-width: 992px) {
    .page-header h1 {
      font-size: 2.5rem;
    }
  
    .loading-title,
    .success-container h2,
    .error-container h2 {
      font-size: 1.75rem;
    }
  }
  
  @media (max-width: 768px) {
    .page-header {
      padding: 3rem 0;
    }
  
    .page-header h1 {
      font-size: 2.25rem;
    }
  
    .page-header p {
      font-size: 1.1rem;
    }
  
    .loading-container,
    .success-container,
    .error-container {
      margin: 3rem auto;
      padding: 1.5rem;
      width: 90%;
    }
  
    .loading-title,
    .success-container h2,
    .error-container h2 {
      font-size: 1.5rem;
    }
  
    .loading-message {
      font-size: 0.95rem;
    }
  
    .creation-steps {
      margin: 1.5rem auto;
    }
  
    .creation-steps li {
      padding: 0.75rem 0 0.75rem 2.5rem;
    }
  
    .loading-steps {
      padding: 1.25rem;
      margin: 1.5rem auto;
    }
  }
  
  @media (max-width: 576px) {
    .page-header h1 {
      font-size: 1.75rem;
    }
  
    .page-header p {
      font-size: 1rem;
    }
  
    .loading-title,
    .success-container h2,
    .error-container h2 {
      font-size: 1.35rem;
    }
  
    .loading-spinner {
      width: 50px;
      height: 50px;
    }
  
    .creation-steps h3 {
      font-size: 1rem;
    }
  
    .creation-steps p {
      font-size: 0.85rem;
    }
  }
  
</style>
<div class="container loading-container">
    <div class="loading-header">
        <h2 class="loading-title">Creating Your Server</h2>
        <p class="loading-subtitle">Please wait while we set up your server. This process typically takes up to 7 minutes to complete.</p>
    </div>
    
    <div class="loading-progress">
        <div class="loading-bar-container">
            <div class="loading-bar"></div>
        </div>
    </div>
    
    <div class="loading-notice">
        <p>Please do not close this window. You will be redirected to your dashboard when the process is complete.</p>
    </div>
</div>


<?php
include_once '../includes/footer.php';
// Flush output buffer to show the loading bar immediately
ob_flush();
flush();

$proxmoxURL = 'https://192.168.5.251:8006/api2/json/';
$proxmox_api_id = 'terraform@pam!terraform';
$proxmox_api_secret = '648c31a6-5bd0-4718-8901-d1b9fe0381cc';

$storage = 'USol-Data';

$server_form = $config['type'];
$service = $config['service'];

if ($server_form == 'unmanaged') {
    $hostname   = $config['hostname'];
    $username   = $config['username'];
    $password   = $config['password'];
    $cpu_cores  = $config['cpu_cores'];
    $ram        = $config['ram_gb'] * 1024;
    $size       = $config['storage_gb'];
    
    if ($service == 'Unmanaged Debian Server') {
        $template = 'Isos:vztmpl/debian-12-standard_12.7-1_amd64.tar.zst';
    }
    
    if ($service == 'Unmanaged Ubuntu Server') {
        $template = 'Isos:vztmpl/ubuntu-24.10-standard_24.10-1_amd64.tar.zst';
    }

    $terraform_format = <<<EOT
terraform {
    required_providers {
        proxmox = {
            source  = "telmate/proxmox"
            version = "2.9.11"
        }
    }
}

provider "proxmox" {
    pm_api_url           = "$proxmoxURL"
    pm_api_token_id      = "$proxmox_api_id"
    pm_api_token_secret  = "$proxmox_api_secret"
    pm_tls_insecure      = true
}

resource "proxmox_lxc" "$hostname" {
    count             = 1
    target_node       = "node1"
    hostname          = "$hostname"
    ostemplate        = "$template"
    password          = "P@ssw0rd"
    unprivileged      = true
    cores             = $cpu_cores
    memory            = $ram
    swap              = 512

    rootfs {
        storage = "USol-Data"
        size    = "{$size}G"
    }

    network {
        name   = "eth0"
        bridge = "vmbr0"
        ip     = "dhcp"
    }

    start  = true
    onboot = true

    features {
        nesting = true
    }

    provisioner "local-exec" {
        command = <<-EOF
#!/bin/bash
CONTAINER_ID=\$(echo \${self.id} | cut -d'/' -f3)

if ping -c 1 192.168.5.251 &> /dev/null; then
  NODE_IP="192.168.5.251"
elif ping -c 1 192.168.5.252 &> /dev/null; then
  NODE_IP="192.168.5.252"
elif ping -c 1 192.168.5.253 &> /dev/null; then
  NODE_IP="192.168.5.253"
else
  exit 1
fi

sshpass -p 'P@ssw0rd' ssh -o StrictHostKeyChecking=no root@\$NODE_IP "pct exec \$CONTAINER_ID -- bash -c \"apt-get update && apt-get install -y sudo && useradd -m -s /bin/bash {$username} && echo {$username}:{$password} | chpasswd && usermod -aG sudo {$username}\""
sshpass -p 'P@ssw0rd' ssh -o StrictHostKeyChecking=no root@\$NODE_IP "ha-manager add ct:\$CONTAINER_ID --group hanodes --state started"

EOF
    }
}
EOT;
    // Se cambia al directorio correspondiente
    shell_exec("cd /var/www/html/pages");
        
    // Se escribe el archivo de Terraform
    $terraformFile = 'main.tf';
    file_put_contents($terraformFile, $terraform_format);
    
    // Inicializar Terraform
    shell_exec("terraform init");
    $output2 = shell_exec("terraform apply -auto-approve");

    // Se limpian los archivos generados por Terraform
    shell_exec("rm -rf .terraform terraform.tfstate*");
    shell_exec("rm -rf .terraform.lock.hcl");
    shell_exec("rm -rf main.tf");
    shell_exec("touch main.tf");
    shell_exec("chmod 777 main.tf");

    // Se obtiene la IP del nodo
    if (shell_exec("ping -c 1 192.168.5.251 &> /dev/null")) {
        $nodo_ip = "192.168.5.251";
    } elseif (shell_exec("ping -c 1 192.168.5.252 &> /dev/null")) {
        $nodo_ip = "192.168.5.252";
    } elseif (shell_exec("ping -c 1 192.168.5.252 &> /dev/null")) {
        $nodo_ip = "192.168.5.253";
    }

    $ssh = "sshpass -p 'P@ssw0rd' ssh -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null root@$nodo_ip";

    // Obtener ID del contenedor
    $comanda_id = "$ssh \"pct list | grep $hostname | awk '{print \$1}'\"";
    $contenedor_id = shell_exec($comanda_id);
    $contenedor_id = substr($contenedor_id, 0, 3);

    // Obtener IP del contenedor
    $comanda_ip = "$ssh \"pct exec $contenedor_id -- hostname -I\"";
    $contenedor_ip = trim(shell_exec($comanda_ip));

    if (strpos($output2, 'Apply complete! Resources: 1 added, 0 changed, 0 destroyed.') !== false) {
        // Add database operations here
        global $conn;
        $userId = $_SESSION['user_id'];
        $serviceId = $config['service_id'];
        $planId = $config['plan_id'];
        
        // Get service and plan details
        $serviceQuery = "SELECT * FROM services WHERE id = $serviceId AND is_active = 1";
        $serviceResult = $conn->query($serviceQuery);
        $service = $serviceResult->fetch_assoc();
        
        $planQuery = "SELECT * FROM server_plans WHERE id = $planId AND is_active = 1";
        $planResult = $conn->query($planQuery);
        $plan = $planResult->fetch_assoc();
        
        // Calculate total price
        $totalPrice = $service['base_price'] * $plan['price_multiplier'];
        
        // Insert server record into database
        $insertServerQuery = "INSERT INTO user_servers (
            user_id, 
            service_id, 
            plan_id, 
            server_name, 
            hostname, 
            status, 
            purchase_date, 
            expiry_date, 
            auto_renew, 
            billing_cycle, 
            notes, 
            created_at
        ) VALUES (
            $userId,
            $serviceId,
            $planId,
            '$hostname',
            '$contenedor_ip',
            'active',
            NOW(),
            DATE_ADD(NOW(), INTERVAL 1 MONTH),
            1,
            'monthly',
            'Created via Terraform',
            NOW()
        )";
        mysqli_query($conn, $insertServerQuery);

    } else {
        echo '<div class="container error-container"><p>Error creating server: ' . htmlspecialchars($output2) . '</p></div>';
    }
} elseif ($server_form == 'managed') {
    $cpu_cores = $config['cpu_cores'];
    $ram = $config['ram_gb'] * 1024;
    $size = $config['storage_gb'];
    global $conn;

    // Get the managed app type
    $managedApp = '';
    if ($service == 'WordPress Managed Hosting') {
        $managedApp = 'wordpress';
    } elseif ($service == 'PrestaShop Managed Hosting') {
        $managedApp = 'prestashop';
    }

     // Get current count from count_administrative table
    $countQuery = "SELECT count FROM count_administrative WHERE service_type = '$managedApp'";
    $countResult = $conn->query($countQuery);
    
    if ($countResult->num_rows === 0) {
        // If no record exists, create one
        $conn->query("INSERT INTO count_administrative (service_type, count) VALUES ('$managedApp', 1)");
        $currentCount = 1;
    } else {
        $countRow = $countResult->fetch_assoc();
        $currentCount = $countRow['count'] + 1;
        
        // Update count in database
        $updateCountQuery = "UPDATE count_administrative SET count = $currentCount WHERE service_type = '$managedApp'";
        $conn->query($updateCountQuery);
    }

    // Create hostname based on app and count
    $hostname = $managedApp . '-' . $currentCount;

    if ($service == 'WordPress Managed Hosting') {
        $template = 'Isos:vztmpl/wordpress-6.8-1_amd64.tar.zst';
        $terraform_format = <<<EOT
terraform {
    required_providers {
        proxmox = {
            source  = "telmate/proxmox"
            version = "2.9.11"
        }
    }
}

provider "proxmox" {
    pm_api_url           = "$proxmoxURL"
    pm_api_token_id      = "$proxmox_api_id"
    pm_api_token_secret  = "$proxmox_api_secret"
    pm_tls_insecure      = true
}

resource "proxmox_lxc" "$hostname" {
    count             = 1
    target_node       = "node1"
    hostname          = "$hostname"
    ostemplate        = "$template"
    password          = "P@ssw0rd"
    unprivileged      = true
    cores             = $cpu_cores
    memory            = $ram
    swap              = 512
                
    rootfs {
        storage = "USol-Data"
        size    = "{$size}G"
    }
                
    network {
        name   = "eth0"
        bridge = "vmbr0"
        ip     = "dhcp"
    }
                
    start             = true
    onboot            = true

    features {
        nesting = true
    }

    provisioner "local-exec" {
        command = <<-EOF
#!/bin/bash

CONTAINER_ID=\$(echo \${self.id} | cut -d'/' -f3)

if ping -c 1 192.168.5.251 &> /dev/null; then
    NODE_IP="192.168.5.251"
elif ping -c 1 192.168.5.252 &> /dev/null; then
    NODE_IP="192.168.5.252"
elif ping -c 1 192.168.5.253 &> /dev/null; then
    NODE_IP="192.168.5.253"
else
    exit 1
fi

sshpass -p 'P@ssw0rd' ssh -o StrictHostKeyChecking=no root@\$NODE_IP "pct exec \$CONTAINER_ID -- bash -c 'hostnamectl set-hostname $hostname && apt update && apt install -y zabbix-agent2 && sed -i \"s/^Server=.*/Server=192.168.5.249/\" /etc/zabbix/zabbix_agent2.conf && sed -i \"s/^ServerActive=.*/ServerActive=192.168.5.249/\" /etc/zabbix/zabbix_agent2.conf && sed -i \"s/^Hostname=.*/Hostname=$hostname/\" /etc/zabbix/zabbix_agent2.conf && systemctl enable zabbix-agent2 && systemctl restart zabbix-agent2'"
sshpass -p 'P@ssw0rd' ssh -o StrictHostKeyChecking=no root@\$NODE_IP "pct exec \$CONTAINER_ID -- bash -c 'echo PermitRootLogin yes >> /etc/ssh/sshd_config && systemctl restart sshd'"
sshpass -p 'P@ssw0rd' ssh -o StrictHostKeyChecking=no root@\$NODE_IP "ha-manager add ct:\$CONTAINER_ID --group hanodes --state started"

EOF
    }
}
EOT;
        // Se cambia al directorio correspondiente
        shell_exec("cd /var/www/html/pages");
        
        // Se escribe el archivo de Terraform
        $terraformFile = 'main.tf';
        file_put_contents($terraformFile, $terraform_format);
        
        // Inicializar Terraform
        shell_exec("terraform init");
        $output2 = shell_exec("terraform apply -auto-approve");

        // Se limpian los archivos generados por Terraform
        shell_exec("rm -rf .terraform terraform.tfstate*");
        shell_exec("rm -rf .terraform.lock.hcl");
        shell_exec("rm -rf main.tf");
        shell_exec("touch main.tf");
        shell_exec("chmod 777 main.tf");

        // Se obtiene la IP del nodo
        if (shell_exec("ping -c 1 192.168.5.251 &> /dev/null")) {
            $nodo_ip = "192.168.5.251";
        } elseif (shell_exec("ping -c 1 192.168.5.252 &> /dev/null")) {
            $nodo_ip = "192.168.5.252";
        } elseif (shell_exec("ping -c 1 192.168.5.252 &> /dev/null")) {
            $nodo_ip = "192.168.5.253";
        }

        $ssh = "sshpass -p 'P@ssw0rd' ssh -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null root@$nodo_ip";

        // Obtener ID del contenedor
        $comanda_id = "$ssh \"pct list | grep $hostname | awk '{print \$1}'\"";
        $contenedor_id = shell_exec($comanda_id);
        $contenedor_id = substr($contenedor_id, 0, 3);

        // Obtener IP del contenedor
        $comanda_ip = "$ssh \"pct exec $contenedor_id -- hostname -I\"";
        $contenedor_ip = trim(shell_exec($comanda_ip));

        // Nueva variable SSH2
        $ssh2 = "sshpass -p 'P@ssw0rd' ssh -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null root@$contenedor_ip";

        // Crear carpetas dentro del contenedor
        $comanda_crear = "$ssh2 \"mkdir -p /root/tmp_scripts\"";
        $output = shell_exec($comanda_crear . " 2>&1");

        // Subir el script al contenedor
        $comanda_subir = "$ssh \"pct push $contenedor_id /root/tmp_scripts/wp_cleanup.sh /root/wp_cleanup.sh\"";
        $output = shell_exec($comanda_subir . " 2>&1");

        // Dar permisos al script
        $comanda_permisos = "$ssh2 \"chmod +x /root/wp_cleanup.sh\"";
        $output = shell_exec($comanda_permisos . " 2>&1");

        // Ejecutar el script
        $comanda_cleanup = "$ssh2 \"/root/wp_cleanup.sh\"";
        $output = shell_exec($comanda_cleanup . " 2>&1");

        if (strpos($output2, 'Apply complete! Resources: 1 added, 0 changed, 0 destroyed.') !== false) {
            // Add database operations here
            global $conn;
            $userId = $_SESSION['user_id'];
            $serviceId = $config['service_id'];
            $planId = $config['plan_id'];
            
            // Get service and plan details
            $serviceQuery = "SELECT * FROM services WHERE id = $serviceId AND is_active = 1";
            $serviceResult = $conn->query($serviceQuery);
            $service = $serviceResult->fetch_assoc();
            
            $planQuery = "SELECT * FROM server_plans WHERE id = $planId AND is_active = 1";
            $planResult = $conn->query($planQuery);
            $plan = $planResult->fetch_assoc();
            
            // Calculate total price
            $totalPrice = $service['base_price'] * $plan['price_multiplier'];
            
            // Insert server record into database
            $insertServerQuery = "INSERT INTO user_servers (
                user_id, 
                service_id, 
                plan_id, 
                server_name, 
                hostname, 
                status, 
                purchase_date, 
                expiry_date, 
                auto_renew, 
                billing_cycle, 
                notes, 
                created_at
            ) VALUES (
                $userId,
                $serviceId,
                $planId,
                '$hostname',
                '$contenedor_ip',
                'active',
                NOW(),
                DATE_ADD(NOW(), INTERVAL 1 MONTH),
                1,
                'monthly',
                'Created via Terraform',
                NOW()
            )";
            mysqli_query($conn, $insertServerQuery);

        } else {
            echo '<div class="container error-container"><p>Error creating server: ' . htmlspecialchars($output2) . '</p></div>';
        }

    } elseif ($service == 'PrestaShop Managed Hosting') {
        $template = 'Isos:vztmpl/prestashop-8.2-1_amd64.tar.zst';
        $terraform_format = <<<EOT
terraform {
    required_providers {
        proxmox = {
            source  = "telmate/proxmox"
            version = "2.9.11"
        }
    }
}

provider "proxmox" {
    pm_api_url           = "$proxmoxURL"
    pm_api_token_id      = "$proxmox_api_id"
    pm_api_token_secret  = "$proxmox_api_secret"
    pm_tls_insecure      = true
}

resource "proxmox_lxc" "$hostname" {
    count             = 1
    target_node       = "node1"
    hostname          = "$hostname"
    ostemplate        = "$template"
    password          = "P@ssw0rd"
    unprivileged      = true
    cores             = $cpu_cores
    memory            = $ram
    swap              = 512
                
    rootfs {
        storage = "USol-Data"
        size    = "{$size}G"
    }
                
    network {
        name   = "eth0"
        bridge = "vmbr0"
        ip     = "dhcp"
    }
                
    start             = true
    onboot            = true

    features {
        nesting = true
    }

    provisioner "local-exec" {
        command = <<-EOF
#!/bin/bash

CONTAINER_ID=\$(echo \${self.id} | cut -d'/' -f3)

if ping -c 1 192.168.5.251 &> /dev/null; then
    NODE_IP="192.168.5.251"
elif ping -c 1 192.168.5.252 &> /dev/null; then
    NODE_IP="192.168.5.252"
elif ping -c 1 192.168.5.253 &> /dev/null; then
    NODE_IP="192.168.5.253"
else
    exit 1
fi

sshpass -p 'P@ssw0rd' ssh -o StrictHostKeyChecking=no root@\$NODE_IP "pct exec \$CONTAINER_ID -- bash -c 'hostnamectl set-hostname $hostname && apt update && apt install -y zabbix-agent2 && sed -i \"s/^Server=.*/Server=192.168.5.249/\" /etc/zabbix/zabbix_agent2.conf && sed -i \"s/^ServerActive=.*/ServerActive=192.168.5.249/\" /etc/zabbix/zabbix_agent2.conf && sed -i \"s/^Hostname=.*/Hostname=$hostname/\" /etc/zabbix/zabbix_agent2.conf && systemctl enable zabbix-agent2 && systemctl restart zabbix-agent2'"
sshpass -p 'P@ssw0rd' ssh -o StrictHostKeyChecking=no root@\$NODE_IP "pct exec \$CONTAINER_ID -- bash -c 'echo PermitRootLogin yes >> /etc/ssh/sshd_config && systemctl restart sshd'"
sshpass -p 'P@ssw0rd' ssh -o StrictHostKeyChecking=no root@\$NODE_IP "ha-manager add ct:\$CONTAINER_ID --group hanodes --state started"
EOF
    }
}
EOT;
        // Se cambia al directorio correspondiente
        shell_exec("cd /var/www/html/pages");
        
        // Se escribe el archivo de Terraform
        $terraformFile = 'main.tf';
        file_put_contents($terraformFile, $terraform_format);
        
        // Inicializar Terraform
        shell_exec("terraform init");
        $output2 = shell_exec("terraform apply -auto-approve");

        // Se limpian los archivos generados por Terraform
        shell_exec("rm -rf .terraform terraform.tfstate*");
        shell_exec("rm -rf .terraform.lock.hcl");
        shell_exec("rm -rf main.tf");
        shell_exec("touch main.tf");
        shell_exec("chmod 777 main.tf");

        // Se obtiene la IP del nodo
        if (shell_exec("ping -c 1 192.168.5.251 &> /dev/null")) {
            $nodo_ip = "192.168.5.251";
        } elseif (shell_exec("ping -c 1 192.168.5.252 &> /dev/null")) {
            $nodo_ip = "192.168.5.252";
        } elseif (shell_exec("ping -c 1 192.168.5.252 &> /dev/null")) {
            $nodo_ip = "192.168.5.253";
        }

        $ssh = "sshpass -p 'P@ssw0rd' ssh -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null root@$nodo_ip";

        // Obtener ID del contenedor
        $comanda_id = "$ssh \"pct list | grep $hostname | awk '{print \$1}'\"";
        $contenedor_id = shell_exec($comanda_id);
        $contenedor_id = substr($contenedor_id, 0, 3);

        // Obtener IP del contenedor
        $comanda_ip = "$ssh \"pct exec $contenedor_id -- hostname -I\"";
        $contenedor_ip = trim(shell_exec($comanda_ip));

        // Nueva variable SSH2
        $ssh2 = "sshpass -p 'P@ssw0rd' ssh -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null root@$contenedor_ip";

        // Crear carpetas dentro del contenedor
        $comanda_crear = "$ssh2 \"mkdir -p /root/tmp_scripts\"";
        $output = shell_exec($comanda_crear . " 2>&1");

        // Subir el script al contenedor
        $comanda_subir = "$ssh \"pct push $contenedor_id /root/tmp_scripts/ps_cleanup.sh /root/ps_cleanup.sh\"";
        $output = shell_exec($comanda_subir . " 2>&1");

        // Dar permisos al script
        $comanda_permisos = "$ssh2 \"chmod +x /root/ps_cleanup.sh\"";
        $output = shell_exec($comanda_permisos . " 2>&1");

        // Ejecutar el script
        $comanda_cleanup = "$ssh2 \"/root/ps_cleanup.sh\"";
        $output = shell_exec($comanda_cleanup . " 2>&1");

        if (strpos($output2, 'Apply complete! Resources: 1 added, 0 changed, 0 destroyed.') !== false) {
            // Add database operations here
            global $conn;
            $userId = $_SESSION['user_id'];
            $serviceId = $config['service_id'];
            $planId = $config['plan_id'];
            
            // Get service and plan details
            $serviceQuery = "SELECT * FROM services WHERE id = $serviceId AND is_active = 1";
            $serviceResult = $conn->query($serviceQuery);
            $service = $serviceResult->fetch_assoc();
            
            $planQuery = "SELECT * FROM server_plans WHERE id = $planId AND is_active = 1";
            $planResult = $conn->query($planQuery);
            $plan = $planResult->fetch_assoc();
            
            // Calculate total price
            $totalPrice = $service['base_price'] * $plan['price_multiplier'];
            
            // Insert server record into database
            $insertServerQuery = "INSERT INTO user_servers (
                user_id, 
                service_id, 
                plan_id, 
                server_name, 
                hostname, 
                status, 
                purchase_date, 
                expiry_date, 
                auto_renew, 
                billing_cycle, 
                notes, 
                created_at
            ) VALUES (
                $userId,
                $serviceId,
                $planId,
                '$hostname',
                '$contenedor_ip',
                'active',
                NOW(),
                DATE_ADD(NOW(), INTERVAL 1 MONTH),
                1,
                'monthly',
                'Created via Terraform',
                NOW()
            )";
            mysqli_query($conn, $insertServerQuery);
        } else {
            echo '<div class="container error-container"><p>Error creating server: ' . htmlspecialchars($output2) . '</p></div>';
        }
    }
} else {
    echo '<div class="container error-container"><p>Invalid server form type.</p></div>';

}
// Clear server configuration from session
unset($_SESSION['server_config']);
?>
<script>               
    if (window.location.href.indexOf("dashboard.php") === -1) {
        window.location.href = "dashboard.php";
    }
</script>