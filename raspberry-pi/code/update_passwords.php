<?php
// Database connection
require_once 'includes/db_connection.php';

// The password you want to set for all users
$password = "P@ssw0rd";

// Generate the correct hash
$correct_hash = password_hash($password, PASSWORD_DEFAULT);

echo "Updating all user passwords to use the correct hash for 'P@ssw0rd'...<br>";
echo "Generated hash: $correct_hash<br><br>";

// Update all users with the correct hash
$updateQuery = "UPDATE usuarios SET password = ?";
$stmt = $conn->prepare($updateQuery);
$stmt->bind_param("s", $correct_hash);

if ($stmt->execute()) {
    echo "SUCCESS: All user passwords have been updated.<br>";
    echo "You can now log in with any email and the password 'P@ssw0rd'<br>";
} else {
    echo "ERROR: Failed to update passwords. " . $conn->error . "<br>";
}

echo "<br><a href='login.php'>Go to login page</a>";
?>

