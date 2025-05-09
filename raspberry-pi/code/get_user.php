<?php
// Start session
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Administrador') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Access denied']);
    exit;
}

// Database connection
require_once 'includes/db_connection.php';

// Get user ID from request
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Invalid user ID']);
    exit;
}

// Get user data
$stmt = $conn->prepare("SELECT id, nombre, apellido, email, rol_id, departamento FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    echo json_encode($user);
} else {
    header('HTTP/1.1 404 Not Found');
    echo json_encode(['error' => 'User not found']);
}
?>

