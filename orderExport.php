<?php
include 'Connect.php';

if (isset($_POST['export'])) {
    // Fetch data from Inventory
    $sql = "SELECT 
                so.SalesOrderID, p.ProductName, s.StoreName, s.Location, so.Quantity, so.OrderDate
            FROM salesOrders so
            JOIN products p ON so.ProductID = p.ProductID
            JOIN stores s ON so.StoreID = s.StoreID
            ORDER BY so.OrderDate DESC";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=dispatched_orders_report.csv');

        $output = fopen('php://output', 'w');

        // Output column headings
        fputcsv($output, array('SalesOrder ID', 'Product Name', 'Store Name', 'Location', 'Quantity', 'Order Date'));

        // Output data rows
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }

        fclose($output);
    } else {
        echo "No records found.";
    }
}

$conn->close();
exit();
