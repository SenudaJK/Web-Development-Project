<?php
//use this to debug
//die("Error executing query: " . mysqli_error($conn));
include 'Connect.php';
session_start();

if (isset($_GET['deleteID'])) {
    //get the SalesOrderID that want to be deleted
    $deleteID = $_GET['deleteID'];

    $sqlDelete = "SELECT ProductID, Quantity
                  FROM salesorders
                  WHERE SalesOrderID=$deleteID";
    $resultDelete = mysqli_query($conn, $sqlDelete);

    //checking errors. 
    if (!$resultDelete || mysqli_num_rows($resultDelete) == 0) {
        echo "Something went wrong. Can not perform operation now.";
        exit;
    }

    $rowDelete = mysqli_fetch_assoc($resultDelete);
    $deleteProductID = $rowDelete['ProductID'];
    $deleteQuantity = $rowDelete['Quantity'];

    //add deleted quantity to the inventory table
    $sqlInsertQuantity = "UPDATE Inventory
                          SET TotalQuantity = TotalQuantity + $deleteQuantity
                          WHERE ProductID=$deleteProductID";
    $resultInsertQuantity = mysqli_query($conn, $sqlInsertQuantity);

    //checking errors
    if (!$resultInsertQuantity) {
        echo "Something went wrong with the inventory. Can not perform operation now.";
        exit;
        //used for debugging purposes
        //die("Error executing query: " . mysqli_error($conn));
    }

    //delete record from the salesorders table
    $sql = "DELETE FROM `salesorders` WHERE SalesOrderID=$deleteID ";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        //to store alert messages
        $_SESSION['status'] = 'success';
        $_SESSION['operation'] = 'delete';
    } else {
        $_SESSION['status'] = 'error';
        $_SESSION['operation'] = 'delete';
        //used for debugging purposes
        //$error = mysqli_error($conn);
        //die("Error deleting record: " . $error);
    }
    mysqli_close($conn);
}
header('location: dispatchedOrders.php');
exit;
