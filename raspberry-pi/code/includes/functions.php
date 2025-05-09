<?php
// Funcions comunes per a l'aplicació

// Funció per comprovar si l'usuari té un permís específic
function hasPermission($permissionName) {
    global $conn;
    
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['rol_id'])) {
        return false;
    }
    
    $rolId = $_SESSION['rol_id'];
    
    // Consulta per comprovar si el rol té el permís especificat
    $stmt = $conn->prepare("
        SELECT COUNT(*) as has_permission 
        FROM rol_permisos rp 
        JOIN permisos p ON rp.permiso_id = p.id 
        WHERE rp.rol_id = ? AND p.nombre = ?
    ");
    
    $stmt->bind_param("is", $rolId, $permissionName);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['has_permission'] > 0;
    }
    
    return false;
}

// Funció per comprovar si l'usuari té una targeta RFID
function hasRfidCard($userId) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT COUNT(*) as has_card FROM tarjetas WHERE usuario_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['has_card'] > 0;
    }
    
    return false;
}

// Funció per obtenir el nom del rol de l'usuari
function getUserRoleName($rolId) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT nombre FROM roles WHERE id = ?");
    $stmt->bind_param("i", $rolId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['nombre'];
    }
    
    return 'Desconegut';
}

// Funció per formatar la data per a la visualització
function formatDateForDisplay($dateString) {
    if (!$dateString) return "";
    
    // Si la data ja està en format DD/MM/YYYY, només retorna-la
    if (strpos($dateString, '/') !== false) {
        return $dateString;
    }
    
    // Altrament, converteix de YYYY-MM-DD a DD/MM/YYYY
    try {
        $date = new DateTime($dateString);
        if ($date->format('Y') < 1000) return $dateString; // Data no vàlida
        
        return $date->format('d/m/Y');
    } catch (Exception $e) {
        return $dateString; // Si hi ha algun error, retorna tal com està
    }
}

// Funció per executar una comanda a la Raspberry Pi via SSH
function executeRaspiCommand($command) {
    // Escapa la comanda correctament
    $sshCommand = "ssh raspberry " . escapeshellarg($command) . " 2>&1";
    
    // Executa la comanda
    $output = shell_exec($sshCommand);
    
    // Comprova si hem obtingut alguna sortida
    if ($output === null) {
        return array(
            'success' => false,
            'message' => 'No s\'ha pogut executar la comanda a la Raspberry Pi.',
            'debug' => $sshCommand
        );
    }
    
    // Retalla la sortida per eliminar espais en blanc addicionals
    $output = trim($output);
    
    // Si la sortida està buida, considera-ho un error
    if (empty($output)) {
        return array(
            'success' => false,
            'message' => 'La comanda no ha produït cap sortida.',
            'debug' => $sshCommand
        );
    }
    
    // Retorna èxit amb la sortida
    return array(
        'success' => true,
        'output' => $output
    );
}

// Funció per comptar registres amb una clàusula WHERE opcional
function contarRegistros($conn, $where = "") {
    $sql = "SELECT COUNT(*) as total FROM sesiones_fichaje $where";
    $result = $conn->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        return $row['total'];
    }
    return 0;
}

// Funció per comptar els registres d'avui amb un filtre d'usuari opcional
function contarRegistrosHoy($conn, $userFilter = "") {
    $hoy = date('Y-m-d');
    return contarRegistros($conn, "WHERE DATE(fecha_entrada) = '$hoy' $userFilter");
}

// Funció per obtenir el total d'hores treballades
function getTotalHours($conn, $startDate, $endDate, $userFilter = "") {
    $query = "SELECT 
                SUM(TIMESTAMPDIFF(HOUR, fecha_entrada, fecha_salida)) as total_hours
              FROM 
                sesiones_fichaje
              WHERE 
                fecha_entrada BETWEEN ? AND ?
                AND fecha_salida IS NOT NULL
                $userFilter";
                
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return (int)$row['total_hours'];
    }
    
    return 0;
}

// Funció per obtenir el percentatge de puntualitat
function getPunctualityRate($conn, $startDate, $endDate, $userFilter = "") {
    $query = "SELECT 
                COUNT(CASE WHEN TIME(fecha_entrada) <= '09:00:00' THEN 1 END) as on_time,
                COUNT(*) as total_entries
              FROM 
                sesiones_fichaje
              WHERE 
                fecha_entrada BETWEEN ? AND ?
                $userFilter";
                
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $onTime = (int)$row['on_time'];
        $totalEntries = (int)$row['total_entries'];
        return $totalEntries > 0 ? round(($onTime / $totalEntries) * 100, 2) : 0;
    }
    
    return 0;
}

// Funció per obtenir els dies treballats
function getDaysWorked($conn, $startDate, $endDate, $userFilter = "") {
    $query = "SELECT 
                COUNT(DISTINCT DATE(fecha_entrada)) as days_worked
              FROM 
                sesiones_fichaje
              WHERE 
                fecha_entrada BETWEEN ? AND ?
                $userFilter";
                
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return (int)$row['days_worked'];
    }
    
    return 0;
}
?>
