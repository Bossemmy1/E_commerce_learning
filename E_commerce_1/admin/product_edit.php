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

$id = $_GET['id'] ?? null;
$name = $description = $price = $image = '';
$category_id = null;

// Fetch categories for the dropdown
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $p = $stmt->fetch();
    if ($p) {
        $name = $p['name'];
        $description = $p['description'];
        $price = $p['price'];
        $category_id = $p['category_id'];
        $image = $p['image'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $category_id = $_POST['category_id'] ?: null;

    // Handle Image Upload
    $image_name = $image; // Keep old image by default
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../assets/uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_name = uniqid() . '.' . $file_ext;
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // Success
        } else {
            // Fallback or error handling can be added here
        }
    }

    if ($id) {
        $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, category_id=?, image=? WHERE id=?");
        $stmt->execute([$name, $description, $price, $category_id, $image_name, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category_id, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $category_id, $image_name]);
        $id = $pdo->lastInsertId();
    }
    header('Location: index.php');
    exit;
}

$pageTitle = ($id ? 'Edit' : 'Add') . ' Product';
$basePath = '../';
include '../includes/head.php';
?>
<?php include '../includes/header.php'; ?>

<main>
    <div
        style="max-width: 600px; margin: 2rem auto; background: var(--surface-color); padding: 2rem; border-radius: var(--radius); border: 1px solid var(--border-color);">
        <h1 style="margin-bottom: 2rem;"><?= $id ? 'Edit' : 'Add' ?> Product</h1>

        <form method="post" enctype="multipart/form-data"
            style="box-shadow: none; padding: 0; border: none; max-width: none;">
            <label>Product Name
                <input name="name" value="<?= htmlspecialchars($name) ?>" required>
            </label>

            <label>Description
                <textarea name="description"
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius); margin-bottom: 1rem; font-family: inherit;"
                    rows="4"><?= htmlspecialchars($description) ?></textarea>
            </label>

            <label>Category
                <select name="category_id"
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius); margin-bottom: 1rem;">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $category_id == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>Price
                <input name="price" value="<?= htmlspecialchars($price) ?>" required step="0.01" type="number">
            </label>

            <label>Product Image
                <?php if ($image): ?>
                    <div style="margin-bottom: 0.5rem;">
                        <img src="../assets/uploads/<?= htmlspecialchars($image) ?>" alt="Current Image"
                            style="max-width: 100px; border-radius: 4px;">
                        <p style="font-size: 0.8rem; color: var(--text-muted);">Current: <?= htmlspecialchars($image) ?></p>
                    </div>
                <?php endif; ?>
                <input type="file" name="image" accept="image/*">
            </label>

            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn btn-primary">Save Product</button>
                <a href="index.php" class="btn" style="text-align: center; padding-top: 0.75rem;">Cancel</a>
            </div>
        </form>
    </div>
</main>
<?php include '../includes/footer.php'; ?>