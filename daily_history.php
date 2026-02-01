<?php
include 'db.php';

// Query to get daily summary
$query = "
    SELECT 
        s.sale_date,
        COUNT(s.id) AS total_products_sold,
        SUM(s.qty_sold) AS total_qty_sold,
        SUM(s.selling_price * s.qty_sold) AS total_revenue,
        SUM(s.original_price * s.qty_sold) AS total_cost,
        SUM((s.selling_price - s.original_price) * s.qty_sold) AS total_profit
    FROM sales s
    GROUP BY s.sale_date
    ORDER BY s.sale_date DESC
";

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Daily Sales History</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>


<body class="container mt-4 mb-5">

    <button onclick="history.back()" class="btn btn-outline-secondary mb-3">🔙 Back</button>
    <a class="btn btn-outline-secondary mb-3" href="index.php">Go to Home</a>
    <h2 class="text-center mb-4">📅 Daily Sales History</h2>

    <?php if ($result->num_rows > 0) { ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Number of Products Sold</th>
                        <th>Total Quantity Sold</th>
                        <th>Total Revenue</th>
                        <th>Total Cost</th>
                        <th>Total Profit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td>
                                <a href="daily_detail.php?date=<?= $row['sale_date'] ?>">
                                    <?= date('F j, Y', strtotime($row['sale_date'])) ?>
                                </a>
                            </td>
                            <td><?= $row['total_products_sold'] ?></td>
                            <td><?= $row['total_qty_sold'] ?></td>
                            <td class="text-primary">$<?= number_format($row['total_revenue'], 2) ?></td>
                            <td class="text-danger">$<?= number_format($row['total_cost'], 2) ?></td>
                            <td class="fw-bold text-success">$<?= number_format($row['total_profit'], 2) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <div class="alert alert-warning text-center">No sales records found.</div>
    <?php } ?>

</body>

</html>