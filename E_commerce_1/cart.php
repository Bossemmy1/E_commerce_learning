<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['cart']))
  $_SESSION['cart'] = [];

$action = $_POST['action'] ?? $_GET['action'] ?? '';
if ($action === 'add' && !empty($_POST['id'])) {
  $id = intval($_POST['id']);
  $qty = max(1, intval($_POST['qty'] ?? 1));
  if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id] += $qty;
  } else {
    $_SESSION['cart'][$id] = $qty;
  }
  $_SESSION['cart_count'] = array_sum($_SESSION['cart']);
  header('Location: cart.php');
  exit;
} elseif ($action === 'remove' && !empty($_GET['id'])) {
  $id = intval($_GET['id']);
  unset($_SESSION['cart'][$id]);
  $_SESSION['cart_count'] = array_sum($_SESSION['cart']);
  header('Location: cart.php');
  exit;
} elseif ($action === 'clear') {
  $_SESSION['cart'] = [];
  $_SESSION['cart_count'] = 0;
  header('Location: cart.php');
  exit;
}

// Display cart
$items = [];
$total = 0.0;
if (!empty($_SESSION['cart'])) {
  $ids = array_keys($_SESSION['cart']);
  $placeholders = implode(',', array_fill(0, count($ids), '?'));
  $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
  $stmt->execute($ids);
  $rows = $stmt->fetchAll();
  foreach ($rows as $r) {
    $qty = $_SESSION['cart'][$r['id']] ?? 0;
    $line = [
      'product' => $r,
      'qty' => $qty,
      'subtotal' => $qty * $r['price']
    ];
    $items[] = $line;
    $total += $line['subtotal'];
  }
}
?>
<!doctype html>
<?php
// Ensure this part is handled by the calling script if not already
// But since this file has mixed PHP/HTML, we should separate logic if possible, 
// or just replace the HTML part.
// The top part of cart.php is pure PHP logic.
// We will replace strictly from line 53 downwards.
?>
<?php
$pageTitle = 'Cart';
include 'includes/head.php';
?>
<?php include 'includes/header.php'; ?>

<main>
  <h1>Your Cart</h1>
  <div class="cart-container"
    style="background: var(--surface-color); padding: 2rem; border-radius: var(--radius); border: 1px solid var(--border-color);">
    <?php if (empty($items)): ?>
      <p>Your cart is currently empty.</p>
      <a href="index.php" class="btn btn-primary" style="margin-top: 1rem;">Continue Shopping</a>
    <?php else: ?>
      <table style="width: 100%; border-collapse: collapse; margin-bottom: 2rem;">
        <thead>
          <tr style="border-bottom: 2px solid var(--border-color); text-align: left;">
            <th style="padding: 1rem;">Product</th>
            <th style="padding: 1rem;">Qty</th>
            <th style="padding: 1rem;">Price</th>
            <th style="padding: 1rem;">Subtotal</th>
            <th style="padding: 1rem;"></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $it): ?>
            <tr style="border-bottom: 1px solid var(--border-color);">
              <td style="padding: 1rem;"><?= htmlspecialchars($it['product']['name']) ?></td>
              <td style="padding: 1rem;"><?= intval($it['qty']) ?></td>
              <td style="padding: 1rem;">$<?= number_format($it['product']['price'], 2) ?></td>
              <td style="padding: 1rem; font-weight: 600;">$<?= number_format($it['subtotal'], 2) ?></td>
              <td style="padding: 1rem;">
                <a href="cart.php?action=remove&id=<?= $it['product']['id'] ?>" class="btn"
                  style="background: #fee2e2; color: #b91c1c;">Remove</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <div
        style="display: flex; justify-content: space-between; align-items: center; border-top: 2px solid var(--border-color); padding-top: 2rem;">
        <div style="font-size: 1.5rem; font-weight: 700;">
          Total: $<?= number_format($total, 2) ?>
        </div>
        <div style="display: flex; gap: 1rem;">
          <a href="cart.php?action=clear" class="btn">Clear Cart</a>
          <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
        </div>
      </div>
    <?php endif; ?>
  </div>
</main>
<?php include 'includes/footer.php'; ?>