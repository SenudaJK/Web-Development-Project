<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">Inventory Report</h2>
        <p class="text-center">Download the latest inventory report in CSV format.</p>

        <!-- Button to Generate and Download CSV Report -->
        <div class="text-center mt-4">
            <form action="export_csv.php" method="post">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-file-csv"></i> Download CSV Report
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
