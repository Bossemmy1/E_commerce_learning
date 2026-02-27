<?php
session_start();
require 'includes/db.php';

if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // For simplicity: create an order with user_id NULL if guest
    $user_id = $_SESSION['user_id'] ?? null;

    // compute total based on DB prices to avoid tampering
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $rows = $stmt->fetchAll();
    $total = 0;
    $prices = [];
    foreach ($rows as $r) {
        $qty = $_SESSION['cart'][$r['id']] ?? 0;
        $total += $qty * $r['price'];
        $prices[$r['id']] = $r['price'];
    }

    $pdo->beginTransaction();
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)");
    $stmt->execute([$user_id, $total]);
    $order_id = $pdo->lastInsertId();
    $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($_SESSION['cart'] as $pid => $qty) {
        $price = $prices[$pid] ?? 0;
        $stmtItem->execute([$order_id, $pid, $qty, $price]);
    }
    $pdo->commit();

    // clear cart
    $_SESSION['cart'] = [];
    $_SESSION['cart_count'] = 0;

    // mock payment success
    header('Location: thankyou.php?order=' . $order_id);
    exit;
}
?>
<?php
$pageTitle = 'Checkout';
include 'includes/head.php';
?>
<?php include 'includes/header.php'; ?>

<main>
    <div
        style="max-width: 600px; margin: 0 auto; background: var(--surface-color); padding: 2rem; border-radius: var(--radius); border: 1px solid var(--border-color);">
        <h1>Checkout</h1>

        <div class="alert" style="background-color: #eff6ff; color: #1e40af; border: 1px solid #dbeafe;">
            <strong>Note:</strong> This is a mock checkout process. No actual payment processing will occur.
        </div>

        <p>Please review your order details before confirming.</p>

        <form method="post" style="box-shadow: none; padding: 0; border: none; max-width: none;">
            <button type="submit" class="btn btn-primary"
                style="width: 100%; font-size: 1.125rem; padding: 1rem;">Confirm Order & Pay</button>
        </form>
    </div>
</main>
<?php include 'includes/footer.php'; ?>