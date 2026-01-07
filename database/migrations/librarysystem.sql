-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 06, 2026 at 06:39 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `librarysystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `author`
--

CREATE TABLE `author` (
  `author_id` int(11) NOT NULL,
  `author_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `author`
--

INSERT INTO `author` (`author_id`, `author_name`) VALUES
(1, 'J.K. Rowling'),
(2, 'George R.R. Martin'),
(3, 'Stephen King'),
(4, 'Jane Austen'),
(5, 'Ernest Hemingway'),
(6, 'Mark Twain'),
(7, 'Charles Dickens'),
(8, 'Agatha Christie'),
(9, 'Isaac Asimov'),
(10, 'Harper Lee');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `author_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `isbn` varchar(50) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `title`, `author_id`, `category_id`, `isbn`, `quantity`, `created_at`) VALUES
(1, 'Introduction to Programming', 1, 1, '978-0123456789', 5, '2026-01-06 23:03:13'),
(2, 'Advanced Database Systems', 1, 1, '978-0123456790', 3, '2026-01-06 23:03:13'),
(3, 'Web Development Fundamentals', 2, 1, '978-0123456791', 4, '2026-01-06 23:03:13'),
(4, 'Data Structures and Algorithms', 2, 1, '978-0123456792', 5, '2026-01-06 23:03:13'),
(5, 'Software Engineering Principles', 3, 1, '978-0123456793', 3, '2026-01-06 23:03:13'),
(6, 'The Martian Chronicles', 9, 2, '978-0123456794', 2, '2026-01-06 23:03:13'),
(7, 'Foundation Series', 9, 2, '978-0123456795', 4, '2026-01-06 23:03:13'),
(8, 'Murder on the Orient Express', 8, 3, '978-0123456796', 3, '2026-01-06 23:03:13'),
(9, 'The ABC Murders', 8, 3, '978-0123456797', 2, '2026-01-06 23:03:13'),
(10, 'World War II: A Complete History', 4, 4, '978-0123456798', 3, '2026-01-06 23:03:13'),
(11, 'Ancient Civilizations', 5, 4, '978-0123456799', 2, '2026-01-06 23:03:13'),
(12, 'Einstein: His Life and Universe', 6, 5, '978-0123456800', 3, '2026-01-06 23:03:13'),
(13, 'Steve Jobs Biography', 7, 5, '978-0123456801', 2, '2026-01-06 23:03:13'),
(14, 'Machine Learning Basics', 3, 1, '978-0123456802', 4, '2026-01-06 23:03:13'),
(15, 'Cloud Computing Essentials', 4, 1, '978-0123456803', 3, '2026-01-06 23:03:13'),
(16, 'Cybersecurity Fundamentals', 5, 1, '978-0123456804', 3, '2026-01-06 23:03:13'),
(17, 'Mobile App Development', 6, 1, '978-0123456805', 4, '2026-01-06 23:03:13'),
(18, 'Artificial Intelligence Guide', 7, 1, '978-0123456806', 3, '2026-01-06 23:03:13'),
(19, 'Network Administration', 8, 1, '978-0123456807', 2, '2026-01-06 23:03:13'),
(20, 'Rare Technical Manual', 10, 1, '978-0123456808', 1, '2026-01-06 23:03:13');

-- --------------------------------------------------------

--
-- Table structure for table `borrow_records`
--

CREATE TABLE `borrow_records` (
  `borrow_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `book_id` int(11) DEFAULT NULL,
  `borrow_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('Pending','Returned') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrow_records`
--

INSERT INTO `borrow_records` (`borrow_id`, `user_id`, `book_id`, `borrow_date`, `due_date`, `status`) VALUES
(1, 6, 19, '2026-01-06', '2026-01-20', 'Returned'),
(2, 6, 9, '2026-01-06', '2026-01-20', 'Returned');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'Fiction'),
(2, 'Science Fiction'),
(3, 'Mystery'),
(4, 'History'),
(5, 'Biography');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`department_id`, `department_name`) VALUES
(1, 'Computer Science'),
(2, 'Engineering'),
(3, 'Business Administration'),
(4, 'Arts and Humanities'),
(5, 'Natural Sciences'),
(6, 'Mathematics'),
(7, 'Library Administration'),
(8, 'Nursing');

-- --------------------------------------------------------

--
-- Table structure for table `fines`
--

CREATE TABLE `fines` (
  `fine_id` int(11) NOT NULL,
  `borrow_id` int(11) DEFAULT NULL,
  `fine_amount` decimal(10,0) DEFAULT NULL,
  `status` enum('paid','unpaid') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fines`
--

INSERT INTO `fines` (`fine_id`, `borrow_id`, `fine_amount`, `status`) VALUES
(1, 2, 100, 'unpaid');

-- --------------------------------------------------------

--
-- Table structure for table `returns`
--

CREATE TABLE `returns` (
  `return_id` int(11) NOT NULL,
  `borrow_id` int(11) DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `bookcondition` enum('Good','Damaged') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `returns`
--

INSERT INTO `returns` (`return_id`, `borrow_id`, `return_date`, `bookcondition`) VALUES
(1, 1, '2026-01-14', 'Good'),
(2, 2, '2026-01-06', 'Damaged');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'Faculty'),
(3, 'Student'),
(4, 'Librarian');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(50) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `password`, `role_id`, `department_id`, `created_at`) VALUES
(1, 'John', 'Administrator', 'admin@library.com', 'admin123', 1, 3, '2026-01-05 19:36:28'),
(2, 'Sarah', 'Professor', 'faculty@library.com', 'faculty123', 2, 1, '2026-01-05 19:36:28'),
(3, 'Mike', 'Student', 'student@library.com', 'student123', 3, 1, '2026-01-05 19:36:28'),
(4, 'Library', 'Librarian', 'librarian@library.com', 'librarian123', 4, 3, '2026-01-05 22:04:21'),
(5, 'Trix', 'Mifrano', 'trix@gmail.com', 'password123', 3, 3, '2026-01-06 23:43:20'),
(6, 'Kenji', 'Lumayog', 'kenji@gmail.com', 'password123', 3, 6, '2026-01-06 23:43:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `author`
--
ALTER TABLE `author`
  ADD PRIMARY KEY (`author_id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `borrow_records`
--
ALTER TABLE `borrow_records`
  ADD PRIMARY KEY (`borrow_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `fines`
--
ALTER TABLE `fines`
  ADD PRIMARY KEY (`fine_id`),
  ADD KEY `borrow_id` (`borrow_id`);

--
-- Indexes for table `returns`
--
ALTER TABLE `returns`
  ADD PRIMARY KEY (`return_id`),
  ADD KEY `borrow_id` (`borrow_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `department_id` (`department_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `author`
--
ALTER TABLE `author`
  MODIFY `author_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `borrow_records`
--
ALTER TABLE `borrow_records`
  MODIFY `borrow_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `fines`
--
ALTER TABLE `fines`
  MODIFY `fine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `returns`
--
ALTER TABLE `returns`
  MODIFY `return_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `author` (`author_id`),
  ADD CONSTRAINT `books_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `borrow_records`
--
ALTER TABLE `borrow_records`
  ADD CONSTRAINT `borrow_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `borrow_records_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`);

--
-- Constraints for table `fines`
--
ALTER TABLE `fines`
  ADD CONSTRAINT `fines_ibfk_1` FOREIGN KEY (`borrow_id`) REFERENCES `borrow_records` (`borrow_id`);

--
-- Constraints for table `returns`
--
ALTER TABLE `returns`
  ADD CONSTRAINT `returns_ibfk_1` FOREIGN KEY (`borrow_id`) REFERENCES `borrow_records` (`borrow_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
