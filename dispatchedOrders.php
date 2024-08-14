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
                <!-- Main content can be added here -->
                <!--methana idala oyalage part eka gahanna-->
                <div class="wrapper">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mt-5 mb-3 clearfix">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h2 class="mb-0">Dispatch Order</h2>
                                        <a href="dispatchOrder.html" class="btn btn-success"><i class="fa fa-plus"></i> Place New Order</a>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <form method="GET" action="index.php" class="d-flex align-items-center">
                                            <input
                                                type="text"
                                                name="search"
                                                class="form-control me-2"
                                                placeholder="Search by Name"
                                                value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                                            <button class="btn btn-outline-success" type="submit">Search</button>
                                        </form>
                                    </div>
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th scope="col">SalesOrderID</th>
                                                <th scope="col">Product Name</th>
                                                <th scope="col">Store Name</th>
                                                <th scope="col">Quantity</th>
                                                <th scope="col">Order Date</th>
                                                <th scope="col">Operation</th>

                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php
                                            // connect to the database
                                            include "Connect.php";
                                            // Initialize search variable
                                            //$search = isset($_GET['search']) ? $mysqli_real_escape_string($_GET['search']) : '';

                                            //  query to select details about dispatched order
                                            $sql = "SELECT 
                                                        so.SalesOrderID,
                                                        p.ProductName,
                                                        s.StoreName,
                                                        so.Quantity,                                                       
                                                        so.OrderDate
                                                        FROM salesOrders so
                                                        JOIN products p ON so.ProductID = p.ProductID
                                                        JOIN stores s ON so.StoreID = s.StoreID
                                                        LIMIT 13";

                                            //store results
                                            $result = mysqli_query($conn, $sql);

                                            /* if (!empty($search)) {
                                    $sql .= " WHERE Name LIKE '%$search%'";
                                } */

                                            if ($result) {

                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    $saleOrderID = $row['SalesOrderID'];
                                                    $productName = $row['ProductName'];
                                                    $storeName = $row['StoreName'];
                                                    $quantity = $row['Quantity'];
                                                    $orderDate = $row['OrderDate'];

                                                    echo '<tr>
                                                        <td>' . $saleOrderID . '</td>
                                                        <td>' . $productName . '</td>
                                                        <td>' . $storeName . '</td>
                                                        <td>' . $quantity . '</td>                                                                                                             
                                                        <td>' . $orderDate . '</td>
                                                        <td>
                                                            <button type="button" class="btn btn-primary">
                                                                <a href="orderUpdate.php" class="text-light">Update</a>
                                                            </button>
                                                            <button type="button" class="btn btn-danger">
                                                                <a href="orderDelete.php?deleteID=' . $saleOrderID . '" class="text-light">Delete</a>
                                                            </button>
                                                        </td>
                                                        </tr>';
                                                }

                                                // Free result set
                                                mysqli_free_result($result);
                                            }
                                            // Close connection
                                            mysqli_close($conn);
                                            ?>
                                        </tbody>
                                    </table>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>