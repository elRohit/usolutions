<?php
require_once '../includes/header.php';
$hola = "sshpass -p 'P@ssw0rd' ssh -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null root@192.168.5.26 \" ls -d /var/www/html/prestashop/admin*/ \"";
$output = shell_exec($hola . "2>&1");
$output = trim($output);
if (preg_match('/\/admin[^\s\/]*/', $output, $matches)) {
    $output = $matches[0];
}
echo "<pre>$output</pre>";


$userId = 1; // Replace with the actual user ID
$serviceId = 1; // Replace with the actual service ID
$planId = 1; // Replace with the actual plan ID
$contenedor_ip = '192.168.5.47';
$hostname = 'wordpress-1';

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
$insertServerResult = mysqli_query($conn, $insertServerQuery);
if ($insertServerResult) {
    echo "Server inserted successfully.";
} else {
    echo "Error inserting server: " . mysqli_error($conn);
}

include_once '../includes/footer.php';
?>
