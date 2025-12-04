/*
\. path-to-this-file
*/

DROP DATABASE IF EXISTS `acerdb`;
CREATE DATABASE `acerdb`;
USE `acerdb`;

DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
    `CustomerID` INT PRIMARY KEY AUTO_INCREMENT,
    `CustomerName` VARCHAR(255),
    `ContactName` VARCHAR(255),
    `Address` VARCHAR(255),
    `City` VARCHAR(255),
    `PostalCode` VARCHAR(20),
    `Country` VARCHAR(50)
);

INSERT INTO `customers` (
    `CustomerName`,
    `ContactName`,
    `Address`,
    `City`,
    `PostalCode`,
    `Country`
) VALUES (
    "Alfreds Futterkiste",
    "Maria Anders",
    "Obere Str. 57",
    "Berlin",
    "M12209",
    "Germany"
);

INSERT INTO `customers` (
    `CustomerName`,
    `ContactName`,
    `Address`,
    `City`,
    `PostalCode`,
    `Country`
) VALUES (
    "Ana Trujillo Emparedados y helados",
    "Ana Trujillo",
    "Avda. de la Constitución 2222",
    "México D.F.",
    "05021",
    "Mexico"
);

INSERT INTO `customers` (
    `CustomerName`,
    `ContactName`,
    `Address`,
    `City`,
    `PostalCode`,
    `Country`
) VALUES (
    "Antonio Moreno Taquería",
    "Antonio Moreno",
    "Mataderos 2312",
    "México D.F.",
    "05023",
    "Mexico"
);

INSERT INTO `customers` (
    `CustomerName`,
    `ContactName`,
    `Address`,
    `City`,
    `PostalCode`,
    `Country`
) VALUES (
    "Around the Horn",
    "Thomas Hardy",
    "120 Hanover Sq.",
    "London",
    "WA1 1DP",
    "UK"
);

INSERT INTO `customers` (
    `CustomerName`,
    `ContactName`,
    `Address`,
    `City`,
    `PostalCode`,
    `Country`
) VALUES (
    "Berglunds snabbköp",
    "Christina Berglund",
    "Berguvsvägen 8",
    "Luleå",
    "S-958 22",
    "Sweden"
);

INSERT INTO `customers` (
    `CustomerName`,
    `ContactName`,
    `Address`,
    `City`,
    `PostalCode`,
    `Country`
) VALUES (
    "Blauer See Delikatessen",
    "Hanna Moos",
    "Forsterstr. 57",
    "Mannheim",
    "68306",
    "Germany"
);

INSERT INTO `customers` (
    `CustomerName`,
    `ContactName`,
    `Address`,
    `City`,
    `PostalCode`,
    `Country`
) VALUES (
    "Blondel père et fils",
    "Frédérique Citeaux",
    "24, place Kléber",
    "Strasbourg",
    "67000",
    "France"
);

INSERT INTO `customers` (
    `CustomerName`,
    `ContactName`,
    `Address`,
    `City`,
    `PostalCode`,
    `Country`
) VALUES (
    "Bólido Comidas preparadas",
    "Martín Sommer",
    "C/ Araquil, 67",
    "Madrid",
    "28023",
    "Spain"
);

INSERT INTO `customers` (
    `CustomerName`,
    `ContactName`,
    `Address`,
    `City`,
    `PostalCode`,
    `Country`
) VALUES (
    "Bon app'",
    "Laurence Lebihans",
    "12, rue des Bouchers",
    "Marseille",
    "13008",
    "France"
);

INSERT INTO `customers` (
    `CustomerName`,
    `ContactName`,
    `Address`,
    `City`,
    `PostalCode`,
    `Country`
) VALUES (
    "Bottom-Dollar Marketse",
    "Elizabeth Lincoln",
    "23 Tsawassen Blvd.",
    "Tsawassen",
    "T2F 8M4",
    "Canada"
);

ALTER TABLE `customers`
    CHANGE COLUMN `CustomerID` `Id` INT;

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
    `OrderID` INT PRIMARY KEY,
    `CustomerID` INT,
    `EmployeeID` INT, 
    `OrderDate` VARCHAR(30),
    `ShipperID` INT
);

INSERT INTO `orders` (`OrderID`, `CustomerID`, `EmployeeID`, `OrderDate`, `ShipperID`) VALUES (
    10248, 
    2,
    5,
    "1996-07-04",
    3
);

INSERT INTO `orders` (`OrderID`, `CustomerID`, `EmployeeID`, `OrderDate`, `ShipperID`) VALUES (
    10249, 
    1,
    6,
    "1996-07-05",
    1
);

INSERT INTO `orders` (`OrderID`, `CustomerID`, `EmployeeID`, `OrderDate`, `ShipperID`) VALUES (
    10250, 
    3,
    4,
    "1996-07-08",
    2
);

INSERT INTO `orders` (`OrderID`, `CustomerID`, `EmployeeID`, `OrderDate`, `ShipperID`) VALUES (
    10251, 
    2,
    3,
    "1996-07-08",
    1
);

INSERT INTO `orders` (`OrderID`, `CustomerID`, `EmployeeID`, `OrderDate`, `ShipperID`) VALUES (
    10252, 
    1,
    4,
    "1996-07-09",
    2
);

INSERT INTO `orders` (`OrderID`, `CustomerID`, `EmployeeID`, `OrderDate`, `ShipperID`) VALUES (
    10253, 
    4,
    3,
    "1996-07-10",
    2
);

INSERT INTO `orders` (`OrderID`, `CustomerID`, `EmployeeID`, `OrderDate`, `ShipperID`) VALUES (
    10254, 
    5,
    5,
    "1996-07-11",
    2
);

INSERT INTO `orders` (`OrderID`, `CustomerID`, `EmployeeID`, `OrderDate`, `ShipperID`) VALUES (
    10255, 
    6,
    9,
    "1996-07-12",
    3
);

INSERT INTO `orders` (`OrderID`, `CustomerID`, `EmployeeID`, `OrderDate`, `ShipperID`) VALUES (
    10256, 
    7,
    3,
    "1996-07-15",
    2
);

INSERT INTO `orders` (`OrderID`, `CustomerID`, `EmployeeID`, `OrderDate`, `ShipperID`) VALUES (
    10257, 
    9,
    4,
    "1996-07-16",
    3
);
