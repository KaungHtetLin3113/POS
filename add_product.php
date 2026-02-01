<?php
include 'db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['product_name']);
    $category_id = $_POST['category_id'];
    $place = trim($_POST['place']);
    $qty = (int) $_POST['qty'];
    $selling_price = (float) $_POST['selling_price'];
    $original_price = (float) $_POST['original_price'];

    if ($name && $category_id && $qty >= 0 && $selling_price >= 0 && $original_price >= 0) {
        $stmt = $conn->prepare("INSERT INTO products (product_name, category_id, place, qty, selling_price, original_price) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisidd", $name, $category_id, $place, $qty, $selling_price, $original_price);
        $stmt->execute();
        $stmt->close();
        header("Location: index.php?success=1");
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
    <title>Add Product - POS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include "nav.php" ?>

    <div class="container mt-5">
        <h3 class="text-center mb-4">➕ Add New Product</h3>
        <?php if (isset($_GET['category_added'])): ?>
            <div class="alert alert-success">✅ Category added successfully.</div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php endif; ?>

        <form method="POST" class="card shadow p-4">
            <div class="mb-3">
                <label class="form-label">Product Name</label>
                <input type="text" name="product_name" class="form-control" required>
            </div>


            <div class="mb-3">
                <label class="form-label d-flex justify-content-between align-items-center">
                    <span>Category</span>
                    <a href="add_category.php" class="btn btn-sm btn-outline-primary">➕ Add Category</a>
                </label>
                <select name="category_id" class="form-select" required>
                    <option value="">-- Select Category --</option>
                    <?php
                    // Re-run category query in case redirected back from add_category
                    $categoryResult = $conn->query("SELECT * FROM categories");
                    while ($cat = $categoryResult->fetch_assoc()):
                    ?>
                        <option value="<?= $cat['id']; ?>"><?= htmlspecialchars($cat['category_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>


            <div class="mb-3">
                <label class="form-label">Place</label>
                <input type="text" name="place" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="qty" class="form-control" min="0" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Selling Price ($)</label>
                <input type="number" name="selling_price" step="0.01" class="form-control" min="0" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Original Price ($)</label>
                <input type="number" name="original_price" step="0.01" class="form-control" min="0" required>
            </div>

            <div class="d-flex justify-content-between">


                <button type="submit" class="btn btn-success">✅ Add Product</button>

            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>