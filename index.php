<?php
include 'db.php';

$search = $_GET['search'] ?? '';
$category_id = $_GET['category_id'] ?? '';
$place_filter = $_GET['place'] ?? '';

$categoryResult = $conn->query("SELECT id, category_name FROM categories ORDER BY category_name");

$where = [];
$params = [];
$types = "";

if (!empty($search)) {
    $where[] = "p.product_name LIKE ?";
    $params[] = "%" . $search . "%";
    $types .= "s";
}
if (!empty($category_id)) {
    $where[] = "p.category_id = ?";
    $params[] = $category_id;
    $types .= "i";
}
if (!empty($place_filter)) {
    $where[] = "p.place LIKE ?";
    $params[] = "%" . $place_filter . "%";
    $types .= "s";
}

$whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";

$sql = "SELECT p.*, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id
        $whereSql
        ORDER BY p.id DESC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>POS Product List</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

    <style>
        :root {
            --green: #16a34a;
            /* green background */
            --yellow: #fde047;
            /* yellow text */
        }

        body {
            background-color: #f8f9fa;
            /* padding: 20px; */

            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            max-width: 1100px;
            margin: auto;
        }

        h2 {
            font-weight: 700;
            margin-bottom: 25px;
            text-align: center;
            color: #0d6efd;
        }

        .results-count {
            font-weight: 600;
            margin-bottom: 15px;
            color: #444;
        }

        .info-message {
            text-align: center;
            color: #dc3545;
            font-weight: 600;
            padding: 20px;
        }

        table th,
        table td {
            vertical-align: middle !important;
        }

        @media (max-width: 575.98px) {

            .form-control,
            .btn {
                font-size: 14px;
            }

            .action-icons a {
                font-size: 16px;
                margin: 0 5px;
            }
        }
    </style>
</head>

<body>
    <?php include "nav.php" ?>
    <br><br>
    <div class="container">

        <h2><i class="fa-solid fa-boxes-stacked"></i> Product Inventory </h2>

        <div class="d-flex justify-content-between mb-3">
            <a href="add_product.php" class="btn btn-primary">➕ Add Product</a>


            <a href="inventory_log.php" class="btn btn-secondary">📋 View Inventory Logs</a>
        </div>
        <form method="GET" class="row g-3 mb-4 align-items-center">
            <div class="col-md-4 col-sm-6">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="🔍 Search product name..."
                    value="<?= htmlspecialchars($search) ?>" />
            </div>
            <div class="col-md-3 col-sm-6">
                <select name="category_id" class="form-select">
                    <option value="">📂 All Categories</option>
                    <?php while ($cat = $categoryResult->fetch_assoc()): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($category_id == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['category_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3 col-sm-6">
                <input
                    type="text"
                    name="place"
                    class="form-control"
                    placeholder="📍 Place"
                    value="<?= htmlspecialchars($place_filter) ?>" />
            </div>
            <div class="col-md-2 col-sm-6 d-grid">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-magnifying-glass"></i> Search
                </button>
            </div>
        </form>

        <div class="mb-3">
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrows-rotate"></i> Reset Filters
            </a>
        </div>

        <p class="results-count">
            🔎 Found <?= $result->num_rows ?> product<?= $result->num_rows !== 1 ? 's' : '' ?> matching your filter.
        </p>

        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle text-center">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Place</th>
                        <th>Qty</th>
                        <th>Selling Price(mmk)</th>
                        <th>Original Price(mmk)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['product_name']) ?></td>
                                <td><?= htmlspecialchars($row['category_name']) ?></td>
                                <td><?= htmlspecialchars($row['place']) ?></td>
                                <td><?= $row['qty'] ?></td>
                                <td><?= number_format($row['selling_price'], 2) ?></td>
                                <td><?= number_format($row['original_price'], 2) ?></td>
                                <td class="action-icons">

                                    <a href="edit_product.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning mb-1">✏️ Edit</a>
                                    <a href="delete_product.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Are you sure?');">🗑️ Delete</a>
                                    <a href="update_qty.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-info mb-1">🔄 Qty</a>

                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="info-message">😥 No products found for your search.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$conn->close();
?>