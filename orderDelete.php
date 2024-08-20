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
                  WHERE SalesOrderID = ?";
    $stmt = $conn->prepare($sqlDelete);
    $stmt->bind_param("i", $deleteID);
    $stmt->execute();
    $resultDelete = $stmt->get_result();

    //checking errors. 
    if (!$resultDelete || mysqli_num_rows($resultDelete) == 0) {
        $_SESSION['status'] = 'error';
        $_SESSION['operation'] = 'delete';
        //echo "Something went wrong. Can not perform operation now.";
        exit;
    }

    $rowDelete = $resultDelete->fetch_assoc();
    $deleteProductID = $rowDelete['ProductID'];
    $deleteQuantity = $rowDelete['Quantity'];

    //add deleted quantity to the inventory table
    $sqlInsertQuantity = "UPDATE Inventory
                          SET TotalQuantity = TotalQuantity + ?
                          WHERE ProductID = ?";
    $stmt = $conn->prepare($sqlInsertQuantity);
    $stmt->bind_param("ii", $deleteQuantity, $deleteProductID);
    $stmt->execute();
    //$resultInsertQuantity = mysqli_query($conn, $sqlInsertQuantity);

    //checking errors
    if ($stmt->affected_rows == 0) {
        $_SESSION['status'] = 'error';
        $_SESSION['operation'] = 'delete';
        //echo "Something went wrong with the inventory. Can not perform operation now.";
        exit;
        //used for debugging purposes
        //die("Error executing query: " . mysqli_error($conn));
    }

    //delete record from the salesorders table
    $sql = "DELETE FROM `salesorders` WHERE SalesOrderID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $deleteID);
    $stmt->execute();
    //$result = mysqli_query($conn, $sql);

    if ($stmt->affected_rows > 0) {
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
    $stmt->close();
    $conn->close();
    //mysqli_close($conn);
}
header('location: dispatchedOrders.php');
exit;
