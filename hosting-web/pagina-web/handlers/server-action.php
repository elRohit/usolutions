<?php
require_once '../includes/functions.php';

// Require login
requireLogin();

$userId = $_SESSION['user_id'];
$serverId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Verify server belongs to user
$server = getServerDetails($serverId, $userId);

if (!$server) {
    // Server not found or doesn't belong to user
    header("Location: ../pages/dashboard.php#servers");
    exit;
}

// Process action
$success = false;
$message = '';

switch ($action) {
    case 'start':
        // Start server logic here
        // In a real application, this would interact with Proxmox API
        $success = true;
        $message = "Server started successfully.";
        break;
        
    case 'stop':
        // Stop server logic here
        $success = true;
        $message = "Server stopped successfully.";
        break;
        
    case 'restart':
        // Restart server logic here
        $success = true;
        $message = "Server restarted successfully.";
        break;
        
    default:
        $message = "Invalid action.";
        break;
}

// Redirect back to server details with message
$redirectUrl = "../pages/server-details.php?id=" . $serverId;

if ($success) {
    $redirectUrl .= "&success=" . urlencode($message);
} else {
    $redirectUrl .= "&error=" . urlencode($message);
}

header("Location: " . $redirectUrl);
exit;

