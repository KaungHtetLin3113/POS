<?php
include 'db.php';

// Get today's sales
$date = date('Y-m-d');
$query = "
    SELECT p.product_name, s.qty_sold, 
           (s.selling_price * s.qty_sold) AS total_revenue,
           (s.original_price * s.qty_sold) AS total_cost,
           ((s.selling_price - s.original_price) * s.qty_sold) AS profit
    FROM sales s
    JOIN products p ON s.product_id = p.id
    WHERE s.sale_date = '$date'
";

$result = $conn->query($query);

// Totals
$totalRevenue = $totalCost = $totalProfit = 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Daily Sales Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- ✅ Responsive meta -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="container mt-4 mb-5">

    <button onclick="history.back()" class="btn btn-outline-secondary mb-3">🔙 Back</button>

    <h2 class="text-center mb-4"><a href="daily_history.php">📊</a> Daily Sales Report <br><small class="text-muted">(<?= $date ?>)</small></h2>

    <?php if ($result->num_rows > 0) { ?>
        <!-- ✅ Make table responsive -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Product</th>
                        <th>Qty Sold</th>
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
                            <td>$<?= number_format($row['total_revenue'], 2) ?></td>
                            <td>$<?= number_format($row['total_cost'], 2) ?></td>
                            <td class="fw-bold text-success">$<?= number_format($row['profit'], 2) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- ✅ Totals styled & responsive -->
        <div class="alert alert-info text-center mt-4">
            <div><strong>Total Revenue:</strong> $<?= number_format($totalRevenue, 2) ?></div>
            <div><strong>Total Cost:</strong> $<?= number_format($totalCost, 2) ?></div>
            <div><strong>Total Profit:</strong> $<?= number_format($totalProfit, 2) ?></div>
        </div>
    <?php } else { ?>
        <div class="alert alert-warning text-center">No sales recorded today.</div>
    <?php } ?>

</body>

</html>