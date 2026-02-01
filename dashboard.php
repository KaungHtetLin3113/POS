<?php
include 'db.php';
// ----- Line Chart: Inventory Change History (Last 7 Days) -----
$logQuery = $conn->query("
  SELECT DATE(log_time) AS date,
    SUM(
      CASE 
        WHEN action = 'reduce' THEN -qty_changed
        ELSE qty_changed
      END
    ) AS net_change
  FROM inventory_log
  WHERE log_time >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
  GROUP BY DATE(log_time)
  ORDER BY DATE(log_time)
");

$log_dates = [];
$log_changes = [];

while ($row = $logQuery->fetch_assoc()) {
    $log_dates[] = $row['date'];
    $log_changes[] = (int)$row['net_change'];
}

// ----- Pie Chart: Stock by Category -----
$categoryQuery = $conn->query("
  SELECT c.category_name, SUM(p.qty) as total_qty
  FROM products p
  JOIN categories c ON p.category_id = c.id
  GROUP BY p.category_id
");

$category_names = [];
$category_qtys = [];

while ($row = $categoryQuery->fetch_assoc()) {
    $category_names[] = $row['category_name'];
    $category_qtys[] = (int)$row['total_qty'];
}


// Fetch product names and quantities for chart
$chartData = $conn->query("SELECT product_name, qty FROM products ORDER BY qty DESC LIMIT 10");
$labels = [];
$quantities = [];

while ($row = $chartData->fetch_assoc()) {
    $labels[] = $row['product_name'];
    $quantities[] = $row['qty'];
}

// Fetch totals
$totalProducts = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
$totalCategories = $conn->query("SELECT COUNT(*) FROM categories")->fetch_row()[0];
$lowStock = $conn->query("SELECT COUNT(*) FROM products WHERE qty <= 5 AND qty > 0")->fetch_row()[0];
$outOfStock = $conn->query("SELECT COUNT(*) FROM products WHERE qty = 0")->fetch_row()[0];

// Fetch lists
$lowStockList = $conn->query("SELECT p.id, p.product_name, c.category_name as category, p.place, p.qty FROM products p JOIN categories c ON p.category_id = c.id WHERE p.qty <= 5 AND p.qty > 0");
$outOfStockList = $conn->query("SELECT p.product_name FROM products p WHERE p.qty = 0");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>POS Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<body class="bg-light">

    <?php include "nav.php" ?>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold"><i class="bi bi-speedometer2 me-2"></i>POS Dashboard</h2>
            <a href="index.php" class="btn btn-primary"><i class="bi bi-boxes"></i> View Products</a>
        </div>

        <!-- Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm border-0 text-center">
                    <div class="card-body">
                        <i class="bi bi-box-seam fs-1 text-primary"></i>
                        <h5>Total Products</h5>
                        <p class="display-6 fw-bold"><?= $totalProducts ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm border-0 text-center">
                    <div class="card-body">
                        <i class="bi bi-tags fs-1 text-success"></i>
                        <h5>Total Categories</h5>
                        <p class="display-6 fw-bold"><?= $totalCategories ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm border-0 text-center">
                    <div class="card-body">
                        <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
                        <h5>Low Stock (≤5)</h5>
                        <p class="display-6 fw-bold"><?= $lowStock ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm border-0 text-center">
                    <div class="card-body">
                        <i class="bi bi-x-circle fs-1 text-danger"></i>
                        <h5>Out of Stock</h5>
                        <p class="display-6 fw-bold"><?= $outOfStock ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ⚠️ Low Stock Table -->
        <div class="mb-5">
            <h4 class="mb-3 text-warning"><i class="bi bi-exclamation-triangle me-2"></i>⚠️ Low Stock Products (≤ 5 qty)</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-hover bg-white shadow-sm">
                    <thead class="table-warning">
                        <tr>
                            <th>#</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Place</th>
                            <th>Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($lowStockList->num_rows > 0):
                            $i = 1;
                            while ($row = $lowStockList->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                                    <td><?= htmlspecialchars($row['category']) ?></td>
                                    <td><?= htmlspecialchars($row['place']) ?></td>
                                    <td class="text-danger fw-bold"><?= $row['qty'] ?></td>
                                </tr>
                            <?php endwhile;
                        else: ?>
                            <tr>
                                <td colspan="5" class="text-center">All stocks are fine.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ❌ Out of Stock Table -->
        <div class="mb-5">
            <h4 class="mb-3 text-danger"><i class="bi bi-x-circle me-2"></i>❌ Out of Stock Products</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-hover bg-white shadow-sm">
                    <thead class="table-danger">
                        <tr>
                            <th>#</th>
                            <th>Product Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($outOfStockList->num_rows > 0):
                            $j = 1;
                            while ($row = $outOfStockList->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $j++ ?></td>
                                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                                </tr>
                            <?php endwhile;
                        else: ?>
                            <tr>
                                <td colspan="2" class="text-center">No out of stock products.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="mt-5">
                    <h4 class="mb-3"><i class="bi bi-bar-chart"></i> Stock Level Chart</h4>
                    <canvas id="stockChart" height="100"></canvas>
                </div>

                <script>
                    const ctx = document.getElementById('stockChart').getContext('2d');
                    const stockChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: <?= json_encode($labels); ?>,
                            datasets: [{
                                label: 'Stock Qty',
                                data: <?= json_encode($quantities); ?>,
                                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1,
                                borderRadius: 5
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    stepSize: 1
                                }
                            }
                        }
                    });
                </script>

            </div>
        </div>
        <div class="mt-5">
            <h4><i class="bi bi-graph-up-arrow"></i> Inventory Change (Last 7 Days)</h4>
            <canvas id="lineChart" height="100"></canvas>
        </div>

        <script>
            const ctxLine = document.getElementById('lineChart').getContext('2d');
            const lineChart = new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: <?= json_encode($log_dates); ?>,
                    datasets: [{
                        label: 'Net Stock Change',
                        data: <?= json_encode($log_changes); ?>,
                        fill: true,
                        backgroundColor: 'rgba(75,192,192,0.2)',
                        borderColor: 'rgba(75,192,192,1)',
                        tension: 0.3,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>



    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>