<?php
// The password you're trying to use
$password = "P@ssw0rd";

// The hash you've stored in the database
$stored_hash = '$2y$10$uE6Nd7EoSz3vpk3uC8rf4.9MC0D7OYCFfJWyymUOhi0xzA06UEX4y';

// Verify if the hash matches the password
if (password_verify($password, $stored_hash)) {
    echo "SUCCESS: The hash matches the password 'P@ssw0rd'";
} else {
    echo "ERROR: The hash does NOT match the password 'P@ssw0rd'";
    
    // Generate the correct hash for this password
    $correct_hash = password_hash($password, PASSWORD_DEFAULT);
    echo "<br><br>The correct hash for 'P@ssw0rd' would be:<br>" . $correct_hash;
    
    // Show what password the current hash might be for (for debugging only)
    $test_passwords = ["password", "123456", "admin", "password123"];
    echo "<br><br>Testing some common passwords against your hash:";
    foreach ($test_passwords as $test) {
        if (password_verify($test, $stored_hash)) {
            echo "<br>- The hash DOES match the password: '$test'";
        }
    }
}
?>

