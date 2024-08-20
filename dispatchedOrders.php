<?php
session_start();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dispatch Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="sketch.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Button to toggle sidebar visibility on smaller screens -->
            <button class="btn d-md-none"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#sidebar"
                aria-expanded="false"
                aria-controls="sidebar">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Sidebar navigation -->
            <nav
                id="sidebar"
                class="col-md-2 d-md-block collapse sidebar">
                <div class="sidebar">
                    <!-- Sidebar header with company logo and name -->
                    <div class="sidebar-header">
                        <img src="" alt="Logo" class="img-fluid">
                        <h4>Company Name</h4>
                    </div>
                    <!-- Sidebar navigation links -->
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href=""><i class="material-icons">home</i>Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="material-icons">inventory</i>inventory</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="material-icons">category</i>Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="material-icons">shopping_cart</i>Purchase Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="material-icons">sell</i>Dispatch Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="material-icons">local_shipping</i>Suppliers</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="material-icons md-18">report</i>Reports</a>
                        </li>
                    </ul>
                    <!-- Logout link at the bottom of the sidebar -->
                    <div class="logout">
                        <a href="#"><i class="material-icons">logout</i>Log out</a>
                    </div>
                </div>
            </nav>

            <!-- Main content area -->
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">

                <!-- Header for the main content with title and user information -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dispatch Orders</h1>
                    <div class="text-right">
                        <div id="username-container">
                            <a id="username" href="#"><i class="material-icons" style="font-size:48px;">account_circle</i>Username</a>
                            <span>Role</span>
                        </div>
                    </div>
                </div>

                <!-- show alert messages -->
                <?php
                if (isset($_SESSION['status']) && isset($_SESSION['operation'])) {
                    $status = $_SESSION['status'];
                    $operation = $_SESSION['operation'];
                    if ($status == 'success') {
                        $alertClass = "alert-success";
                    } else {
                        $alertClass = "alert-danger";
                    }

                    if ($status == 'success') {
                        $message = "Order " . $operation . "d successfully";
                    } else {
                        $message = "Fail to " . $operation . " order. Try again later.";
                    }

                    echo "<div class='alert $alertClass alert-dismissible fade show' role='alert'>
                            <strong>$message</strong>
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                          </div>";

                    //clear the session status
                    unset($_SESSION['status']);
                    unset($_SESSION['operation']);
                }
                ?>


                <!-- Main content can be added here -->
                <!--methana idala oyalage part eka gahanna-->
                <div class="wrapper">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mt-1 mb-3 clearfix">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <!-- place new order button -->
                                        <a href="dispatchOrder.php" class="btn btn-success"><i class="fa fa-plus"></i> Place New Order</a>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <form method="GET" action="search.php" class="d-flex align-items-center">
                                            <input
                                                type="text"
                                                name="search"
                                                id="search"
                                                class="form-control me-2"
                                                placeholder="Search order"><br>
                                        </form>
                                    </div>
                                    <br>
                                    <div style="height: 780px; overflow-y: auto; scrollbar-width: none; -ms-overflow-style: none;">
                                        <table class="table table-hover" id="table">
                                            <thead style="position: sticky; top: 0; background-color: white; z-index: 100;">
                                                <tr>
                                                    <th scope="col">SalesOrderID</th>
                                                    <th scope="col">Product Name</th>
                                                    <th scope="col">Store Name</th>
                                                    <th scope="col">Location</th>
                                                    <th scope="col">Quantity</th>
                                                    <th scope="col">Order Date</th>
                                                    <th scope="col">Operation</th>

                                                </tr>
                                            </thead>
                                            <tbody>

                                                <?php
                                                // connect to the database
                                                include "Connect.php";

                                                //  query to select details about dispatched order
                                                $sql = "SELECT 
                                                        so.SalesOrderID, p.ProductName, s.StoreName, s.Location, so.Quantity, so.OrderDate
                                                        FROM salesOrders so
                                                        JOIN products p ON so.ProductID = p.ProductID
                                                        JOIN stores s ON so.StoreID = s.StoreID
                                                        ORDER BY so.OrderDate DESC
                                                        ";

                                                //store results
                                                $result = mysqli_query($conn, $sql);
                                                if (mysqli_num_rows($result) > 0) {

                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        $saleOrderID = $row['SalesOrderID'];
                                                        $productName = $row['ProductName'];
                                                        $storeName = $row['StoreName'];
                                                        $location = $row['Location'];
                                                        $quantity = $row['Quantity'];
                                                        $orderDate = $row['OrderDate'];

                                                        echo '<tr>
                                                        <td>' . $saleOrderID . '</td>
                                                        <td>' . $productName . '</td>
                                                        <td>' . $storeName . '</td>
                                                        <td>' . $location . '</td>
                                                        <td>' . $quantity . '</td>                                                                                                             
                                                        <td>' . $orderDate . '</td>
                                                        <td>
                                                            <button type="button" class="btn btn-link">
                                                                <a href="orderUpdate.php?updateID=' . $saleOrderID . '" class="text-dark link-offset-2 link-underline link-underline-opacity-0">
                                                                <i class="material-icons">edit</i>
                                                                </a>
                                                            </button>
                                                            <button type="button" class="btn btn-link">
                                                                <a href="orderDelete.php?deleteID=' . $saleOrderID . '" class="text-dark link-offset-2 link-underline link-underline-opacity-0">
                                                                <i class="material-icons">delete</i>
                                                                </a>
                                                            </button>
                                                        </td>
                                                        </tr>';
                                                    }

                                                    // Free result set
                                                    mysqli_free_result($result);
                                                } else {
                                                    if (mysqli_num_rows($result) == 0) {
                                                        echo "No orders placed yet.";
                                                    } else {
                                                        echo "Database connection error.";
                                                    }
                                                }
                                                // Close connection
                                                mysqli_close($conn);
                                                ?>
                                            </tbody>
                                        </table>

                                        <!-- to show search results -->
                                        <div id="search-result"></div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="searchList.js"></script>

    <!-- alert function created by @senuda -->
    <script>
        function fadeAlerts() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.classList.add('hide');
                    setTimeout(() => alert.remove(), 500); // Remove after fade-out
                }, 3000); // Adjust delay as needed
            });
        }

        // call the function after page loads
        window.onload = function() {
            fadeAlerts();
        }
    </script>

</body>

</html>