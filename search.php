<?php
include 'Connect.php';

if (isset($_GET['input'])) {
    $input = '%' . $_GET['input'] . '%';
    $sql = "SELECT 
            so.SalesOrderID, p.ProductName, s.StoreName, so.Quantity, so.OrderDate
            FROM salesOrders so
            JOIN products p ON so.ProductID = p.ProductID
            JOIN stores s ON so.StoreID = s.StoreID
            WHERE so.SalesOrderID LIKE ? OR 
            p.ProductName LIKE ? OR 
            s.StoreName LIKE ? OR 
            so.Quantity LIKE ? OR 
            so.OrderDate LIKE ?
            ORDER BY so.OrderDate DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $input, $input, $input, $input, $input);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result || mysqli_num_rows($result) == 0) {
        echo '<div class="alert alert-danger" role="alert">
                No product found
              </div>';
        return;
    } else {
        echo "<br>";
        echo '<table class="table table-hover">';
        echo '<thead>
        <tr>
            <th>Order ID</th>
            <th>Product Name</th>
            <th>Store Name</th>
            <th>Quantity</th>
            <th>Order Date</th>
            <th>Actions</th>
        </tr></thead>';
        echo '<tbody>';

        while ($row = $result->fetch_assoc()) {
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
        echo '</tbody></table>';
        // Free result set
        $result->free();
    }
    // Close connection
    $stmt->close();
    mysqli_close($conn);
}
