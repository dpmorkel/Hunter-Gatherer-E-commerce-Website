-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 16, 2018 at 09:29 PM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 7.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hunter_gatherer_db`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `calcTotalPrice` ()  BEGIN

CREATE TEMPORARY TABLE tmp 
AS 
SELECT SUM(product.price) AS totalP, cart.cartID 
FROM cart 
LEFT JOIN cart_details ON cart.cartID = cart_details.cartID 
LEFT JOIN product ON cart_details.productID = product.productID 
GROUP BY cart.cartID;

UPDATE tmp
SET tmp.totalP = IFNULL(tmp.totalP, 0);

UPDATE cart
INNER JOIN tmp
ON cart.cartID = tmp.cartID
SET cart.totalPrice = tmp.totalP
WHERE cart.datePaid IS NULL;

DROP TEMPORARY TABLE IF EXISTS tmp;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `checkout` (IN `outCartID` INT(11), IN `outCustomerID` INT(11))  BEGIN

DECLARE newCartID INT(11);

UPDATE cart 
SET cart.datePaid = NOW()
WHERE cart.cartID = outCartID;

DELETE t1.*
FROM cart_details t1
INNER JOIN (
SELECT cart_details.productID
FROM cart_details
GROUP BY cart_details.productID
HAVING COUNT(*)>1) t2
ON t1.productID = t2.productID
AND t1.cartID <> outCartID;

INSERT INTO cart (cart.customerID)
VALUES (outCustomerID);

SELECT cart.cartID 
INTO newCartID 
FROM cart 
WHERE cart.customerID = outCustomerID 
AND cart.datePaid IS NULL;

SELECT newCartID;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `register` (IN `regEmail` VARCHAR(255), IN `regPassword` VARCHAR(255), IN `regFirstName` VARCHAR(255), IN `regLastName` VARCHAR(255), IN `regNewsletter` TINYINT(1))  BEGIN

	DECLARE regID INT(11);

	INSERT INTO user (
        user.email,
        user.password
    )
    VALUES (
        regEmail,
        regPassword
    );

    SELECT user.userID INTO regID FROM user WHERE user.email = regEmail;
    
    INSERT INTO customer (
        customer.customerID,
        customer.firstName,
        customer.lastName,
        customer.newsletter
    )
    VALUES (
        regID,
        regFirstName,
        regLastName,
        regNewsletter
    );
    
    INSERT INTO cart (
        cart.customerID
    )
    VALUES (
        regID
    );
    
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `adminID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`adminID`) VALUES
(7);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cartID` int(11) NOT NULL,
  `customerID` int(11) NOT NULL,
  `totalPrice` decimal(7,2) NOT NULL DEFAULT '0.00',
  `datePaid` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cartID`, `customerID`, `totalPrice`, `datePaid`) VALUES
(25, 13, '420.00', '2018-10-16 19:17:36'),
(26, 14, '770.00', '2018-10-16 19:18:11'),
(27, 15, '410.00', '2018-10-16 19:16:54'),
(28, 15, '0.00', NULL),
(29, 13, '0.00', NULL),
(30, 14, '0.00', NULL),
(31, 16, '0.00', NULL);

--
-- Triggers `cart`
--
DELIMITER $$
CREATE TRIGGER `availability` AFTER UPDATE ON `cart` FOR EACH ROW BEGIN

IF NEW.datePaid IS NOT NULL THEN

UPDATE product 
LEFT JOIN cart_details
ON product.productID = cart_details.productID
LEFT JOIN cart
ON cart_details.cartID = cart.cartID
SET product.available = 0
WHERE cart.datePaid IS NOT NULL;

UPDATE collection
LEFT JOIN
(SELECT product.collectionID, SUM(product.available) AS tot
 FROM product
 GROUP BY product.collectionID
 ) tmp
ON collection.collectionID = tmp.collectionID
SET collection.active = 0
WHERE tot = 0;

END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `cart_details`
--

CREATE TABLE `cart_details` (
  `productID` int(11) NOT NULL,
  `cartID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cart_details`
--

INSERT INTO `cart_details` (`productID`, `cartID`) VALUES
(10, 27),
(15, 27),
(26, 25),
(28, 25),
(36, 26),
(38, 26),
(43, 26);

--
-- Triggers `cart_details`
--
DELIMITER $$
CREATE TRIGGER `calcTotalPriceDelete` AFTER DELETE ON `cart_details` FOR EACH ROW BEGIN

CALL calcTotalPrice;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `calcTotalPriceInsert` AFTER INSERT ON `cart_details` FOR EACH ROW BEGIN

CALL calcTotalPrice;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `collection`
--

CREATE TABLE `collection` (
  `collectionID` int(11) NOT NULL,
  `adminID` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `flavourText` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `collection`
--

INSERT INTO `collection` (`collectionID`, `adminID`, `name`, `flavourText`, `image`, `active`) VALUES
(8, 7, 'Everything is Never Enough', 'Hello everyone again, I am so happy to be back.  Tami, our guest thrifta hails from the Garden route and has been proudly raiding her grandmother\'s closet since 1994.  She is a freelance graphic designer by day, dreamer by night and full time hoarder of all things crimpolene.  \'Everything is never enough\' is the happy by-product of 2 months spent scouring everything Hospice shop from Mosselbay to Knysna, and LOVING IT.', '../Images/Everything is Never Enough.png', 1),
(9, 7, 'Souvenirs', 'Hello lovelies. SO THIS IS WEIRD. This album is ONLY for those living in Hanoi, Vietnam.  As much as it sucks seeing things you can\'t have, you may need to just wait a little longer.  I wanted to post this on the page for a number of reasons, but take a look anyway if you are back in SA!  Also, if you know friends in Hanoi, Vietnam, please do spread the word.', '../Images/Souvenirs.png', 1),
(10, 7, 'Fluorescent Adolescent', 'HELLO everyone.  So we went on a sourcing trip as you can see, and found some pretty rare and beautiful garments for you all - hope you find something you love.  I wish I could keep them all, but they need new loving homes!', '../Images/Fluorescent Adolescent.png\r\n', 1);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customerID` int(11) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `newsletter` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customerID`, `firstName`, `lastName`, `newsletter`) VALUES
(13, 'Dreyer', 'Morkel', 0),
(14, 'Helene', 'Haarhoff', 0),
(15, 'Jane', 'Doe', 1),
(16, 'Ryan', 'Ford', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `productID` int(11) NOT NULL,
  `collectionID` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `keyword` enum('Blouse','Dress','Jeans','Shorts','Skirt','Top') NOT NULL,
  `price` decimal(6,2) NOT NULL,
  `size` enum('XS','S','M','L','XL') NOT NULL,
  `image` varchar(255) NOT NULL,
  `available` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`productID`, `collectionID`, `name`, `keyword`, `price`, `size`, `image`, `available`) VALUES
(9, 8, 'pleated cream wool midi skirt', 'Skirt', '220.00', 'S', '../Images/pleated cream wool midi skirt.png', 1),
(10, 8, 'genuine leather cut-out cami', 'Top', '250.00', 'M', '../Images/genuine leather cut-out cami.png', 0),
(11, 8, 'high-waisted elasticated denims', 'Jeans', '190.00', 'M', '../Images/high-waisted elasticated denims.png', 1),
(12, 8, 'sheer shirt with navy trim', 'Blouse', '170.00', 'L', '../Images/sheer shirt with navy trim.png', 1),
(13, 8, 'coral sateen floral embroidered vest', 'Blouse', '130.00', 'S', '../Images/coral sateen floral embroidered vest.png', 1),
(14, 8, 'mesh and stripe crop', 'Top', '100.00', 'M', '../Images/mesh and stripe crop.png', 1),
(15, 8, 'red knitted mini skirt', 'Skirt', '160.00', 'S', '../Images/red knitted mini skirt.png', 0),
(16, 8, 'calvin klein jeans', 'Jeans', '330.00', 'M', '../Images/calvin klein jeans.png', 1),
(17, 8, '70s shirt with blue buttons', 'Blouse', '270.00', 'M', '../Images/70s shirt with blue buttons.png', 1),
(18, 8, 'blue sheer lined skirt', 'Skirt', '170.00', 'M', '../Images/blue sheer lined skirt.png', 1),
(19, 8, 'gucci white denims with anchor embroidered pocket', 'Jeans', '470.00', 'XS', '../Images/gucci white denims with anchor embroidered pocket.png', 1),
(20, 8, 'bottle green pleated midi', 'Skirt', '170.00', 'L', '../Images/bottle green pleated midi.png', 1),
(21, 9, 'tucan applique and embroidered t-shirt', 'Top', '170.00', 'M', '../Images/tucan applique and embroidered t-shirt.png', 1),
(22, 9, 'genuine leather pencil skirt', 'Skirt', '290.00', 'M', '../Images/genuine leather pencil skirt.png', 1),
(23, 9, 'polo deep cut boyfriend jeans', 'Jeans', '330.00', 'S', '../Images/polo deep cut boyfriend jeans.png', 1),
(24, 9, 'high-waisted maroon wool skirt', 'Skirt', '190.00', 'M', '../Images/high-waisted maroon wool skirt.png', 1),
(25, 9, 'new york t-shirt', 'Top', '150.00', 'S', '../Images/new york t-shirt.png', 1),
(26, 9, '70s roses printed shirt', 'Blouse', '170.00', 'M', '../Images/70s roses printed shirt.png', 0),
(27, 9, 'black sheer dress', 'Dress', '170.00', 'L', '../Images/black sheer dress.png', 1),
(28, 9, 'pastel pink 90s prom dress', 'Dress', '250.00', 'S', '../Images/pastel pink 90s prom dress.png', 0),
(29, 9, 'high-waisted deep pocket denims', 'Jeans', '290.00', 'M', '../Images/high-waisted deep pocket denims.png', 1),
(30, 9, '90s gold buckle cocktail dress', 'Dress', '240.00', 'S', '../Images/90s gold buckle cocktail dress.png', 1),
(31, 9, 'yellow shirt with passants', 'Blouse', '170.00', 'L', '../Images/yellow shirt with passants.png', 1),
(32, 9, 'roses babydoll scoop neck dress', 'Dress', '160.00', 'L', '../Images/roses babydoll scoop neck dress.png', 1),
(33, 10, 'turtleneck knit', 'Top', '190.00', 'L', '../Images/turtleneck knit.png', 1),
(34, 10, 'high-waisted wool shorts', 'Shorts', '190.00', 'S', '../Images/high-waisted wool shorts.png', 1),
(35, 10, 'pink cut-out seam short sleeve', 'Blouse', '170.00', 'L', '../Images/pink cut-out seam short sleeve.png', 1),
(36, 10, 'faded benetton slim fit', 'Jeans', '330.00', 'S', '../Images/faded benetton slim fit.png', 0),
(37, 10, 'white detailed collar with detachable pussybow', 'Blouse', '170.00', 'L', '../Images/white detailed collar with detachable pussybow.png', 1),
(38, 10, 'polo rainbow jersey', 'Top', '250.00', 'L', '../Images/polo rainbow jersey.png', 0),
(39, 10, '90s denim babydoll button up', 'Dress', '230.00', 'M', '../Images/90s denim babydoll button up.png', 1),
(40, 10, 'grey pinstripe jeans', 'Jeans', '190.00', 'XS', '../Images/grey pinstripe jeans.png', 1),
(41, 10, 'sheer yellow pleated blouse', 'Blouse', '110.00', 'M', '../Images/sheer yellow pleated blouse.png', 1),
(42, 10, '90s pastel grafitti maxi dress', 'Dress', '190.00', 'M', '../Images/90s pastel grafitti maxi dress.png', 1),
(43, 10, '70s semi-sheer print shirt', 'Blouse', '190.00', 'M', '../Images/70s semi-sheer print shirt.png', 0),
(44, 10, 'high-waisted white button up levis', 'Jeans', '350.00', 'XS', '../Images/high-waisted white button up levis.png', 1);

-- --------------------------------------------------------

--
-- Stand-in structure for view `subview1`
-- (See below for the actual view)
--
CREATE TABLE `subview1` (
`customerID` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `subview2`
-- (See below for the actual view)
--
CREATE TABLE `subview2` (
`passiveCustomers` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userID` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userID`, `email`, `password`) VALUES
(7, 'naucamp@gmail.com', 'naucamp'),
(13, 'dmorkel@gmail.com', 'dmorkel'),
(14, 'hhaarhoff@gmail.com', 'hhaarhoff'),
(15, 'jdoe@gmail.com', 'jdoe'),
(16, 'rford@gmail.com', 'rford');

-- --------------------------------------------------------

--
-- Stand-in structure for view `view1`
-- (See below for the actual view)
--
CREATE TABLE `view1` (
`passiveCustomers` bigint(21)
,`totalCustomers` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view2`
-- (See below for the actual view)
--
CREATE TABLE `view2` (
`firstName` varchar(255)
,`lastName` varchar(255)
,`totalSpend` decimal(29,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view3`
-- (See below for the actual view)
--
CREATE TABLE `view3` (
`keyword` enum('Blouse','Dress','Jeans','Shorts','Skirt','Top')
,`totalSales` bigint(21)
);

-- --------------------------------------------------------

--
-- Structure for view `subview1`
--
DROP TABLE IF EXISTS `subview1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `subview1`  AS  select `cart`.`customerID` AS `customerID` from `cart` group by `cart`.`customerID` having (count(`cart`.`customerID`) > 1) ;

-- --------------------------------------------------------

--
-- Structure for view `subview2`
--
DROP TABLE IF EXISTS `subview2`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `subview2`  AS  select count(0) AS `passiveCustomers` from `subview1` ;

-- --------------------------------------------------------

--
-- Structure for view `view1`
--
DROP TABLE IF EXISTS `view1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view1`  AS  select `subview2`.`passiveCustomers` AS `passiveCustomers`,count(`customer`.`customerID`) AS `totalCustomers` from (`subview2` join `customer`) ;

-- --------------------------------------------------------

--
-- Structure for view `view2`
--
DROP TABLE IF EXISTS `view2`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view2`  AS  select `customer`.`firstName` AS `firstName`,`customer`.`lastName` AS `lastName`,sum(`cart`.`totalPrice`) AS `totalSpend` from (`customer` join `cart`) where (`customer`.`customerID` = `cart`.`customerID`) group by `cart`.`customerID` order by sum(`cart`.`totalPrice`) desc limit 5 ;

-- --------------------------------------------------------

--
-- Structure for view `view3`
--
DROP TABLE IF EXISTS `view3`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view3`  AS  select `product`.`keyword` AS `keyword`,ifnull(count(0),0) AS `totalSales` from `product` where (`product`.`available` = 0) group by `product`.`keyword` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`adminID`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cartID`),
  ADD KEY `customerID` (`customerID`);

--
-- Indexes for table `cart_details`
--
ALTER TABLE `cart_details`
  ADD PRIMARY KEY (`productID`,`cartID`),
  ADD KEY `cartID` (`cartID`);

--
-- Indexes for table `collection`
--
ALTER TABLE `collection`
  ADD PRIMARY KEY (`collectionID`),
  ADD KEY `adminID` (`adminID`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customerID`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`productID`),
  ADD KEY `collectionID` (`collectionID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cartID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `collection`
--
ALTER TABLE `collection`
  MODIFY `collectionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `productID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`adminID`) REFERENCES `user` (`userID`);

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`customerID`) REFERENCES `customer` (`customerID`);

--
-- Constraints for table `cart_details`
--
ALTER TABLE `cart_details`
  ADD CONSTRAINT `cart_details_ibfk_1` FOREIGN KEY (`productID`) REFERENCES `product` (`productID`),
  ADD CONSTRAINT `cart_details_ibfk_2` FOREIGN KEY (`cartID`) REFERENCES `cart` (`cartID`);

--
-- Constraints for table `collection`
--
ALTER TABLE `collection`
  ADD CONSTRAINT `collection_ibfk_1` FOREIGN KEY (`adminID`) REFERENCES `admin` (`adminID`);

--
-- Constraints for table `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `customer_ibfk_1` FOREIGN KEY (`customerID`) REFERENCES `user` (`userID`);

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`collectionID`) REFERENCES `collection` (`collectionID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
