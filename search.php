

<?php
include 'Connect.php';

if (isset($_GET['input'])) {
    $input = $_GET['input'];
    $sql = "SELECT 
            so.SalesOrderID, p.ProductName, s.StoreName, so.Quantity, so.OrderDate
            FROM salesOrders so
            JOIN products p ON so.ProductID = p.ProductID
            JOIN stores s ON so.StoreID = s.StoreID
            WHERE p.ProductName LIKE '%$input%'
            ORDER BY so.OrderDate DESC
            LIMIT 13";
    $result = mysqli_query($conn, $sql);
    if (!$result || mysqli_num_rows($result) == 0) {
        echo "No order Found.";
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
                            <a href="orderUpdate.php?updateID=' . $saleOrderID . '" class="text-light link-offset-2 link-underline link-underline-opacity-0">Update</a>
                        </button>
                        <button type="button" class="btn btn-danger">
                            <a href="orderDelete.php?deleteID=' . $saleOrderID . '" class="text-light link-offset-2 link-underline link-underline-opacity-0">Delete</a>
                        </button>
                    </td>
                    </tr>';
        }
        echo '</tbody></table>';
        // Free result set
        mysqli_free_result($result);
    }
    // Close connection
    mysqli_close($conn);
}
