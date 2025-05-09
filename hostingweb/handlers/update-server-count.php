<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// This handler updates the count for a specific service type
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['service_type'])) {
    $serviceType = sanitize_input($_POST['service_type']);
    
    // Get current count
    $stmt = $conn->prepare("SELECT count FROM count_administrative WHERE service_type = ?");
    $stmt->bind_param("s", $serviceType);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentCount = $row['count'];
        
        // Increment count
        $newCount = $currentCount + 1;
        
        // Update count in database
        $updateStmt = $conn->prepare("UPDATE count_administrative SET count = ? WHERE service_type = ?");
        $updateStmt->bind_param("is", $newCount, $serviceType);
        
        if ($updateStmt->execute()) {
            echo json_encode(['success' => true, 'new_count' => $newCount]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update count']);
        }
        $updateStmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Service type not found']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>
