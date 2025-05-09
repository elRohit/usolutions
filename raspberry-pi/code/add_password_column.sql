-- Check if password column exists, if not add it
SELECT COUNT(*) AS column_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'ddb250465' 
AND TABLE_NAME = 'usuarios' 
AND COLUMN_NAME = 'password';

-- If the count is 0, add the password column
ALTER TABLE usuarios ADD COLUMN password VARCHAR(255) NOT NULL DEFAULT '';

-- Update existing users with a default hashed password (change this in production)
-- Default password: "password123"
UPDATE usuarios SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE password = '';

