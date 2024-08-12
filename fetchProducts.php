<?php

// Check the query is successful executed
include 'Connect.php';

//get user typings into 'query'
if (isset($_POST['query'])) {

    //declare variables
    $query = $_POST['query']; //store user input
    $sql = "SELECT ProductName 
            FROM products 
            WHERE ProductName LIKE '$query%'";
    $result = mysqli_query($conn, $sql);

    //show matching item list in list group
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
