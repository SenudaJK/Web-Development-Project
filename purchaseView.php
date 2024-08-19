<?php
session_start(); // Start session

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>view Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style> 
       
        main {
         
         padding: 20px; 
        }

        .d-flex {
          margin-top: 20px;
         }

         

         
     
     </style>
</head>
<body>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Button to toggle sidebar visibility on smaller screens -->
            <button class="btn d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar" aria-expanded="false" aria-controls="sidebar">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Sidebar navigation -->
            <nav id="sidebar" class="col-md-2 d-md-block collapse sidebar">
                <div class="sidebar">
                    <!-- Sidebar header with company logo and name -->
                    <div class="sidebar-header">
                        <img src="" alt="Logo" class="img-fluid">
                        <h4>Company Name</h4>
                    </div>
                    <!-- Sidebar navigation links -->
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php"><i class="material-icons">home</i>Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="InventoryUpdate.php"><i class="material-icons">inventory</i>inventory</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="productGet.php"><i class="material-icons">category</i>Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="purchaseView.php"><i class="material-icons">shopping_cart</i>Purchase Orders</a>                            
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="material-icons">sell</i>Dispatch Orders</a>                            
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="suppliers.php"><i class="material-icons">local_shipping</i>Suppliers</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="shopIndex.php"><i class="material-icons md-18">store</i>Shops</a>
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
                    <h1 class="h2"> Order Details</h1>
                    <div class="text-right">
                        
                        <div id="username-container">
                            <a id="username" href="#"><i class="material-icons" style="font-size:48px;">account_circle</i><?php echo htmlspecialchars($username); ?></a>
                            <!-- <span><?php echo htmlspecialchars($role); ?></span> -->
                        </div>
                    </div>
                </div>
            <!-- Main content can be added here -->
                
                <!-- Search bar with real-time filtering -->
                <div class ="display_table" >
                
                <a href="purchaseOrder.php" class="btn btn-success" ><i class="fa fa-plus"></i> Add New Purchase</a><br><br>
                <div class="input-group mb-5">
                    <input type="text" id="search" class="form-control" placeholder="Search by Product Name or Supplier Name" oninput="filterResults()">
                </div>
                <?php
                    // Include config file
                    require_once "config.php";
                    
                    // Attempt select query execution
                    $sql = "SELECT ps.PurchaseOrderID, ps.QuantityOrdered, ps.QuantityRecieved, ps.UnitPrice, ps.OrderDate, ps.Status, p.ProductName, s.SupplierName FROM products p, purchaseorders ps, suppliers s WHERE ps.ProductID = p.ProductID AND ps.SupplierID = s.SupplierID ORDER BY ps.PurchaseOrderID ;
";
                    if($result = $mysqli->query($sql)){
                        if($result->num_rows > 0){
                            echo '<div style="height: 390px; overflow-y: auto;">';
                            echo '<table class="table table-bordered table-striped" id="ordertable">';
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>Order ID</th>";
                                        echo "<th>Product Name</th>";
                                        echo "<th>Supplier Name</th>";
                                        echo "<th>Qty order</th>";
                                        echo "<th>Qty received</th>";
                                        echo "<th>Unit Price</th>";
                                        echo "<th>Ordered Date</th>";
                                        echo "<th>Status</th>";
                                        echo "<th>Action</th>";

                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = $result->fetch_array()){
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['PurchaseOrderID']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['ProductName']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['SupplierName']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['QuantityOrdered']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['QuantityRecieved']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['UnitPrice']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['OrderDate']) . "</td>";
                                    

                                    echo "<td>" . htmlspecialchars($row['Status']) . "</td>";

                                    
                                        echo "<td>";
                                            
                                            if ($role !== 'Worker') {
                                                // If the role is not 'worker', display the active links
                                                echo "<a href='purchaseRead.php?id=" . $row['PurchaseOrderID'] . "' class='link-dark'><i class='fa-solid fa-pen-to-square fs-5 me-3'></i></a>";
                                                echo "<a href='purchaseUpdate.php?id=" . $row['PurchaseOrderID'] . "' class='link-dark'><i class='fa-solid fa-trash fs-5'></i></a>";
                                            } else {
                                                // If the role is 'worker', display disabled icons without links
                                                echo "<i class='fa fa-eye fs-5 me-3' style='color: gray; cursor: not-allowed;' title='View (disabled)'></i>";
                                                echo "&nbsp;&nbsp;";
                                                echo "<i class='fa fa-pencil fs-5' style='color: gray; cursor: not-allowed;' title='Update (disabled)'></i>";
                                            }
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            echo '</div>';
                            // Free result set
                            $result->free();
                        } else{
                            echo '<div class="alert alert-danger"><em>No records were found.</em></div>';
                        }
                    } else{
                        echo "Oops! Something went wrong. Please try again later.";
                    }
                    
                    // Close connection
                    $mysqli->close();
                    ?> 
                
                </div> 
            
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
       function filterResults() {
    var search = document.getElementById('search').value.toLowerCase();
    var table = document.getElementById("ordertable");
    var rows = table.getElementsByTagName("tr");

    for (var i = 1; i < rows.length; i++) { // Start at 1 to skip the header row
        var productNameCell = rows[i].getElementsByTagName("td")[1]; // Product Name column (2nd column)
        var supplierNameCell = rows[i].getElementsByTagName("td")[2]; // Supplier Name column (3rd column)
        var match = false;

        if (productNameCell && supplierNameCell) {
            var productNameValue = productNameCell.textContent || productNameCell.innerText;
            var supplierNameValue = supplierNameCell.textContent || supplierNameCell.innerText;

            // Check if the search term matches either Product Name or Supplier Name
            if (productNameValue.toLowerCase().indexOf(search) > -1 || supplierNameValue.toLowerCase().indexOf(search) > -1) {
                match = true;
            }
        }

        if (match) {
            rows[i].style.display = "";
        } else {
            rows[i].style.display = "none";
        }
    }
}

    </script>
</body>
</html>
