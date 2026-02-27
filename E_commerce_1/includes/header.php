<?php
$user = null;
if (!empty($_SESSION['user_id'])) {
  $stmt = $pdo->prepare("SELECT id,name,is_admin FROM users WHERE id = ?");
  $stmt->execute([$_SESSION['user_id']]);
  $user = $stmt->fetch();
}
$cart_count = $_SESSION['cart_count'] ?? '';
$bp = $basePath ?? '';
?>
<header>
  <div class="logo">
    <a href="<?= $bp ?>index.php" style="font-weight: 700; font-size: 1.25rem; color: var(--text-color);">MyShop</a>
  </div>
  <nav>
    <a href="<?= $bp ?>index.php">Home</a>
    <?php if ($user): ?>
    <a href="<?= $bp ?>cart.php">Cart (<?= $cart_count ?>)</a>
    
      <a href="<?= $bp ?>profile.php">Hello, <?= htmlspecialchars($user['name']) ?></a>
      <?php if ($user['is_admin']): ?>
        <a href="<?= $bp ?>admin/index.php">Admin</a>
      <?php endif; ?>
      <a href="<?= $bp ?>logout.php" class="btn btn-primary"
        style="color: white; padding: 0.25rem 0.75rem; text-decoration: none;">Logout</a>
    <?php else: ?>
      <a href="<?= $bp ?>login.php">Login</a>
      <a href="<?= $bp ?>register.php" class="btn btn-primary"
        style="color: white; padding: 0.25rem 0.75rem; text-decoration: none;">Register</a>
    <?php endif; ?>
  </nav>
</header>