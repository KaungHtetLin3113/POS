<?php
include 'db.php';

// Get product ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$product_id = $_GET['id'];

// Fetch product data
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Product not found.";
    exit();
}

$product = $result->fetch_assoc();
$stmt->close();

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['product_name']);
    $category_id = $_POST['category_id'];
    $place = trim($_POST['place']);
    $qty = (int) $_POST['qty'];
    $selling_price = (float) $_POST['selling_price'];
    $original_price = (float) $_POST['original_price'];

    if ($name && $category_id && $qty >= 0 && $selling_price >= 0 && $original_price >= 0) {
        $stmt = $conn->prepare("UPDATE products SET product_name = ?, category_id = ?, place = ?, qty = ?, selling_price = ?, original_price = ? WHERE id = ?");
        $stmt->bind_param("sisiddi", $name, $category_id, $place, $qty, $selling_price, $original_price, $product_id);
        $stmt->execute();
        $stmt->close();
        header("Location: index.php?updated=1");
        exit();
    } else {
        $error = "Please fill all fields correctly.";
    }
}

// Fetch categories
$categoryResult = $conn->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Product - POS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h3 class="text-center mb-4">✏️ Edit Product <span class="text-primary"><?= htmlspecialchars($product['product_name']); ?></span></h3>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php endif; ?>

        <form method="POST" class="card p-4 shadow">
            <div class="mb-3">
                <label class="form-label">Product Name</label>
                <input type="text" name="product_name" value="<?= htmlspecialchars($product['product_name']); ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label d-flex justify-content-between align-items-center">
                    <span>Category</span>

                </label>
                <select name="category_id" class="form-select" required>
                    <option value="">-- Select Category --</option>
                    <?php while ($cat = $categoryResult->fetch_assoc()): ?>
                        <option value="<?= $cat['id']; ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($cat['category_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Place</label>
                <input type="text" name="place" value="<?= htmlspecialchars($product['place']); ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="qty" value="<?= $product['qty']; ?>" class="form-control" min="0" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Selling Price ($)</label>
                <input type="number" name="selling_price" value="<?= $product['selling_price']; ?>" step="0.01" class="form-control" min="0" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Original Price ($)</label>
                <input type="number" name="original_price" value="<?= $product['original_price']; ?>" step="0.01" class="form-control" min="0" required>
            </div>

            <div class="d-flex justify-content-between">
                <a href="index.php" class="btn btn-secondary">🔙 Back</a>
                <button type="submit" class="btn btn-success">✅ Update Product</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>