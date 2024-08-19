CREATE DATABASE Camera_Warehouse;

USE Camera_Warehouse;

CREATE TABLE Users (
  UserID INT AUTO_INCREMENT PRIMARY KEY,
  Username VARCHAR(50) NOT NULL UNIQUE,
  PasswordHash VARCHAR(255) NOT NULL,
  PhoneNumber VARCHAR(10) NOT NULL UNIQUE,
  Role VARCHAR(50),
  CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Suppliers (
  SupplierID INT AUTO_INCREMENT PRIMARY KEY,
  SupplierName VARCHAR(100) NOT NULL,
 Location VARCHAR(100)  NOT NULL,
  ContactEmail VARCHAR(100) NOT NULL
);

CREATE TABLE shop (
    ShopID INT(4) AUTO_INCREMENT PRIMARY KEY,
    Man_name VARCHAR(20) NOT NULL,
    Address VARCHAR(30) NOT NULL,
    SEmail VARCHAR(20) NOT NULL
);

CREATE TABLE Products (
  ProductID INT AUTO_INCREMENT PRIMARY KEY,
  ProductName VARCHAR(100) NOT NULL,
  Brand VARCHAR(100) NOT NULL,
  Type VARCHAR(100) NOT NULL,
  SKU VARCHAR(50),
  DateAdded TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE DispatchOrders (
  DispatchOrderID INT AUTO_INCREMENT PRIMARY KEY,
  ProductID INT,
  Quantity INT,
  UnitPrice DECIMAL(10, 2),
  OrderDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (ProductID) REFERENCES products(ProductID)
);

CREATE TABLE PurchaseOrders (
  PurchaseOrderID INT AUTO_INCREMENT PRIMARY KEY,
  SupplierID INT,
  ProductID INT,
  QuantityOrdered INT,
  QuantityRecieved INT,
  UnitPrice DECIMAL(10, 2),
  Status VARCHAR(50),
  OrderDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (SupplierID) REFERENCES Suppliers(SupplierID),
  FOREIGN KEY (ProductID) REFERENCES products(ProductID)
);

CREATE TABLE Inventory (
  ProductID INT PRIMARY KEY,
  ProductName VARCHAR(100),
  Brand VARCHAR(100),
  Type VARCHAR(100),
  SKU VARCHAR(50),
  TotalQuantity INT,
  LastReceivedDate TIMESTAMP,
  TotalValue DECIMAL(10, 2)
);

CREATE TABLE productreceiveddate (
    PurchaseOrderID  INT(11),
    DateReceived     TIMESTAMP,
    quantity         INT(50),
    PRIMARY KEY (PurchaseOrderID, DateReceived),
    FOREIGN KEY (PurchaseOrderID) REFERENCES purchaseorders(PurchaseOrderID)
);

CREATE DATABASE Camera_Warehouse;

USE Camera_Warehouse;

CREATE TABLE Users (
  UserID INT AUTO_INCREMENT PRIMARY KEY,
  Username VARCHAR(50) NOT NULL UNIQUE,
  PasswordHash VARCHAR(255) NOT NULL,
  PhoneNumber VARCHAR(10) NOT NULL UNIQUE,
  Role VARCHAR(50),
  CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Suppliers (
  SupplierID INT AUTO_INCREMENT PRIMARY KEY,
  SupplierName VARCHAR(100) NOT NULL,
 Location VARCHAR(100)  NOT NULL,
  ContactEmail VARCHAR(100) NOT NULL
);

CREATE TABLE shop (
    ShopID INT(4) AUTO_INCREMENT PRIMARY KEY,
    Man_name VARCHAR(20) NOT NULL,
    Address VARCHAR(30) NOT NULL,
    SEmail VARCHAR(20) NOT NULL
);

CREATE TABLE Products (
  ProductID INT AUTO_INCREMENT PRIMARY KEY,
  ProductName VARCHAR(100) NOT NULL,
  Brand VARCHAR(100) NOT NULL,
  Type VARCHAR(100) NOT NULL,
  SKU VARCHAR(50),
  DateAdded TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE DispatchOrders (
  DispatchOrderID INT AUTO_INCREMENT PRIMARY KEY,
  ProductID INT,
  Quantity INT,
  UnitPrice DECIMAL(10, 2),
  OrderDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (ProductID) REFERENCES products(ProductID)
);

CREATE TABLE PurchaseOrders (
  PurchaseOrderID INT AUTO_INCREMENT PRIMARY KEY,
  SupplierID INT,
  ProductID INT,
  QuantityOrdered INT,
  QuantityRecieved INT,
  UnitPrice DECIMAL(10, 2),
  Status VARCHAR(50),
  OrderDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (SupplierID) REFERENCES Suppliers(SupplierID),
  FOREIGN KEY (ProductID) REFERENCES products(ProductID)
);

CREATE TABLE Inventory (
  ProductID INT PRIMARY KEY,
  ProductName VARCHAR(100),
  Brand VARCHAR(100),
  Type VARCHAR(100),
  SKU VARCHAR(50),
  TotalQuantity INT,
  LastReceivedDate TIMESTAMP,
  TotalValue DECIMAL(10, 2)
);

CREATE TABLE productreceiveddate (
    PurchaseOrderID  INT(11),
    DateReceived     TIMESTAMP,
    quantity         INT(50),
    PRIMARY KEY (PurchaseOrderID, DateReceived),
    FOREIGN KEY (PurchaseOrderID) REFERENCES purchaseorders(PurchaseOrderID)
);

DELIMITER $$

CREATE PROCEDURE UpdateInventory()
BEGIN
    INSERT INTO Inventory (ProductID, ProductName, Brand, Type, SKU, TotalQuantity, LastReceivedDate, TotalValue)
    SELECT 
        p.ProductID,
        p.ProductName,
        p.Brand,
        p.Type,
        p.SKU,
        SUM(po.QuantityRecieved) AS TotalQuantity,
        MAX(po.OrderDate) AS LastReceivedDate,
        SUM(po.QuantityRecieved * po.UnitPrice) AS TotalValue
    FROM 
        PurchaseOrders po
    JOIN 
        Products p ON po.ProductID = p.ProductID
    GROUP BY 
        p.ProductID, p.ProductName, p.Brand, p.Type, p.SKU
    ON DUPLICATE KEY UPDATE 
        TotalQuantity = VALUES(TotalQuantity),
        LastReceivedDate = VALUES(LastReceivedDate),
        TotalValue = VALUES(TotalValue);
END$$

DELIMITER ;
