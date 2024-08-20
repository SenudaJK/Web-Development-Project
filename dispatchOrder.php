<?php

//connect to the database
include 'Connect.php';
session_start();

//get data from html
if (isset($_POST['confirm'])) {

    //declare variables
    $productName = $_POST['product-name']; //stored user entered product name
    $storeName = $_POST['store-name']; //stored user entered store name
    $quantity = $_POST['quantity']; //stored user entered quantity

    // Fetch productID using productName
    $sql = "SELECT ProductID 
            FROM products 
            WHERE ProductName = '$productName'";
    $result = mysqli_query($conn, $sql);

    // Check the query is successful executed
    if (!$result || mysqli_num_rows($result) == 0) {
        $_SESSION['status'] = 'error';
        $_SESSION['operation'] = 'place';
        header('location: dispatchedOrders.php');
        exit;
        //echo "Your request is can not be done now. Please try again later.";
        //return;
        //used for debugging purposes
        //die("Error executing query: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        //fetch an item from result
        $row = mysqli_fetch_assoc($result);
        $productID = $row['ProductID'];

        $sqlStore = "SELECT StoreID 
                    FROM stores 
                    WHERE StoreName = '$storeName'";
        $resultStore = mysqli_query($conn, $sqlStore);

        if (!$resultStore || mysqli_num_rows($resultStore) == 0) {
            $_SESSION['status'] = 'error';
            $_SESSION['operation'] = 'place';
            header('location: dispatchedOrders.php');
            exit;
            //echo "Your request is can not be done now. Please try again later";
            //return;
            //used for debugging purposes
            //die("Error executing query: " . mysqli_error($conn));
        }

        if (mysqli_num_rows($resultStore)) {
            $rowStore = mysqli_fetch_assoc(($resultStore));
            $storeID = $rowStore['StoreID'];

            // get TotalQuantity related to ProductID
            $sqlQuantity = "SELECT TotalQuantity 
                             FROM Inventory 
                             WHERE ProductID = '$productID'";
            $resultQuantity = mysqli_query($conn, $sqlQuantity);

            if (!$resultQuantity || mysqli_num_rows($resultQuantity) == 0) {
                $_SESSION['status'] = 'error';
                $_SESSION['operation'] = 'place';
                header('location: dispatchedOrders.php');
                exit;
                //echo "Inventory not found.";
                //return;
                //used for debugging purposes
                //die("Error executing query: " . mysqli_error($conn));
            }

            $rowQuantity = mysqli_fetch_assoc($resultQuantity);
            $availableQuantity = $rowQuantity['TotalQuantity'];

            // check available quantity is enough to update an order
            if ($quantity > $availableQuantity) {
                $_SESSION['status'] = 'error';
                $_SESSION['operation'] = 'place';
                header('location: dispatchedOrders.php');
                exit;
                //echo "Available quantity is not enough.";
                //return;
            }

            // Insert dispatch order data into the salesOrder table


            $updatedQuantity = $availableQuantity - $quantity;
            if ($updatedQuantity < 0) {
                $_SESSION['status'] = 'error';
                $_SESSION['operation'] = 'place';
                header('location: dispatchedOrders.php');
                exit;
            } else {
                $sqlUpdateQuantity = "UPDATE Inventory
                                  SET TotalQuantity = '$updatedQuantity'
                                  WHERE ProductID = '$productID'";
                $resultUpdateQuantity = mysqli_query($conn, $sqlUpdateQuantity);
                if ($resultUpdateQuantity) {
                    $sqlInsertQuery = "INSERT INTO salesorders (ProductID, StoreID, quantity, orderDate)
                                       VALUES ('$productID', '$storeID', '$quantity', NOW())";
                    $resultInsertQuery = mysqli_query($conn, $sqlInsertQuery);

                    if (!$resultInsertQuery) {
                        $_SESSION['status'] = 'error';
                        $_SESSION['operation'] = 'place';
                    } else {
                        $_SESSION['status'] = 'success';
                        $_SESSION['operation'] = 'place';
                        //echo "Order placed successfully!";
                        header('location: dispatchedOrders.php');
                    }
                } else {
                    $_SESSION['status'] = 'error';
                    $_SESSION['operation'] = 'place';
                    //echo "can not perform the action";
                    header('location: dispatchedOrders.php');
                    exit;
                }
            }
        }
    } else {
        $_SESSION['status'] = 'error';
        $_SESSION['operation'] = 'place';
        header('location: dispatchedOrders.php');
        exit;
        //echo "Product not found!";
    }
}
mysqli_close($conn);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dispatch Order</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous" />
</head>

<body>
    <div class="container">
        <div class="mb-3">
            <h2>Dispatch order</h2>
        </div>
        <form method="post">
            <div class="mb-3">
                <label>Product Name:</label>
                <input
                    type="text"
                    class="form-control"
                    id="product-name"
                    name="product-name"
                    placeholder="Search Product"
                    required />
                <div class="product-list" id="product-list"></div>
            </div>

            <div class="mb-3">
                <label>Store Name:</label>
                <input
                    type="text"
                    class="form-control"
                    id="store-name"
                    name="store-name"
                    placeholder="Search Store"
                    required />
                <div class="store-list" id="store-list"></div>
            </div>

            <div class="mb-3">
                <label>Quantity:</label>
                <input
                    type="number"
                    class="form-control"
                    id="quantity"
                    name="quantity"
                    placeholder="Enter Product Quantity"
                    min="1"
                    required />
            </div>

            <div class="mt-4">
                <button type="submit"
                    name="confirm"
                    id="confirm-btn"
                    class="btn btn-primary">
                    Confirm
                </button>
                <button type="button" class="btn btn-secondary">
                    <a
                        href="dispatchedOrders.php"
                        class="text-light link-offset-2 link-underline link-underline-opacity-0">Cancel</a>
                </button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="productList.js"></script>
    <script src="storeList.js"></script>
    <script>
        //validate quantity
        $(document).ready(function() {
            $("#quantity").on("input", function() {
                var value = $(this).val();
                if (value < 1) {
                    $(this).val("");
                }
            });
        });
    </script>
    <script>
        //validate all inputs
        $(document).ready(function() {
            function validateInputs() {
                var productName = $("#product-name").val().trim();
                var storeName = $("#store-name").val().trim();
                var quantity = $("#quantity").val().trim();

                if (productName == "" || storeName == "" || quantity < 1) {
                    $("#confirm-btn").attr("disabled", true);
                } else {
                    $("#confirm-btn").attr("disabled", false);
                }
            }
            $("#product-name, #store-name, #quantity").on("input", function() {
                validateInputs();
            })

            validateInputs();
        })
    </script>
</body>

</html>