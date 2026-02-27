<?php
session_start();
require 'includes/db.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pwd = $_POST['password'];

    // Basic validation
    if (empty($name) || empty($email) || empty($pwd)) {
        $message = 'All fields are required.';
    } else {
        $hashed = password_hash($pwd, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name,email,password) VALUES (?, ?, ?)");
        try {
            $stmt->execute([$name, $email, $hashed]);
            header('Location: login.php?registered=1');
            exit;
        } catch (Exception $e) {
            // Check for duplicate entry
            if (strpos($e->getMessage(), 'UNIQUE constraint failed') !== false) {
                $message = 'Email already registered.';
            } else {
                $message = 'Registration failed: ' . $e->getMessage();
            }
        }
    }
}

$pageTitle = 'Register';
include 'includes/head.php';
?>
<?php include 'includes/header.php'; ?>

<main>
    <div style="max-width: 400px; margin: 2rem auto;">
        <h1 style="text-align: center;">Create Account</h1>

        <?php if ($message): ?>
            <div class="alert alert-error"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="post">
            <label>Full Name
                <input type="text" name="name" required placeholder="John Doe">
            </label>

            <label>Email Address
                <input type="email" name="email" required placeholder="you@example.com">
            </label>

            <label>Password
                <input type="password" name="password" required placeholder="••••••••">
            </label>

            <button type="submit">Register</button>

            <p style="margin-top: 1rem; text-align: center;">
                Already have an account? <a href="login.php">Login here</a>
            </p>
        </form>
    </div>
</main>

<?php include 'includes/footer.php'; ?>