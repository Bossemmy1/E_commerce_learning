<?php
session_start();
require 'includes/db.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $pwd = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $u = $stmt->fetch();
    if ($u && password_verify($pwd, $u['password'])) {
        $_SESSION['user_id'] = $u['id'];
        header('Location: index.php');
        exit;
    } else {
        $message = 'Invalid login credentials.';
    }
}

$pageTitle = 'Login';
include 'includes/head.php';
?>
<?php include 'includes/header.php'; ?>

<main>
    <?php if (isset($_GET['registered']) && $_GET['registered'] == 1): ?>
    <div style="max-width: 400px; margin: 2rem auto;">
        <h1 style="text-align: center;">Welcome Back</h1>
    <?php else: ?>
    <div style="max-width: 400px; margin: 2rem auto;">
        <h1 style="text-align: center;">Login</h1>
    <?php endif; ?>
        <?php if ($message): ?>
            <div class="alert alert-error"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="post">
            <label>Email Address
                <input type="email" name="email" required placeholder="you@example.com">
            </label>

            <label>Password
                <input type="password" name="password" required placeholder="••••••••">
            </label>

            <button type="submit">Login</button>

            <p style="margin-top: 1rem; text-align: center;">
                Don't have an account? <a href="register.php">Register here</a>
            </p>
        </form>
    </div>
</main>

<?php include 'includes/footer.php'; ?>