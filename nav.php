<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar Example</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-custom {
            background-color: green;
        }

        .navbar-custom .navbar-brand {
            color: yellow;
            font-weight: bold;
        }

        .navbar-custom .btn-custom {
            background-color: green;
            color: yellow;
            border: 2px solid yellow;
            margin-left: 15px;
            /* space between buttons */
        }

        .navbar-custom .btn-custom:hover {
            background-color: yellow;
            color: green;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-custom p-3">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">My POS system</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarButtons" aria-controls="navbarButtons" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarButtons">
                <div class="navbar-nav">
                    <a class="btn btn-custom" href="index.php">Home</a>
                    <a class="btn btn-custom" href="add_sale.php">Add Sale Daily</a>
                    <a class="btn btn-custom" href="daily_sale.php">Daily Sale</a>
                    <a href="dashboard.php" class="btn btn-custom">📊 Dashboard</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>