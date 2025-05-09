<?php
// Database connection
require_once 'includes/db_connection.php';

// Check if password column exists
$checkColumn = $conn->query("
    SELECT COUNT(*) AS column_exists 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = 'ddb250465' 
    AND TABLE_NAME = 'usuarios' 
    AND COLUMN_NAME = 'password'
");

$columnExists = 0;
if ($row = $checkColumn->fetch_assoc()) {
    $columnExists = $row['column_exists'];
}

// If password column doesn't exist, add it
if ($columnExists == 0) {
    $addColumn = $conn->query("ALTER TABLE usuarios ADD COLUMN password VARCHAR(255) NOT NULL DEFAULT ''");
    
    if ($addColumn) {
        echo "Password column added successfully.<br>";
        
        // Update existing users with a default hashed password
        // Default password: "password123"
        $defaultPassword = password_hash("password123", PASSWORD_DEFAULT);
        $updateUsers = $conn->query("UPDATE usuarios SET password = '$defaultPassword' WHERE password = ''");
        
        if ($updateUsers) {
            echo "Default passwords set for existing users.<br>";
            echo "Default password is: password123<br>";
            echo "Please change this password after logging in.<br>";
        } else {
            echo "Error setting default passwords: " . $conn->error . "<br>";
        }
    } else {
        echo "Error adding password column: " . $conn->error . "<br>";
    }
} else {
    echo "Password column already exists.<br>";
}

// Check if other required columns exist and add them if needed
$requiredColumns = [
    'telefono' => 'VARCHAR(20)',
    'direccion' => 'VARCHAR(255)',
    'fecha_alta' => 'DATETIME DEFAULT CURRENT_TIMESTAMP'
];

foreach ($requiredColumns as $column => $definition) {
    $checkColumn = $conn->query("
        SELECT COUNT(*) AS column_exists 
        FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = 'ddb250465' 
        AND TABLE_NAME = 'usuarios' 
        AND COLUMN_NAME = '$column'
    ");
    
    $columnExists = 0;
    if ($row = $checkColumn->fetch_assoc()) {
        $columnExists = $row['column_exists'];
    }
    
    if ($columnExists == 0) {
        $addColumn = $conn->query("ALTER TABLE usuarios ADD COLUMN $column $definition");
        
        if ($addColumn) {
            echo "$column column added successfully.<br>";
        } else {
            echo "Error adding $column column: " . $conn->error . "<br>";
        }
    } else {
        echo "$column column already exists.<br>";
    }
}

echo "<br>Setup complete. <a href='login.php'>Go to login page</a>";
?>

