<?php
// Database connection
require_once 'includes/db_connection.php';

echo "<h2>Database Connection Test</h2>";

if ($conn->connect_error) {
    echo "Connection failed: " . $conn->connect_error;
} else {
    echo "Connection successful!<br><br>";
    
    // Check if the usuarios table exists and has records
    $result = $conn->query("SELECT COUNT(*) as count FROM usuarios");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "Found " . $row['count'] . " users in the database.<br><br>";
        
        // Check if password column exists
        $columnResult = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'password'");
        if ($columnResult->num_rows > 0) {
            echo "Password column exists in the usuarios table.<br>";
            
            // Check if passwords are set
            $passwordResult = $conn->query("SELECT COUNT(*) as count FROM usuarios WHERE password != ''");
            $passwordRow = $passwordResult->fetch_assoc();
            echo $passwordRow['count'] . " users have passwords set.<br><br>";
            
            // List a few users for testing
            echo "<h3>Sample Users for Testing:</h3>";
            $usersResult = $conn->query("SELECT id, nombre, apellido, email FROM usuarios LIMIT 5");
            if ($usersResult->num_rows > 0) {
                echo "<table border='1' cellpadding='5'>";
                echo "<tr><th>ID</th><th>Nombre</th><th>Apellido</th><th>Email</th></tr>";
                while ($user = $usersResult->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $user['id'] . "</td>";
                    echo "<td>" . $user['nombre'] . "</td>";
                    echo "<td>" . $user['apellido'] . "</td>";
                    echo "<td>" . $user['email'] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        } else {
            echo "Password column does NOT exist in the usuarios table!<br>";
            echo "Please run the setup_password_column.php script first.";
        }
    } else {
        echo "Error checking usuarios table: " . $conn->error;
    }
}
?>

