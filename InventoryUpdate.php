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
    <style>
        /* CSS to ensure the sidebar stays fixed while scrolling */
        #sidebar {
            position: fixed;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            width: 250px; /* Adjust as needed */
        }

        main {
            margin-left: 250px; /* Should match the width of the sidebar */
        }

        .fade-away {
            opacity: 1;
            transition: opacity 0.5s ease-out;
        }

        .fade-away.hide {
            opacity: 0;
        }
    </style>
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
                            <a class="nav-link" href="#"><i class="material-icons">inventory</i>Inventory</a>
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
                            <a class="nav-link" href="#"><i class="material-icons md-18">report</i>Store</a>
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
                                echo "<div class='alert alert-success fade-away' role='alert'>Inventory updated successfully!</div>";
                            } else {
                                echo "<div class='alert alert-danger fade-away' role='alert'>Error updating inventory: " . $stmt->error . "</div>";
                            }
                        } else {
                            echo "<div class='alert alert-warning fade-away' role='alert'>Quantity to remove exceeds current inventory.</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger fade-away' role='alert'>Product not found in inventory.</div>";
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
                                    echo "<td><button class='btn btn-danger' onclick='showRemoveModal(" . $row["ProductID"] . ")'>Remove</button></td>";
                                    echo "</tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>

                    <!-- Modal for removing quantity -->
                    <div class="modal fade" id="removeModal" tabindex="-1" aria-labelledby="removeModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="removeModalLabel">Remove Quantity</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="post" id="removeForm">
                                        <input type="hidden" name="productID" id="productID">
                                        <div class="mb-3">
                                            <label for="quantity" class="form-label">Quantity to Remove</label>
                                            <input type="number" class="form-control" id="quantity" name="quantity" required>
                                        </div>
                                        <button type="submit" class="btn btn-danger" name="updateQuantity">Remove</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            // Function to show the remove modal with the correct Product ID
            window.showRemoveModal = function (productID) {
                document.getElementById('productID').value = productID;
                var removeModal = new bootstrap.Modal(document.getElementById('removeModal'));
                removeModal.show();
            }

            // Function to filter the table by brand and type
            window.filterTable = function () {
                var brand = document.getElementById('filterBrand').value.toLowerCase();
                var type = document.getElementById('filterType').value.toLowerCase();
                var rows = document.querySelectorAll('#inventoryTableBody tr');

                rows.forEach(row => {
                    var rowBrand = row.children[2].textContent.toLowerCase();
                    var rowType = row.children[3].textContent.toLowerCase();

                    if ((brand === '' || rowBrand.includes(brand)) &&
                        (type === '' || rowType.includes(type))) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            // Function to search the table
            window.searchTable = function () {
                var input = document.getElementById('searchInput').value.toLowerCase();
                var rows = document.querySelectorAll('#inventoryTableBody tr');

                rows.forEach(row => {
                    var cells = row.getElementsByTagName('td');
                    var found = Array.from(cells).some(cell => cell.textContent.toLowerCase().includes(input));

                    row.style.display = found ? '' : 'none';
                });
            }

            // Function to handle alert fading away
            function fadeAlerts() {
                var alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    setTimeout(() => {
                        alert.classList.add('hide');
                        setTimeout(() => alert.remove(), 500); // Remove after fade-out
                    }, 3000); // Adjust delay as needed
                });
            }

            fadeAlerts();
        });
    </script>
</body>

</html>
