<?php
//use this to debug
//die("Error executing query: " . mysqli_error($mysqli));
include 'config.php';
session_start();

if (isset($_GET['deleteID'])) {
    //get the DispatchOrderID that want to be deleted
    $deleteID = $_GET['deleteID'];

    $sqlDelete = "SELECT ProductID, Quantity
                  FROM DispatchOrders
                  WHERE DispatchOrderID = ?";
    $stmt = $mysqli->prepare($sqlDelete);
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
    $stmt = $mysqli->prepare($sqlInsertQuantity);
    $stmt->bind_param("ii", $deleteQuantity, $deleteProductID);
    $stmt->execute();
    //$resultInsertQuantity = mysqli_query($mysqli, $sqlInsertQuantity);

    //checking errors
    if ($stmt->affected_rows == 0) {
        $_SESSION['status'] = 'error';
        $_SESSION['operation'] = 'delete';
        //echo "Something went wrong with the inventory. Can not perform operation now.";
        exit;
        //used for debugging purposes
        //die("Error executing query: " . mysqli_error($mysqli));
    }

    //delete record from the DispatchOrders table
    $sql = "DELETE FROM `DispatchOrders` WHERE DispatchOrderID = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $deleteID);
    $stmt->execute();
    //$result = mysqli_query($mysqli, $sql);

    if ($stmt->affected_rows > 0) {
        //to store alert messages
        $_SESSION['status'] = 'success';
        $_SESSION['operation'] = 'delete';
    } else {
        $_SESSION['status'] = 'error';
        $_SESSION['operation'] = 'delete';
        //used for debugging purposes
        //$error = mysqli_error($mysqli);
        //die("Error deleting record: " . $error);
    }
    $stmt->close();
    $mysqli->close();
    //mysqli_close($mysqli);
}
header('location: dispatchedOrders.php');
exit;
