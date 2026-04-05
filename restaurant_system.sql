-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 25, 2026 at 11:21 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `restaurant_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `total_orders` int(11) DEFAULT 0,
  `total_spent` decimal(10,2) DEFAULT 0.00,
  `last_visit` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT 0,
  `unit` varchar(20) DEFAULT 'units',
  `reorder_level` int(11) DEFAULT 10,
  `price_per_unit` decimal(10,2) DEFAULT 0.00,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `item_name`, `category`, `quantity`, `unit`, `reorder_level`, `price_per_unit`, `last_updated`) VALUES
(1, 'Chicken Breast', 'Meat', 50, 'kg', 10, 450.00, '2026-03-23 19:11:52'),
(2, 'Beef', 'Meat', 40, 'kg', 8, 550.00, '2026-03-23 19:11:52'),
(3, 'Lettuce', 'Vegetables', 30, 'kg', 5, 120.00, '2026-03-23 19:11:52'),
(4, 'Tomatoes', 'Vegetables', 25, 'kg', 5, 80.00, '2026-03-23 19:11:52'),
(5, 'Cheese', 'Dairy', 15, 'kg', 5, 600.00, '2026-03-23 19:11:52'),
(6, 'Flour', 'Dry Goods', 100, 'kg', 20, 80.00, '2026-03-23 19:11:52'),
(7, 'Cooking Oil', 'Dry Goods', 50, 'liters', 10, 250.00, '2026-03-23 19:11:52'),
(8, 'Soft Drinks', 'Beverages', 200, 'bottles', 50, 60.00, '2026-03-23 19:11:52'),
(9, 'Ice Cream', 'Frozen', 30, 'liters', 10, 300.00, '2026-03-23 19:11:52'),
(10, 'Bread', 'Bakery', 40, 'loaves', 10, 50.00, '2026-03-23 19:11:52'),
(11, 'Chicken Breast', 'Meat', 50, 'kg', 10, 450.00, '2026-03-24 09:11:37'),
(12, 'Beef', 'Meat', 40, 'kg', 8, 550.00, '2026-03-24 09:11:37'),
(13, 'Lettuce', 'Vegetables', 30, 'kg', 5, 120.00, '2026-03-24 09:11:37'),
(14, 'Tomatoes', 'Vegetables', 25, 'kg', 5, 80.00, '2026-03-24 09:11:37'),
(15, 'Cheese', 'Dairy', 15, 'kg', 5, 600.00, '2026-03-24 09:11:37');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `name`, `category`, `price`, `description`, `image`) VALUES
(1, 'Garlic Bread', 'Starter', 350.00, 'Toasted bread with garlic butter', 'garlic-bread.jpg'),
(2, 'Chicken Wings', 'Starter', 600.00, 'Crispy spicy wings', 'chicken-wings.jpg'),
(3, 'Beef Steak', 'Main Course', 1200.00, 'Grilled steak with fries', 'steak.jpg'),
(4, 'Margherita Pizza', 'Pizza', 1000.00, 'Classic cheese pizza', 'pizza.jpg'),
(5, 'Ice Cream Sundae', 'Dessert', 450.00, 'Vanilla ice cream with syrup', 'sundae.jpg'),
(6, 'BBQ Burger', 'Pizza', 700.00, 'juicy grilled beef patty with tangy BBQ sauce, cheese and crispy bacon', 'bbq.jpg'),
(7, 'Spaghetti Carbonara', 'Main Course', 900.00, 'Pasta with creamy sauce', 'carbonara.jpg'),
(8, 'Chicken Curry', 'Main Course', 850.00, 'Spicy curry with rice', 'chicken-curry.jpg'),
(9, 'Pepperoni pizza', 'Pizza', 1100.00, 'Pizza with pepperoni', 'pepperoni.jpg'),
(10, 'vegetable spring rolls', 'Starter', 250.00, 'Crunchy rolls filled with seasoned veggies', 'spring-rolls.jpg'),
(11, 'Vegetable stir-fry', 'Main Course', 500.00, 'Mixed veggies sautéed in soy-garlic sauce', 'tofu.jpg'),
(12, 'Veggie Burger', 'Pizza', 480.00, 'Plant-based patty with avocado and greens', 'veggie-burger.jpg'),
(13, 'Classic Beef Burger', 'Pizza', 500.00, 'Grilled beef patty with lettuce and tomato', 'beef-burger.jpg'),
(14, 'French fries', 'sides', 200.00, 'Crispy golden potato fries', 'fries.jpg'),
(15, 'Mashed potatoes', 'sides', 450.00, 'Creamy mashed potatoes with butter', 'mashed-potatoes.jpg'),
(16, 'Fruit salad', 'Dessert', 300.00, 'Fresh seasonal fruits with mint drizzle', 'fruit-salad.jpg'),
(17, 'Fresh juice', 'Beverages', 200.00, 'Mango, orange, or passion fruit', 'juice.jpg'),
(18, 'Soda', 'Beverages', 150.00, 'Coke, Fanta, Sprite', 'soda.jpg'),
(19, 'Coffee/Tea', 'Beverages', 220.00, 'Served hot with optional milk/sugar', 'coffee.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `menu_inventory`
--

CREATE TABLE `menu_inventory` (
  `id` int(11) NOT NULL,
  `menu_item_id` int(11) DEFAULT NULL,
  `inventory_item_id` int(11) DEFAULT NULL,
  `quantity_needed` decimal(10,2) DEFAULT 1.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `table_number` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `table_number`, `item_name`, `price`, `status`, `created_at`) VALUES
(1, 1, 'Grilled Chicken', 450.00, 'completed', '2026-03-24 09:39:36'),
(2, 2, 'Beef Burger', 350.00, 'pending', '2026-03-24 09:39:36'),
(3, 3, 'Caesar Salad', 280.00, 'pending', '2026-03-24 09:39:36'),
(4, 4, 'Pasta Carbonara', 380.00, 'completed', '2026-03-24 09:39:36'),
(5, 6, 'Spaghetti Carbonara', 900.00, 'completed', '2026-03-24 09:41:36'),
(6, 6, 'Ice Cream Sundae', 450.00, 'completed', '2026-03-24 09:41:41');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `table_number` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `total`, `payment_method`, `payment_time`, `table_number`) VALUES
(1, NULL, 1450.00, NULL, '2026-03-15 11:51:00', 1),
(2, NULL, 1800.00, '', '2026-03-15 14:26:03', 9),
(3, NULL, 450.00, 'Cash', '2026-03-15 15:15:09', 4),
(4, NULL, 1450.00, '', '2026-03-15 17:34:36', 1),
(5, NULL, 550.00, 'Cash', '2026-03-15 17:35:27', 8),
(6, NULL, 550.00, 'Cash', '2026-03-16 08:13:37', 8),
(7, NULL, 1450.00, 'Cash', '2026-03-16 08:14:27', 5),
(8, NULL, 570.00, 'Cash', '2026-03-16 09:57:55', 2),
(9, NULL, 570.00, 'Cash', '2026-03-16 10:13:07', 2),
(10, NULL, 570.00, 'Cash', '2026-03-16 10:13:17', 2),
(11, NULL, 570.00, 'Cash', '2026-03-16 10:13:22', 2),
(12, NULL, 150.00, 'Cash', '2026-03-18 19:13:19', 9),
(13, NULL, 150.00, 'Cash', '2026-03-18 19:19:07', 9),
(14, NULL, 150.00, 'Cash', '2026-03-18 19:19:12', 9),
(15, NULL, 0.00, '', '2026-03-19 12:45:22', 0),
(16, NULL, 0.00, '', '2026-03-21 08:44:57', 0),
(17, NULL, 0.00, '', '2026-03-21 09:00:40', 0),
(18, NULL, 0.00, '', '2026-03-21 09:00:42', 0);

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_tables`
--

CREATE TABLE `restaurant_tables` (
  `id` int(11) NOT NULL,
  `table_number` int(11) NOT NULL,
  `capacity` int(11) DEFAULT 4,
  `status` varchar(20) DEFAULT 'available',
  `current_order_id` int(11) DEFAULT NULL,
  `reserved_by` varchar(100) DEFAULT NULL,
  `reserved_time` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurant_tables`
--

INSERT INTO `restaurant_tables` (`id`, `table_number`, `capacity`, `status`, `current_order_id`, `reserved_by`, `reserved_time`, `created_at`) VALUES
(1, 1, 4, 'occupied', NULL, NULL, NULL, '2026-03-23 18:26:25'),
(2, 2, 4, 'available', NULL, NULL, NULL, '2026-03-23 18:26:25'),
(3, 3, 6, 'available', NULL, NULL, NULL, '2026-03-23 18:26:25'),
(4, 4, 2, 'available', NULL, NULL, NULL, '2026-03-23 18:26:25'),
(5, 5, 4, 'available', NULL, NULL, NULL, '2026-03-23 18:26:25'),
(6, 6, 8, 'available', NULL, NULL, NULL, '2026-03-23 18:26:25'),
(7, 7, 4, 'available', NULL, NULL, NULL, '2026-03-23 18:26:25'),
(8, 8, 2, 'reserved', NULL, NULL, NULL, '2026-03-23 18:26:25'),
(9, 9, 6, 'available', NULL, NULL, NULL, '2026-03-23 18:26:25'),
(10, 10, 4, 'available', NULL, NULL, NULL, '2026-03-23 18:26:25');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `name`, `email`, `role`, `password`) VALUES
(4, 'Waiter', 'waiter@gmail.com', 'Waiter', '1234'),
(5, 'Kitchen', 'kitchen@gmail.com', 'Kitchen', '1234'),
(6, 'Admin User', 'admin@grabs.com', 'Admin', 'admin123'),
(7, 'John Waiter', 'waiter@grabs.com', 'Waiter', 'waiter123'),
(8, 'Chef Mike', 'kitchen@grabs.com', 'Kitchen', 'kitchen123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_inventory`
--
ALTER TABLE `menu_inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_item_id` (`menu_item_id`),
  ADD KEY `inventory_item_id` (`inventory_item_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `table_number` (`table_number`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `menu_inventory`
--
ALTER TABLE `menu_inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menu_inventory`
--
ALTER TABLE `menu_inventory`
  ADD CONSTRAINT `menu_inventory_ibfk_1` FOREIGN KEY (`menu_item_id`) REFERENCES `menu` (`id`),
  ADD CONSTRAINT `menu_inventory_ibfk_2` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
