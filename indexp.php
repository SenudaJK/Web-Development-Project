<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset= "UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewpoint" content="width=device-width, initial-scale=1.0">
    <title> Supplier </title>
    <link rel="stylesheet" href="sketch.html">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="sketch.css">
</head>
<body>
    <div class="container my-5">
        <h2> Suppliers </h2>
        <a class= "btn btn-primary" href="create.php" role= "button"> Add New Supplier</a>
        <br>
        <table class= "table">
            <thead>
                <tr>
                    <th>Supplier ID</th>
                    <th>Supplier Name</th>
                    <th>Location</th>
                    <th>Email</th>
</tr>
</thead>
<tbody>
    <?php
    

    // Include config file
    require_once "config.php";


    // read all row from database table
    $sql = "SELECT * FROM suppliers";
    if($result = $mysqli->query($sql)){
        if (!$result) {
            die("Invalid query: " . $connection->error);
        }
        while($row = $result->fetch_assoc()) {
            echo "
            <tr>
                <td>$row[SupplierID]</td>
                <td>$row[Name]</td>
                <td>$row[Location]</td>
                <td>$row[Email]</td>
                <td>
                    <a class='btn btn-primary btn-sm' href='update.php?id=$row[SupplierID]'>Update </a>
                    <a class='btn btn-danger btn-sm' href='delete.php?id=$row[SupplierID]'>Delete</a>
                </td>
            </tr>
    
            ";
    
        }
    } else {
        die("Invalid query: " . $mysqli->error);
    }
    
    ?>

</table>
</div>

</body>
</html>

    