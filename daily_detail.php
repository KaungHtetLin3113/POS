<?php
include 'db.php';

if (!isset($_GET['date'])) {
    die("Date not specified.");
}

$date = $_GET['date'];

// Query to get products sold on that date
$query = "
    SELECT p.product_name, s.qty_sold, s.selling_price, s.original_price,
           (s.selling_price * s.qty_sold) AS total_revenue,
           (s.original_price * s.qty_sold) AS total_cost,
           ((s.selling_price - s.original_price) * s.qty_sold) AS profit
    FROM sales s
    JOIN products p ON s.product_id = p.id
    WHERE s.sale_date = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

// Totals
$totalRevenue = $totalCost = $totalProfit = 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sales Detail for <?= date('F j, Y', strtotime($date)) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="container mt-4 mb-5">
    <?php include "nav.php" ?>
    <br><br>
    <a href="daily_history.php" class="btn btn-outline-secondary mb-3">🔙 Back to History</a>
    <h2 class="text-center mb-4">🛒 Sales Detail for <?= date('F j, Y', strtotime($date)) ?></h2>

    <?php if ($result->num_rows > 0) { ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Product</th>
                        <th>Quantity Sold</th>
                        <th>Selling Price</th>
                        <th>Total Revenue</th>
                        <th>Total Cost</th>
                        <th>Profit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) {
                        $totalRevenue += $row['total_revenue'];
                        $totalCost += $row['total_cost'];
                        $totalProfit += $row['profit'];
                    ?>
                        <tr>
                            <td><?= $row['product_name'] ?></td>
                            <td><?= $row['qty_sold'] ?></td>
                            <td>$<?= number_format($row['selling_price'], 2) ?></td>
                            <td class="text-primary">$<?= number_format($row['total_revenue'], 2) ?></td>
                            <td class="text-danger">$<?= number_format($row['total_cost'], 2) ?></td>
                            <td class="fw-bold text-success">$<?= number_format($row['profit'], 2) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="alert alert-info text-center mt-4">
            <strong>Total Revenue:</strong> $<?= number_format($totalRevenue, 2) ?> |
            <strong>Total Cost:</strong> $<?= number_format($totalCost, 2) ?> |
            <strong>Total Profit:</strong> $<?= number_format($totalProfit, 2) ?>
        </div>
    <?php } else { ?>
        <div class="alert alert-warning text-center">No products sold on this date.</div>
    <?php } ?>

</body>

</html>