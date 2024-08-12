<?php
include 'db.php';

try {
    // Query to count the number of categories
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
    $categoriesCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Query to count the number of suppliers
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM suppliers");
    $suppliersCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    $stmt = $pdo->query("SELECT SUM(TotalValue) AS count FROM Inventory");
    $inventoryvaluesCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    $stmt = $pdo->query("SELECT SUM(Quantity) AS count FROM `dispatchorders`");
    $dispatchQuantityCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Return the counts as JSON
    echo json_encode([
        'categories' => $categoriesCount,
        'suppliers' => $suppliersCount,
        'inventoryvalues' => $inventoryvaluesCount,
        'dispatchquantity' => $dispatchQuantityCount
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
