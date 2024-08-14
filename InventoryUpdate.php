<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="sketch.css">
    <style>
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
                            <a class="nav-link active" href="dashboard.php"><i class="material-icons">home</i>Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="InventoryUpdate.php"><i class="material-icons">inventory</i>Inventory</a>
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

                    <div style="height: 300px; overflow-y: auto;">
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
                                    echo "<td>" . htmlspecialchars($row["ProductID"] ) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["ProductName"])  . "</td>";
                                    echo "<td>" . htmlspecialchars($row["Brand"])  . "</td>";
                                    echo "<td>" . htmlspecialchars($row["Type"])  . "</td>";
                                    echo "<td>" . htmlspecialchars($row["SKU"])  . "</td>";
                                    echo "<td>" . htmlspecialchars($row["TotalQuantity"])  . "</td>";
                                    echo "<td>" . htmlspecialchars($row["LastReceivedDate"])  . "</td>";
                                    echo "<td>" . htmlspecialchars($row["TotalValue"])  . "</td>";
                                    echo "<td><button class='btn btn-danger' onclick='showRemoveModal(" . $row["ProductID"] . ", \"" . htmlspecialchars($row["ProductName"]) . "\")'>Remove</button></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9'>No records found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    </div>
                </div>

                <!-- Remove Quantity Modal -->
                <div class="modal fade" id="removeModal" tabindex="-1" role="dialog" aria-labelledby="removeModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="removeModalLabel">Remove Quantity</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="removeForm" method="POST">
                                    <input type="hidden" id="productID" name="productID">
                                    <div class="form-group">
                                        <label for="quantity">Quantity to remove</label>
                                        <input type="number" class="form-control" id="quantity" name="quantity" min="1"
                                            required>
                                    </div>
                                    <button type="submit" class="btn btn-danger mt-3" name="updateQuantity" >Remove</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const alertElement = document.querySelector(".fade-away");

            if (alertElement) {
                setTimeout(() => {
                    alertElement.classList.add("hide");
                    setTimeout(() => alertElement.remove(), 500);
                }, 2000);
            }
        });

        function showRemoveModal(productID, productName) {
            const productIDField = document.getElementById("productID");
            productIDField.value = productID;

            const removeForm = document.getElementById("removeForm");
            removeForm.onsubmit = function (event) {
                if (!confirm("Are you sure you want to update the quantity for " + productName + "?")) {
                    event.preventDefault(); // Prevent form submission if user cancels
                }
            };

            const removeModal = new bootstrap.Modal(document.getElementById("removeModal"));
            removeModal.show();
        }

        function searchTable() {
            const input = document.getElementById("searchInput");
            const filter = input.value.toUpperCase();
            const table = document.querySelector("table tbody");
            const rows = table.getElementsByTagName("tr");

            for (let i = 0; i < rows.length; i++) {
                let row = rows[i];
                let cells = row.getElementsByTagName("td");
                let found = false;

                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].innerText.toUpperCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }

                if (found) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            }
        }

        function filterTable() {
            const filterBrand = document.getElementById("filterBrand").value.toUpperCase();
            const filterType = document.getElementById("filterType").value.toUpperCase();
            const table = document.querySelector("table tbody");
            const rows = table.getElementsByTagName("tr");

            for (let i = 0; i < rows.length; i++) {
                let row = rows[i];
                let brandCell = row.getElementsByTagName("td")[2];
                let typeCell = row.getElementsByTagName("td")[3];
                let brandMatch = filterBrand === "" || brandCell.innerText.toUpperCase().indexOf(filterBrand) > -1;
                let typeMatch = filterType === "" || typeCell.innerText.toUpperCase().indexOf(filterType) > -1;

                if (brandMatch && typeMatch) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            }
        }
    </script>
</body>

</html>
