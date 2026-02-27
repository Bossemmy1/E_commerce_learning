<?php
require 'includes/db.php';

$new_password = 'admin123';
$new_hash = password_hash($new_password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->execute([$new_hash, 'admin@example.com']);

    if ($stmt->rowCount() > 0) {
        echo "Password for 'admin@example.com' has been reset to: $new_password<br>";
        echo "New Hash: $new_hash<br>";
    } else {
        echo "User 'admin@example.com' not found or password was already correct (no changes made).<br>";
    }
} catch (PDOException $e) {
    echo "Error updating password: " . $e->getMessage();
}
?>