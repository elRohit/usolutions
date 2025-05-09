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

// Database connection
require_once 'includes/db_connection.php';

// Get parameters
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$userFilter = isset($_GET['user_filter']) ? $_GET['user_filter'] : '';

// Validate user has permission to view requested data
if (strpos($userFilter, 'usuario_id') !== false && !hasPermission('Ver todos los fichajes')) {
    // If user filter contains another user ID and user doesn't have permission
    $userId = $_SESSION['user_id'];
    $userFilter = "AND usuario_id = $userId";
}

// Get fichajes data
$where = "WHERE DATE(fecha_entrada) = '" . $conn->real_escape_string($fecha) . "' $userFilter";

$sql = "SELECT usuarios.nombre, usuarios.apellido, 
        DATE_FORMAT(sesiones_fichaje.fecha_entrada, '%d/%m/%Y %H:%i:%s') as fecha_entrada, 
        CASE WHEN sesiones_fichaje.fecha_salida IS NOT NULL 
            THEN DATE_FORMAT(sesiones_fichaje.fecha_salida, '%d/%m/%Y %H:%i:%s') 
            ELSE NULL 
        END as fecha_salida, 
        sesiones_fichaje.tiempo_extra 
        FROM sesiones_fichaje 
        JOIN usuarios ON sesiones_fichaje.usuario_id = usuarios.id 
        $where 
        ORDER BY sesiones_fichaje.fecha_entrada DESC";

$result = $conn->query($sql);
$fichajes = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $fichajes[] = [
            'nombre' => htmlspecialchars($row['nombre']),
            'apellido' => htmlspecialchars($row['apellido']),
            'fecha_entrada' => $row['fecha_entrada'],
            'fecha_salida' => $row['fecha_salida'],
            'tiempo_extra' => htmlspecialchars($row['tiempo_extra'] ?: '-')
        ];
    }
}

// Get updated stats
$stats = [
    'total' => contarRegistros($conn, $userFilter ? "WHERE 1=1 $userFilter" : ""),
    'hoy' => contarRegistrosHoy($conn, $userFilter)
];

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'fichajes' => $fichajes,
    'stats' => $stats
]);
?>
