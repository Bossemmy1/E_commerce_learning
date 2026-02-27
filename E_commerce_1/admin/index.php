<?php
session_start();
require '../includes/db.php';
if (empty($_SESSION['user_id'])) {
  header('Location: ../login.php');
  exit;
}
$stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
if (empty($user) || !$user['is_admin']) {
  die('Access denied');
}

$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll();

$pageTitle = 'Admin Dashboard';
$basePath = '../';
include '../includes/head.php';
?>
<?php include '../includes/header.php'; ?>

<main>
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1>Product Management</h1>
    <a href="product_edit.php" class="btn btn-primary">Add New Product</a>
  </div>

  <div
    style="background: var(--surface-color); border-radius: var(--radius); border: 1px solid var(--border-color); overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse;">
      <thead>
        <tr style="background: #f1f5f9; border-bottom: 1px solid var(--border-color); text-align: left;">
          <th style="padding: 1rem; font-weight: 600;">ID</th>
          <th style="padding: 1rem; font-weight: 600;">Name</th>
          <th style="padding: 1rem; font-weight: 600;">Price</th>
          <th style="padding: 1rem; font-weight: 600;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
          <tr style="border-bottom: 1px solid var(--border-color);">
            <td style="padding: 1rem;"><?= $p['id'] ?></td>
            <td style="padding: 1rem; font-weight: 500;"><?= htmlspecialchars($p['name']) ?></td>
            <td style="padding: 1rem;">$<?= number_format((float) filter_var($p['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION), 2) ?></td>
            <td style="padding: 1rem;">
              <a href="product_edit.php?id=<?= $p['id'] ?>" class="btn" style="font-size: 0.8rem;">Edit</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include '../includes/footer.php'; ?>