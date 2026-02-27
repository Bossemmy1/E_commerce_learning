<?php
require 'includes/db.php';

echo "Database file: " . realpath($db_file) . "<br>";
echo "File size: " . filesize($db_file) . " bytes<br><br>";

echo "<h2>Users Table:</h2>";
$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll();

if (empty($users)) {
    echo "No users found in database.<br>";
} else {
    echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Email</th><th>Password Hash</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . $user['id'] . "</td>";
        echo "<td>" . $user['name'] . "</td>";
        echo "<td>" . $user['email'] . "</td>";
        echo "<td>" . $user['password'] . "</td>";
        echo "</tr>";

        // Test password verify
        $test_pass = 'admin123';
        $verify = password_verify($test_pass, $user['password']);
        echo "<tr><td colspan='4'>Password 'admin123' match: " . ($verify ? '<b style="color:green">YES</b>' : '<b style="color:red">NO</b>') . "</td></tr>";
    }
    echo "</table>";
}

// Check other tables counts
echo "<h2>Counts:</h2>";
echo "Categories: " . $pdo->query("SELECT count(*) FROM categories")->fetchColumn() . "<br>";
echo "Products: " . $pdo->query("SELECT count(*) FROM products")->fetchColumn() . "<br>";
?>