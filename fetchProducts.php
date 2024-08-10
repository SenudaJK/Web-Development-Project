<?php
include 'Connect.php';

if (isset($_POST['query'])) {
    $query = $_POST['query'];
    $sql = "SELECT ProductName 
            FROM products 
            WHERE ProductName LIKE '$query%'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo '<ul class="list-group">';
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<li class="list-group-item product-list-item">' . $row['ProductName'] . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p class="text-danger">No products found</p>';
    }
}
