<?php
include 'Connect.php';

if (isset($_POST['confirm'])) {
    $productName = $_POST['product-name'];
    $quantity = $_POST['quantity'];

    // Fetch productID from the productName
    $sql = "SELECT ProductID FROM products WHERE ProductName = '$productName'";

    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
    if (!$result) {
        die("Error executing query: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $productID = $row['ProductID'];

        // Insert the order into the salesOrder table
        $sql = "INSERT INTO salesorders (ProductID, quantity, orderDate) VALUES ('$productID', '$quantity', NOW())";

        if (mysqli_query($conn, $sql)) {
            echo "Order placed successfully!";
        } else {
            echo "Error inserting order: " . mysqli_error($conn);
        }
    } else {
        echo "Product not found!";
    }
}
