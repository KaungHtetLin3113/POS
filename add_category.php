<?php
include 'db.php';

// Handle delete
if (isset($_GET['delete_id'])) {

    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: add_category.php?deleted=1");
    exit();
}
// Handle form submission (Add category)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = trim($_POST['category_name']);
    if (!empty($category_name)) {
        $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
        $stmt->bind_param("s", $category_name);
        $stmt->execute();
        $stmt->close();
        header("Location: add_category.php?category_added=1");
        exit();
    } else {
        $error = "Category name is required.";
    }
}

// Fetch categories
$result = $conn->query("SELECT * FROM categories ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Category - POS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include "nav.php" ?>
    <div class="container mt-5">
        <h3 class="text-center mb-4">📂 Add New Category</h3>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['category_added'])): ?>
            <div class="alert alert-success">✅ Category added successfully!</div>
        <?php endif; ?>
        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-warning">🗑️ Category deleted successfully!</div>
        <?php endif; ?>

        <!-- Add Category Form -->
        <form method="POST" class="card p-4 shadow mb-4">
            <div class="mb-3">
                <label class="form-label">Category Name</label>
                <input type="text" name="category_name" class="form-control" required>
            </div>
            <div class="d-flex justify-content-between">
                <a href="add_product.php" class="btn btn-secondary">🔙 Back to Add Product</a>
                <button type="submit" class="btn btn-primary">✅ Add Category</button>
            </div>
        </form>

        <!-- Category List -->
        <div class="card shadow">
            <div class="card-header bg-dark text-white">📋 Category List</div>
            <div class="card-body">
                <table class="table table-bordered table-hover text-center">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Category Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id']; ?></td>
                                <td><?= htmlspecialchars($row['category_name']); ?></td>
                                <td>
                                    <a href="add_category.php?delete_id=<?= $row['id']; ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this category?')">
                                        🗑️ Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>