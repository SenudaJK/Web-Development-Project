<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Name</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="sketch.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Button to toggle sidebar visibility on smaller screens -->
            <button class="btn d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar"
                aria-expanded="false" aria-controls="sidebar">
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
                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Inventory</h1>
                    <div class="text-right">
                        <div id="username-container">
                            <a id="username" href="#"><i class="material-icons"
                                    style="font-size:48px;">account_circle</i>Username</a>
                            <span>Role</span>
                        </div>
                    </div>
                </div>
                <!-- Main content can be added here -->
                <!--methana idala oyalage part eka gahanna-->
                <?php
                // Database connection
                $servername = "localhost:3307";
                $username = "root";
                $password = "";
                $dbname = "camerainventory";

                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Handle quantity update
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateQuantity'])) {
                    $productID = $_POST['productID'];
                    $quantityToRemove = $_POST['quantity'];

                    // Fetch the current details for the product from Inventory
                    $stmt = $conn->prepare("SELECT TotalQuantity, TotalValue, (TotalValue / TotalQuantity) AS UnitPrice FROM Inventory WHERE ProductID = ?");
                    $stmt->bind_param("i", $productID);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $product = $result->fetch_assoc();

                    if ($product) {
                        $currentQuantity = $product['TotalQuantity'];
                        $currentTotalValue = $product['TotalValue'];
                        $unitPrice = $product['UnitPrice'];

                        if ($quantityToRemove <= $currentQuantity) {
                            // Calculate new total quantity and total value
                            $newQuantity = $currentQuantity - $quantityToRemove;
                            $newTotalValue = $newQuantity * $unitPrice;

                            // Update Inventory table with new values
                            $stmt = $conn->prepare("UPDATE Inventory SET TotalQuantity = ?, TotalValue = ? WHERE ProductID = ?");
                            $stmt->bind_param("idi", $newQuantity, $newTotalValue, $productID);
                            if ($stmt->execute()) {
                                echo "<div class='alert alert-success' role='alert'>Inventory updated successfully!</div>";
                            } else {
                                echo "<div class='alert alert-danger' role='alert'>Error updating inventory: " . $stmt->error . "</div>";
                            }
                        } else {
                            echo "<div class='alert alert-warning' role='alert'>Quantity to remove exceeds current inventory.</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger' role='alert'>Product not found in inventory.</div>";
                    }

                    $stmt->close();
                }

                // Fetch data from Inventory
                $sql = "SELECT * FROM Inventory";
                $result = $conn->query($sql);

                if (!$result) {
                    die("Error executing query: " . $conn->error);
                }

                // Fetch distinct brands and types for the dropdowns
                $brandSql = "SELECT DISTINCT Brand FROM Inventory";
                $brandResult = $conn->query($brandSql);

                $typeSql = "SELECT DISTINCT Type FROM Inventory";
                $typeResult = $conn->query($typeSql);
                ?>

                <!DOCTYPE html>
                <html lang="en">

                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Inventory Management</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
                        rel="stylesheet">
                    <style>
                        /* Custom CSS Styling */
                        body {
                            background-color: #f8f9fa;
                            font-family: 'Arial', sans-serif;
                        }

                        h2 {
                            margin-bottom: 20px;
                            color: #343a40;
                        }

                        .table {
                            background-color: #fff;
                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                        }

                        .table th {
                            background-color: #007bff;
                            color: #fff;
                            text-align: center;
                        }

                        .table td {
                            text-align: center;
                            vertical-align: middle;
                        }

                        .table tr:nth-child(even) {
                            background-color: #f2f2f2;
                        }

                        .form-control {
                            width: 300px;
                            margin: 20px auto;
                            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                        }
                    </style>
                </head>

                <body>

                    <div class="container mt-5">
                        <h2 class="text-center">Inventory Management</h2>

                        <!-- Filters -->
                        <div class="d-flex mb-3">
                            <select id="filterBrand" class="form-control me-2" onchange="filterTable()">
                                <option value="">Select Brand</option>
                                <?php
                                if ($brandResult->num_rows > 0) {
                                    while ($row = $brandResult->fetch_assoc()) {
                                        echo "<option value='" . $row["Brand"] . "'>" . $row["Brand"] . "</option>";
                                    }
                                }
                                ?>
                            </select>

                            <select id="filterType" class="form-control" onchange="filterTable()">
                                <option value="">Select Type</option>
                                <?php
                                if ($typeResult->num_rows > 0) {
                                    while ($row = $typeResult->fetch_assoc()) {
                                        echo "<option value='" . $row["Type"] . "'>" . $row["Type"] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Search Bar -->
                        <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search for products..."
                            class="form-control mb-3">

                        <!-- Inventory Table -->
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Brand</th>
                                    <th>Type</th>
                                    <th>SKU</th>
                                    <th>Total Quantity</th>
                                    <th>Last Received Date</th>
                                    <th>Total Value</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="inventoryTableBody">
                                <?php
                                if ($result->num_rows > 0) {
                                    // Output data of each row
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $row["ProductID"] . "</td>";
                                        echo "<td>" . $row["ProductName"] . "</td>";
                                        echo "<td>" . $row["Brand"] . "</td>";
                                        echo "<td>" . $row["Type"] . "</td>";
                                        echo "<td>" . $row["SKU"] . "</td>";
                                        echo "<td>" . $row["TotalQuantity"] . "</td>";
                                        echo "<td>" . $row["LastReceivedDate"] . "</td>";
                                        echo "<td>" . $row["TotalValue"] . "</td>";
                                        echo "<td><button class='btn btn-warning' onclick='openUpdateModal(" . $row["ProductID"] . ", " . $row["TotalQuantity"] . ")'>Update Quantity</button></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='9'>No records found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="updateQuantityModal" tabindex="-1"
                        aria-labelledby="updateQuantityModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="updateQuantityModalLabel">Update Quantity</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form method="post">
                                    <div class="modal-body">
                                        <input type="hidden" id="modalProductID" name="productID">
                                        <div class="mb-3">
                                            <label for="modalQuantity" class="form-label">Quantity to Remove</label>
                                            <input type="number" class="form-control" id="modalQuantity" name="quantity"
                                                required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary"
                                            name="updateQuantity">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
                    <script>
                        function openUpdateModal(productID, currentQuantity) {
                            document.getElementById('modalProductID').value = productID;
                            document.getElementById('modalQuantity').value = '';
                            var myModal = new bootstrap.Modal(document.getElementById('updateQuantityModal'));
                            myModal.show();
                        }

                        function filterTable() {
                            let brandFilter = document.getElementById("filterBrand").value.toUpperCase();
                            let typeFilter = document.getElementById("filterType").value.toUpperCase();
                            let tableBody = document.getElementById("inventoryTableBody");
                            let rows = tableBody.getElementsByTagName("tr");

                            for (let i = 0; i < rows.length; i++) {
                                let cells = rows[i].getElementsByTagName("td");
                                let brand = cells[2].textContent.toUpperCase();
                                let type = cells[3].textContent.toUpperCase();
                                let display = true;

                                if (brandFilter && brand.indexOf(brandFilter) === -1) {
                                    display = false;
                                }

                                if (typeFilter && type.indexOf(typeFilter) === -1) {
                                    display = false;
                                }

                                rows[i].style.display = display ? "" : "none";
                            }
                        }

                        function searchTable() {
                            let input = document.getElementById("searchInput");
                            let filter = input.value.toUpperCase();
                            let table = document.querySelector("table");
                            let tr = table.getElementsByTagName("tr");

                            for (let i = 1; i < tr.length; i++) {
                                let td = tr[i].getElementsByTagName("td");
                                let match = false;
                                for (let j = 0; j < td.length; j++) {
                                    if (td[j].textContent.toUpperCase().indexOf(filter) > -1) {
                                        match = true;
                                    }
                                }
                                if (match) {
                                    tr[i].style.display = "";
                                } else {
                                    tr[i].style.display = "none";
                                }
                            }
                        }
                    </script>

                </body>

                </html>

                <?php
                $conn->close();
                ?>



</body>

</html>

</main>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>