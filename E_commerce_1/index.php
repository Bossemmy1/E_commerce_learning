<?php
session_start();
require 'includes/db.php';

// fetch products
$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id");
$products = $stmt->fetchAll();

$pageTitle = 'Home';
include 'includes/head.php';
?>
<?php include 'includes/header.php'; ?>

<main>
  <h1>Our Products</h1>

  <?php if (isset($_GET['added'])): ?>
    <div class="alert alert-success">Product added to cart!</div>
  <?php endif; ?>

  <div class="products">
    <?php foreach ($products as $p): ?>
      <div class="product">
        <?php if (!empty($p['image'])): ?>
          <img src="assets/uploads/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>"
            style="width:100%; height:200px; object-fit: fit; border-radius: var(--radius);">
        <?php else: ?>
          <div
            style="width:100%; height:200px; background-color: #e2e8f0; border-radius: var(--radius); display: flex; align-items: center; justify-content: center; color: #64748b;">
            No Image</div>
        <?php endif; ?>

        <h3><?= htmlspecialchars($p['name']) ?></h3>
        <p><?= htmlspecialchars(substr($p['description'], 0, 100)) ?>...</p>
        <p class="text-sm text-muted">Category: <?= htmlspecialchars($p['category_name'] ?? 'Uncategorized') ?></p>
        <div class="price">
          $<?= number_format((float) filter_var($p['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION), 2) ?>
        </div>

        <form method="post" action="cart.php">
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="id" value="<?= intval($p['id']) ?>">
          <label>Qty: <input type="number" name="qty" value="1" min="1" style="width: 60px;"></label>
          <button type="submit" class="btn btn-primary">Add to Cart</button>
        </form>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<?php include 'includes/footer.php'; ?>