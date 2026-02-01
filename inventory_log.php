<?php
include 'db.php';

// Fetch logs
$sql = "SELECT log.*, p.product_name 
        FROM inventory_log log 
        JOIN products p ON log.product_id = p.id 
        ORDER BY log.log_time DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Inventory Logs - POS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .log-add {
            color: green;
            font-weight: bold;
        }

        .log-reduce {
            color: red;
            font-weight: bold;
        }

        .table-responsive {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <?php include "nav.php" ?>
    <div class="container mt-5">
        <h3 class="text-center mb-4">📋 Inventory Logs</h3>

        <div class="d-flex justify-content-between mb-3">
            <a href="index.php" class="btn btn-secondary">🔙 Back to Products</a>
            <input type="text" id="searchInput" class="form-control w-50" placeholder="🔍 Search Product or Note...">
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered text-center" id="logTable">
                <thead class="table-dark">
                    <tr>
                        <th>Time</th>
                        <th>Product</th>
                        <th>Action</th>
                        <th>Qty Changed</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= date("Y-m-d H:i", strtotime($row['log_time'])); ?></td>
                                <td><?= htmlspecialchars($row['product_name']); ?></td>
                                <td class="<?= $row['action'] === 'add' ? 'log-add' : 'log-reduce'; ?>">
                                    <?= ucfirst($row['action']); ?>
                                </td>
                                <td><?= $row['qty_changed']; ?></td>
                                <td><?= htmlspecialchars($row['note']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No logs found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Simple JS Filter -->
    <script>
        const searchInput = document.getElementById("searchInput");
        const rows = document.querySelectorAll("#logTable tbody tr");

        searchInput.addEventListener("input", function() {
            const query = this.value.toLowerCase();
            rows.forEach(row => {
                const rowText = row.innerText.toLowerCase();
                row.style.display = rowText.includes(query) ? "" : "none";
            });
        });
    </script>

</body>

</html>