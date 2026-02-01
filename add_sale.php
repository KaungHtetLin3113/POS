<?php
include 'db.php';

// Insert Sale
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $qty_sold = $_POST['qty_sold'];

    // Fetch product prices
    $query = "SELECT selling_price, original_price, qty FROM products WHERE id = $product_id";
    $result = $conn->query($query);
    $product = $result->fetch_assoc();

    if ($product && $product['qty'] >= $qty_sold) {
        $selling_price = $product['selling_price'];
        $original_price = $product['original_price'];

        // Insert into sales
        $stmt = $conn->prepare("INSERT INTO sales (product_id, qty_sold, selling_price, original_price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iidd", $product_id, $qty_sold, $selling_price, $original_price);
        $stmt->execute();

        // Reduce stock
        $conn->query("UPDATE products SET qty = qty - $qty_sold WHERE id = $product_id");

        $message = "Sale recorded successfully!";
    } else {
        $message = "Not enough stock available!";
    }
}

// Fetch Products
$products = $conn->query("SELECT id, product_name FROM products");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Sale</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- ✅ Responsive meta -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">


        <?php if (!empty($message)) echo "<div class='alert alert-info'>$message</div>"; ?>

        <div class="row justify-content-center">
            <div class="col-12 col-md-6"> <!-- ✅ Responsive form width -->
                <form method="POST" class="card p-4 shadow">
                    <div class="mb-3">
                        <a class="btn btn-outline-secondary mb-3" href="index.php"> 🔙 Back</a>

                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                            <h2 class="mb-2">Add Sale</h2>

                        </div>
                        <label for="product_id" class="form-label">Product</label>
                        <select name="product_id" class="form-select" required>
                            <option value="">-- Select Product --</option>
                            <?php while ($row = $products->fetch_assoc()) { ?>
                                <option value="<?= $row['id'] ?>"><?= $row['product_name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="qty_sold" class="form-label">Quantity Sold</label>
                        <input type="number" name="qty_sold" class="form-control" required min="1">
                    </div>

                    <button type="submit" class="btn btn-success w-100">Record Sale</button> <!-- ✅ Full width on mobile -->
                </form>
            </div>
        </div>
    </div>
</body>

</html>