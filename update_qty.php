<?php
include 'db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$product_id = (int)$_GET['id'];

// Fetch current product data
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

// Handle Qty Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $change_qty = (int)$_POST['qty'];
    $note = trim($_POST['note']);

    if ($change_qty > 0 && in_array($action, ['add', 'reduce'])) {
        $new_qty = $product['qty'];
        if ($action === 'add') {
            $new_qty += $change_qty;
        } elseif ($action === 'reduce') {
            $new_qty = max(0, $product['qty'] - $change_qty);
        }

        // Update product qty
        $updateStmt = $conn->prepare("UPDATE products SET qty = ? WHERE id = ?");
        $updateStmt->bind_param("ii", $new_qty, $product_id);
        $updateStmt->execute();
        $updateStmt->close();

        // Log inventory action
        $logStmt = $conn->prepare("INSERT INTO inventory_log (product_id, action, qty_changed, note) VALUES (?, ?, ?, ?)");
        $logStmt->bind_param("isis", $product_id, $action, $change_qty, $note);
        $logStmt->execute();
        $logStmt->close();

        header("Location: index.php?qty_updated=1");
        exit();
    } else {
        $error = "Invalid quantity or action.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Update Quantity - POS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <h3 class="text-center mb-4">🔄 Update Quantity for <span class="text-primary"><?= htmlspecialchars($product['product_name']); ?></span></h3>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php endif; ?>

        <form method="POST" class="card p-4 shadow">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Action</label>
                    <select name="action" class="form-select" required>
                        <option value="add">➕ Add Quantity</option>
                        <option value="reduce">➖ Reduce Quantity</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="qty" class="form-control" min="1" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Note (optional)</label>
                <input type="text" name="note" class="form-control" placeholder="Reason or details (optional)">
            </div>

            <div class="d-flex justify-content-between">
                <a href="index.php" class="btn btn-secondary">🔙 Back</a>
                <button type="submit" class="btn btn-success">✅ Update Quantity</button>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>