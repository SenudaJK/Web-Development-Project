<?php

//connect to the database
include 'Connect.php';

//get data from html
if (isset($_POST['confirm'])) {

    //declare variables
    $productName = $_POST['product-name']; //stored user entered product name
    $quantity = $_POST['quantity']; //stored user entered quantity

    // Fetch productID using productName
    $sql = "SELECT ProductID FROM products WHERE ProductName = '$productName'";

    $result = mysqli_query($conn, $sql);

    // Check the query is successful executed
    if (!$result) {

        echo "Your request is can not be done. Please try again later.";
        //used for debugging purposes
        //die("Error executing query: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        //fetch an item from result
        $row = mysqli_fetch_assoc($result);
        $productID = $row['ProductID'];

        // Insert dispatch order data into the salesOrder table
        $sql = "INSERT INTO salesorders (ProductID, quantity, orderDate) VALUES ('$productID', '$quantity', NOW())";

        //display whether order is successfully dispatched or not
        if (mysqli_query($conn, $sql)) {
            echo "Order placed successfully!";
?>
<?php } else {
            echo "Something went wrong. Try again later.";
            //used for debugging purposes
            //echo "Error inserting order: " . mysqli_error($conn);
        }
    } else {
        echo "Product not found!";
    }
} ?>