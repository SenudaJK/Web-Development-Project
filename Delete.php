<?php
include "config.php";

if (isset($_GET['ProductID']) && is_numeric($_GET['ProductID'])) {
    $ProductId = $_GET['ProductID'];

    $stmt = $conn->prepare("DELETE FROM products WHERE ProductID = ?");
    
    if ($stmt) {
        $stmt->bind_param("i", $ProductId);
        
        if ($stmt->execute()) {
            header("Location: Getproduct.php?msg=Record deleted successfully");
            exit();
        } else {
            echo "Failed: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Failed to prepare the SQL statement: " . $conn->error;
    }
} else {
    echo "Invalid Product Id.";
}

$conn->close();
?>
