<?php
$conn = new mysqli('localhost', 'root', '', 'inventorydb');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . " (Error No: " . $conn->connect_errno . ")");
}
