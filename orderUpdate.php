<?php
//use this for debugging purposes
//die("Error executing query: " . mysqli_error($conn));

//connect to the database
include 'Connect.php';
session_start();

$updateID = $_GET['updateID'];

//to fill form with already assigned values
$sqlFill = "SELECT
            p.ProductName, s.StoreName, so.Quantity                                                       
            FROM salesOrders so
            JOIN products p ON so.ProductID = p.ProductID
            JOIN stores s ON so.StoreID = s.StoreID
            WHERE SalesOrderID = ?";
$stmtFill = $conn->prepare($sqlFill);
$stmtFill->bind_param("i", $updateID);
$stmtFill->execute();
$resultFill = $stmtFill->get_result();

if (!$resultFill) {
    $_SESSION['status'] = 'error';
    $_SESSION['operation'] = 'update';
    header('location: dispatchedOrders.php');
    exit;
    //echo "Something went wrong. Please try again later";
}

$rowFill = $resultFill->fetch_assoc();
$fillProductName = $rowFill['ProductName'];
$fillStoreName = $rowFill['StoreName'];
$fillQuantity = $rowFill['Quantity'];

//get data from html
if (isset($_POST['confirm'])) {

    //declare variables
    $productName = $_POST['product-name']; //stored user entered product name
    $storeName = $_POST['store-name']; //stored user entered store name
    $quantity = $_POST['quantity']; //stored user entered quantity

    // Fetch productID using productName
    $sql = "SELECT ProductID 
            FROM products 
            WHERE ProductName = ?";
    $stmtProduct = $conn->prepare($sql);
    $stmtProduct->bind_param("s", $productName);
    $stmtProduct->execute();
    $result = $stmtProduct->get_result();

    // Check the query is successful executed
    if (!$result || $result->num_rows == 0) {
        $_SESSION['status'] = 'error';
        $_SESSION['operation'] = 'update';
        header('location: dispatchedOrders.php');
        exit;
        //echo "Your request is can not be done now. Please try again later.";
    }

    if (mysqli_num_rows($result)) {
        //fetch an item from result
        $row = $result->fetch_assoc();
        $productID = $row['ProductID'];

        //get StoreID using StoreName
        $sqlStore = "SELECT StoreID 
                     FROM stores 
                     WHERE StoreName = ?";
        $stmtStore = $conn->prepare($sqlStore);
        $stmtStore->bind_param("s", $storeName);
        $stmtStore->execute();
        $resultStore = $stmtStore->get_result();

        if (!$resultStore || $resultStore->num_rows == 0) {
            $_SESSION['status'] = 'error';
            $_SESSION['operation'] = 'update';
            header('location: dispatchedOrders.php');
            exit;
            //echo "Your request is can not be done now. Please try again later";
        } else {
            $rowStore = $resultStore->fetch_assoc();
            $storeID = $rowStore['StoreID'];

            // get TotalQuantity related to ProductID
            $sqlQuantity = "SELECT TotalQuantity 
                            FROM Inventory 
                            WHERE ProductID = ?";
            $stmtQuantity = $conn->prepare($sqlQuantity);
            $stmtQuantity->bind_param("i", $productID);
            $stmtQuantity->execute();
            $resultQuantity = $stmtQuantity->get_result();

            if (!$resultQuantity || $resultQuantity->num_rows == 0) {
                $_SESSION['status'] = 'error';
                $_SESSION['operation'] = 'update';
                header('location: dispatchedOrders.php');
                exit;
                //echo "Inventory not found.";
            }

            $rowQuantity = $resultQuantity->fetch_assoc();
            $availableQuantity = $rowQuantity['TotalQuantity'];

            // check available quantity is enough to update an order
            if ($quantity > $availableQuantity) {
                $_SESSION['status'] = 'error';
                $_SESSION['operation'] = 'update';
                header('location: dispatchedOrders.php');
                exit;
                //echo "Available quantity is not enough.";
            }

            // Insert updated dispatch order data into the salesOrder table
            $sqlInsertQuery = "UPDATE salesorders
                               SET ProductID = ?,
                               StoreID = ?,
                               Quantity = ?,
                               OrderDate = NOW()
                               WHERE SalesOrderID = ?";
            $stmtUpdate = $conn->prepare($sqlInsertQuery);
            $stmtUpdate->bind_param("iiii", $productID, $storeID, $quantity, $updateID);

            //display whether order is successfully dispatched or not
            if ($stmtUpdate->execute()) {
                // reduce Inventory from Inventory table
                $updatedQuantity = $availableQuantity - ($quantity - $fillQuantity);

                //ensure remaining quantity is not a negative value
                if ($updatedQuantity < 0) {
                    $_SESSION['status'] = 'error';
                    $_SESSION['operation'] = 'update';
                    header('location: dispatchedOrders.php');
                    exit;
                }

                $sqlUpdateQuantity = "UPDATE Inventory
                                      SET TotalQuantity = ?
                                      WHERE ProductID = ?";
                $stmtUpdateQuantity = $conn->prepare($sqlUpdateQuantity);
                $stmtUpdateQuantity->bind_param("ii", $updatedQuantity, $productID);
                $stmtUpdateQuantity->execute();

                if ($stmtUpdateQuantity->affected_rows > 0) {
                    $_SESSION['status'] = 'success';
                    $_SESSION['operation'] = 'update';
                    //echo "Order placed successfully!";
                    header('location: dispatchedOrders.php');
                } else {
                    $_SESSION['status'] = 'error';
                    $_SESSION['operation'] = 'update';
                    header('location: dispatchedOrders.php');
                    //echo "Can not update inventory now. Try again later.";
                }
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['operation'] = 'update';
                header('location: dispatchedOrders.php');
                //echo "Something went wrong. Can not update your inventory now.";
            }
        }
    }
}
$conn->close();

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
            <h2>Update dispatch order</h2>
        </div>
        <form method="post">
            <div class="mb-3">
                <label>Product Name:</label>
                <input
                    type="text"
                    class="form-control"
                    id="product-name"
                    name="product-name"
                    value="<?php echo htmlspecialchars($fillProductName); ?>"
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
                    value="<?php echo htmlspecialchars($fillStoreName); ?>"
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
                    value="<?php echo htmlspecialchars($fillQuantity); ?>"
                    placeholder="Enter Product Quantity"
                    min="1"
                    required />
            </div>

            <div class="mt-4">
                <button type="submit"
                    id="update-btn"
                    name="confirm"
                    class="btn btn-primary">
                    Update
                </button>
                <button type="button" class="btn btn-secondary">
                    <a href="dispatchedOrders.php" class="text-light link-offset-2 link-underline link-underline-opacity-0">Cancel</a>
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
                var value = $(this).val()
                if (value < 1) {
                    $(this).val('')
                }
            })
        })
    </script>
    <script>
        //validate all inputs
        $(document).ready(function() {
            function validateInputs() {
                var productName = $("#product-name").val().trim();
                var storeName = $("#store-name").val().trim();
                var quantity = $("#quantity").val().trim();

                if (productName == "" || storeName == "" || quantity < 1) {
                    $("#update-btn").attr("disabled", true);
                } else {
                    $("#update-btn").attr("disabled", false);
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