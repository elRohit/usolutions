<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Access denied']);
    exit;
}

// Include common functions
require_once 'includes/functions.php';

// Check if user has permission to view all attendance
if (!hasPermission('View all attendance records')) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'You do not have permission to view this information']);
    exit;
}

// Database connection
require_once 'includes/db_connection.php';

// Get today's date
$today = date('Y-m-d');

// Get today's attendance
$todayQuery = "SELECT u.nombre, u.apellido, sf.fecha_entrada, sf.fecha_salida
              FROM sesiones_fichaje sf
              JOIN usuarios u ON sf.usuario_id = u.id
              WHERE DATE(sf.fecha_entrada) = '$today'
              ORDER BY sf.fecha_entrada DESC";

$result = $conn->query($todayQuery);
$attendance = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $attendance[] = [
            'nombre' => htmlspecialchars($row['nombre']),
            'apellido' => htmlspecialchars($row['apellido']),
            'fecha_entrada' => $row['fecha_entrada'],
            'fecha_salida' => $row['fecha_salida']
        ];
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($attendance);
?>
