<?php
include 'Connect.php';

if (isset($_GET['deleteID'])) {
    $deleteID = $_GET['deleteID'];

    $sql = "DELETE FROM `salesorders` WHERE SalesOrderID=$deleteID ";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        //echo "deleted successfully";
        header('location: dispatchedOrders.php');
    } else {
        //used for debugging purposes
        //$error = mysqli_error($conn);
        //die("Error deleting record: " . $error);
        echo "Something went wrong. Try again later.";
        header('location: dispatchedOrders.php');
    }
}
