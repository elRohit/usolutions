<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  echo json_encode([
    'success' => false,
    'message' => '⚠️ Session expired. Please log in again.',
    'action' => 'session'
  ]);
  exit;
}

// Include common functions
require_once 'includes/functions.php';

// Database connection
require_once 'includes/db_connection.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');

// Default response
$response = [
  'success' => false,
  'message' => 'No action has been performed.',
  'action' => '',
  'data' => null
];

// Function to calculate time difference in a readable format
function formatTimeDifference($seconds) {
  if ($seconds < 60) {
    return sprintf("%d segundos", $seconds);
  }
  $minutes = floor($seconds / 60);
  $seconds = $seconds % 60;
  return sprintf("%d min y %d seg", $minutes, $seconds);
}

// Handle RFID scanning
if (isset($_POST['scan_rfid'])) {
  // Get the action type (entrada or sortida)
  $action_type = isset($_POST['action_type']) ? $_POST['action_type'] : '';
  
  // Validate action type
  if ($action_type !== 'entrada' && $action_type !== 'sortida') {
    $response['message'] = "❌ Invalid action. It must be 'entrada' or 'sortida'.";
    echo json_encode($response);
    exit;
  }
  
  // Execute the Python script to read the RFID card
  $result = executeRaspiCommand('python3 /home/ira/leer_rfid.py');
  
  if (!$result['success']) {
    $response['message'] = "❌ Error reading the RFID card: " . $result['message'];
    echo json_encode($response);
    exit;
  }
  
  $rfid_code = trim($result['output']);

  // Validate RFID code more thoroughly
  if (empty($rfid_code)) {
    $response['message'] = "❌ No RFID code was received.";
    echo json_encode($response);
    exit;
  }

  if (!is_numeric($rfid_code)) {
    $response['message'] = "❌ Invalid RFID code: " . htmlspecialchars($rfid_code);
    echo json_encode($response);
    exit;
  }
  
  // Look up the user associated with the card
  $stmt = $conn->prepare("
    SELECT usuarios.id, usuarios.nombre, usuarios.apellido
    FROM usuarios
    JOIN tarjetas ON usuarios.id = tarjetas.usuario_id
    WHERE tarjetas.rfid_code = ?
  ");
  $stmt->bind_param("s", $rfid_code);
  $stmt->execute();
  $result = $stmt->get_result();
  
  if ($result->num_rows === 0) {
    $response['message'] = "❌ Unknown card (UID: " . $rfid_code . ").";
    echo json_encode($response);
    exit;
  }
  
  $usuario = $result->fetch_assoc();
  $usuario_id = $usuario['id'];
  $nombre_completo = $usuario['nombre'] . ' ' . $usuario['apellido'];
  
  $ahora = new DateTime();
  
  // Check for recent scans (5 second cooldown)
  $stmt = $conn->prepare("
    SELECT MAX(CASE WHEN fecha_salida IS NOT NULL THEN fecha_salida ELSE fecha_entrada END) as last_scan
    FROM sesiones_fichaje
    WHERE usuario_id = ?
  ");
  $stmt->bind_param("i", $usuario_id);
  $stmt->execute();
  $result = $stmt->get_result();
  
  $row = $result->fetch_assoc();
if ($row && $row['last_scan'] !== null) {    $last_scan = new DateTime($row['last_scan']);
    $diferencia = $ahora->getTimestamp() - $last_scan->getTimestamp();
    
    if ($diferencia < 5) { // 5 second cooldown
      $restante = 5 - $diferencia;
      $response['message'] = "⏳ You must wait " . formatTimeDifference($restante) . " before clocking in again.";
      $response['action'] = 'cooldown';
      $response['data'] = [
        'usuario' => $nombre_completo,
        'restante' => $restante
      ];
      echo json_encode($response);
      exit;
    }
  }
  
  // Process based on action type
  if ($action_type === 'entrada') {
    // Register new check-in
    $stmt = $conn->prepare("
      INSERT INTO sesiones_fichaje (usuario_id, fecha_entrada)
      VALUES (?, ?)
    ");
    $fecha_entrada = $ahora->format('Y-m-d H:i:s');
    $stmt->bind_param("is", $usuario_id, $fecha_entrada);
    
    if ($stmt->execute()) {
      $response['success'] = true;
      $response['message'] = "🟢 Check-in registered for " . $nombre_completo . " at " . $ahora->format('H:i:s');
      $response['action'] = 'checkin';
      $response['data'] = [
        'user' => $nombre_completo,
        'time' => $ahora->format('H:i:s')
      ];
        } else {
      $response['message'] = "❌ Error registering the check-in: " . $conn->error;
    }
  } 
  elseif ($action_type === 'sortida') {
    // Find the latest open session
    $stmt = $conn->prepare("
      SELECT id, fecha_entrada
      FROM sesiones_fichaje
      WHERE usuario_id = ? AND fecha_salida IS NULL
      ORDER BY fecha_entrada DESC
      LIMIT 1
    ");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
      $response['message'] = "❌ There is no open session to register the check-out.";
      echo json_encode($response);
      exit;
    }
    
    $sesion = $result->fetch_assoc();
    $sesion_id = $sesion['id'];
    $fecha_entrada = new DateTime($sesion['fecha_entrada']);
    
    // Register check-out
    $stmt = $conn->prepare("
      UPDATE sesiones_fichaje 
      SET fecha_salida = ? 
      WHERE id = ?
    ");
    $fecha_salida = $ahora->format('Y-m-d H:i:s');
    $stmt->bind_param("si", $fecha_salida, $sesion_id);
    
    if ($stmt->execute()) {
      // Calculate tiempo_extra
      $entrada_hora = (int)$fecha_entrada->format('H');
      $salida_hora = (int)$ahora->format('H');
      
      // Calculate working hours
      $horas_trabajadas = ($ahora->getTimestamp() - $fecha_entrada->getTimestamp()) / 3600;
      
      // Calculate tiempo_extra (simplified version)
      $tiempo_extra = '00:00:00';
      
      // If working more than 8 hours or outside normal hours (9-17)
      if ($horas_trabajadas > 8 || $entrada_hora < 9 || $salida_hora > 17) {
        $horas_extra = max(0, $horas_trabajadas - 8);
        $minutos_extra = round(($horas_extra - floor($horas_extra)) * 60);
        $tiempo_extra = sprintf("%02d:%02d:00", floor($horas_extra), $minutos_extra);
      }
      
      // Update tiempo_extra
      $stmt = $conn->prepare("
        UPDATE sesiones_fichaje 
        SET tiempo_extra = ? 
        WHERE id = ?
      ");
      $stmt->bind_param("si", $tiempo_extra, $sesion_id);
      $stmt->execute();
      
      $response['success'] = true;
      $response['message'] = "🔴 Check-out registered for " . $nombre_completo . " at " . $ahora->format('H:i:s');
      $response['action'] = 'checkout';
      $response['data'] = [
        'usuario' => $nombre_completo,
        'hora' => $ahora->format('H:i:s'),
        'tiempo_extra' => $tiempo_extra
      ];
    } else {
      $response['message'] = "❌ Error registering the check-out: " . $conn->error;
    }
  }
}

// Return JSON response
if (!headers_sent() && ob_get_length() === 0) {
    echo json_encode($response);
}
exit;

// Respuesta vacía de fallback si nada se ha enviado
register_shutdown_function(function() {
  if (!headers_sent()) {
    header('Content-Type: application/json');
  }

  // Si no se ha enviado nada, devuelve JSON vacío
  if (ob_get_length() === 0) {
    echo json_encode([
      'success' => false,
      'message' => '⚠️ No response was received from the server.',
      'action' => 'empty'
    ]);
  }
});


?>
