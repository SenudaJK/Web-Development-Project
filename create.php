<?php
// Include config file
require_once "Connect.php";

// Define variables and initialize with empty values
$name = $location = $email = "";
$name_err = $location_err = $email_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    $input_name = trim($_POST["name"]);
    if (empty($input_name)) {
        $name_err = "Please enter a name.";
    } elseif (!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[a-zA-Z\s]+$/")))) {
        $name_err = "Please enter a valid name.";
    } else {
        $name = $input_name;
    }

    // Validate location
    $input_location = trim($_POST["location"]);
    if (empty($input_location)) {
        $location_err = "Please enter an address.";
    } else {
        $location = $input_location;
    }

    // Validate email
    $input_email = trim($_POST["email"]);
    if (empty($input_email)) {
        $email_err = "Fill the email address!<br />";
    } else {
        // Check if email contains any uppercase letters
        if (preg_match('/[A-Z]/', $input_email)) {
            $email_err = "This email address must contain only lowercase letters.";
        } elseif (!filter_var($input_email, FILTER_VALIDATE_EMAIL)) {
            $email_err = "This email address is invalid.";
        } else {
            $email = $input_email;
        }
    }

    // Check input errors before inserting in database
    if (empty($name_err) && empty($location_err) && empty($email_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO suppliers (name, location, email) VALUES (?, ?, ?)";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("sss", $param_name, $param_location, $param_email);

            // Set parameters
            $param_name = $name;
            $param_location = $location;
            $param_email = $email;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Records created successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        $stmt->close();
    }

    // Close connection
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper {
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Create Record</h2>
                    <p>Please fill this form and submit to add employee record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                            <span class="invalid-feedback"><?php echo $name_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>location</label>
                            <textarea name="location" class="form-control <?php echo (!empty($location_err)) ? 'is-invalid' : ''; ?>"><?php echo $location; ?></textarea>
                            <span class="invalid-feedback"><?php echo $location_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>Supplier's Email</label>
                            <input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                            <span class="invalid-feedback"><?php echo $email_err; ?></span>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>