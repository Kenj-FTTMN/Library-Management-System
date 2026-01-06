-- Sample Data Insertion Script
-- Library Management System
-- This script inserts sample data for testing and demonstration purposes
--
-- IMPORTANT NOTES:
-- 1. This script assumes there are existing users in the users table
-- 2. It references user_id values 1-5. Make sure these users exist before running.
-- 3. For proper department analysis, ensure:
--    - user_id 1, 2, 3 belong to department_id 1 (Computer Science)
--    - user_id 4 belongs to department_id 3 (Business Administration)
--    - user_id 5 belongs to department_id 5 (Natural Sciences)
-- 4. Do NOT run this script if you already have data in these tables (it will cause duplicates)
-- 5. To avoid foreign key errors, ensure users and roles tables have data first

-- ============================================
-- 1. INSERT 10 AUTHORS
-- ============================================
INSERT INTO `author` (`author_name`) VALUES
('J.K. Rowling'),
('George R.R. Martin'),
('Stephen King'),
('Jane Austen'),
('Ernest Hemingway'),
('Mark Twain'),
('Charles Dickens'),
('Agatha Christie'),
('Isaac Asimov'),
('Harper Lee');

-- ============================================
-- 2. INSERT 5 CATEGORIES
-- ============================================
INSERT INTO `categories` (`category_name`) VALUES
('Fiction'),
('Science Fiction'),
('Mystery'),
('History'),
('Biography');

-- ============================================
-- 3. INSERT 5 DEPARTMENTS
-- ============================================
INSERT INTO `department` (`department_name`) VALUES
('Computer Science'),
('Engineering'),
('Business Administration'),
('Arts and Humanities'),
('Natural Sciences');

-- ============================================
-- 4. INSERT 20 BOOKS
-- ============================================
-- Note: Assuming author_id 1-10 and category_id 1-5 exist
INSERT INTO `books` (`title`, `author_id`, `category_id`, `isbn`, `quantity`, `created_at`) VALUES
-- Most borrowed book (will be borrowed multiple times)
('Introduction to Programming', 1, 1, '978-0123456789', 5, NOW()),
('Advanced Database Systems', 1, 1, '978-0123456790', 3, NOW()),
('Web Development Fundamentals', 2, 1, '978-0123456791', 4, NOW()),
('Data Structures and Algorithms', 2, 1, '978-0123456792', 5, NOW()),
('Software Engineering Principles', 3, 1, '978-0123456793', 3, NOW()),
-- Books for different categories
('The Martian Chronicles', 9, 2, '978-0123456794', 2, NOW()),
('Foundation Series', 9, 2, '978-0123456795', 4, NOW()),
('Murder on the Orient Express', 8, 3, '978-0123456796', 3, NOW()),
('The ABC Murders', 8, 3, '978-0123456797', 2, NOW()),
('World War II: A Complete History', 4, 4, '978-0123456798', 3, NOW()),
('Ancient Civilizations', 5, 4, '978-0123456799', 2, NOW()),
('Einstein: His Life and Universe', 6, 5, '978-0123456800', 3, NOW()),
('Steve Jobs Biography', 7, 5, '978-0123456801', 2, NOW()),
-- More books for variety
('Machine Learning Basics', 3, 1, '978-0123456802', 4, NOW()),
('Cloud Computing Essentials', 4, 1, '978-0123456803', 3, NOW()),
('Cybersecurity Fundamentals', 5, 1, '978-0123456804', 3, NOW()),
('Mobile App Development', 6, 1, '978-0123456805', 4, NOW()),
('Artificial Intelligence Guide', 7, 1, '978-0123456806', 3, NOW()),
('Network Administration', 8, 1, '978-0123456807', 2, NOW()),
-- Least borrowed book (will be borrowed only once)
('Rare Technical Manual', 10, 1, '978-0123456808', 1, NOW());

-- ============================================
-- 5. INSERT 20 BORROW RECORDS
-- ============================================
-- Note: Assuming user_id 1-5 exist and book_id 1-20 exist
-- Structure: 
-- - Book 1 (most borrowed) will be borrowed 8 times
-- - Books 2-6 will be borrowed 2 times each (10 total)
-- - Book 20 (least borrowed) will be borrowed 1 time
-- - Remaining books: 1 time each

-- Department 1 (Computer Science) - borrows more (8 records)
-- Department 2 (Engineering) - borrows moderately (5 records)
-- Department 3 (Business Admin) - borrows less (4 records)
-- Department 4 (Arts) - borrows less (2 records)
-- Department 5 (Natural Sciences) - borrows less (1 record)

-- Most borrowed book (book_id = 1) - 7 borrows
INSERT INTO `borrow_records` (`user_id`, `book_id`, `borrow_date`, `due_date`, `status`) VALUES
-- Book 1 borrowed multiple times (most borrowed)
(1, 1, DATE_SUB(CURDATE(), INTERVAL 30 DAY), DATE_SUB(CURDATE(), INTERVAL 23 DAY), 'Returned'),
(1, 1, DATE_SUB(CURDATE(), INTERVAL 20 DAY), DATE_SUB(CURDATE(), INTERVAL 13 DAY), 'Returned'),
(2, 1, DATE_SUB(CURDATE(), INTERVAL 15 DAY), DATE_SUB(CURDATE(), INTERVAL 8 DAY), 'Returned'),
(2, 1, DATE_SUB(CURDATE(), INTERVAL 10 DAY), DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'Returned'),
(3, 1, DATE_SUB(CURDATE(), INTERVAL 7 DAY), CURDATE(), 'Returned'),
(1, 1, DATE_SUB(CURDATE(), INTERVAL 5 DAY), DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'Returned'),
(2, 1, DATE_SUB(CURDATE(), INTERVAL 3 DAY), DATE_ADD(CURDATE(), INTERVAL 4 DAY), 'Returned'),

-- Other books borrowed multiple times
(1, 2, DATE_SUB(CURDATE(), INTERVAL 25 DAY), DATE_SUB(CURDATE(), INTERVAL 18 DAY), 'Returned'),
(4, 2, DATE_SUB(CURDATE(), INTERVAL 12 DAY), DATE_SUB(CURDATE(), INTERVAL 5 DAY), 'Returned'),

(2, 3, DATE_SUB(CURDATE(), INTERVAL 22 DAY), DATE_SUB(CURDATE(), INTERVAL 15 DAY), 'Returned'),
(3, 3, DATE_SUB(CURDATE(), INTERVAL 8 DAY), DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Returned'),

(1, 4, DATE_SUB(CURDATE(), INTERVAL 18 DAY), DATE_SUB(CURDATE(), INTERVAL 11 DAY), 'Returned'),
(4, 4, DATE_SUB(CURDATE(), INTERVAL 6 DAY), DATE_ADD(CURDATE(), INTERVAL 1 DAY), 'Pending'),

(2, 5, DATE_SUB(CURDATE(), INTERVAL 14 DAY), DATE_SUB(CURDATE(), INTERVAL 7 DAY), 'Returned'),
(5, 5, DATE_SUB(CURDATE(), INTERVAL 4 DAY), DATE_ADD(CURDATE(), INTERVAL 3 DAY), 'Pending'),

(3, 6, DATE_SUB(CURDATE(), INTERVAL 11 DAY), DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'Returned'),
(1, 6, DATE_SUB(CURDATE(), INTERVAL 2 DAY), DATE_ADD(CURDATE(), INTERVAL 5 DAY), 'Pending'),

-- Single borrows for other books
(4, 7, DATE_SUB(CURDATE(), INTERVAL 9 DAY), DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'Returned'),
(5, 8, DATE_SUB(CURDATE(), INTERVAL 6 DAY), DATE_ADD(CURDATE(), INTERVAL 1 DAY), 'Pending'),

-- Least borrowed book (book_id = 20)
(3, 20, DATE_SUB(CURDATE(), INTERVAL 13 DAY), DATE_SUB(CURDATE(), INTERVAL 6 DAY), 'Returned');

-- ============================================
-- 6. INSERT 15 RETURNS
-- ============================================
-- Note: Returns reference borrow_id from borrow_records using subqueries
-- This ensures we get the correct borrow_id regardless of AUTO_INCREMENT values
-- Only borrow_records with status 'Returned' should have returns
-- Department 1 (Computer Science) - returns on time (all returns before or on due_date)
-- Department 3 (Business Admin) - returns late (some returns after due_date)
-- Other departments - mixed

-- Returns for borrow_records (matching by user_id, book_id, and borrow_date)
-- Department 1 users (user_id 1,2,3) - return on time
INSERT INTO `returns` (`borrow_id`, `return_date`, `bookcondition`) VALUES
-- On-time returns (return_date <= due_date) - Department 1 (Computer Science)
-- user_id 1, book_id 1, borrowed 30 days ago: due_date = 23 days ago, return 24 days ago (1 day early = ON TIME)
((SELECT borrow_id FROM borrow_records WHERE user_id = 1 AND book_id = 1 AND borrow_date = DATE_SUB(CURDATE(), INTERVAL 30 DAY) LIMIT 1), DATE_SUB(CURDATE(), INTERVAL 24 DAY), 'Good'),
-- user_id 1, book_id 1, borrowed 20 days ago: due_date = 13 days ago, return 14 days ago (1 day early = ON TIME)
((SELECT borrow_id FROM borrow_records WHERE user_id = 1 AND book_id = 1 AND borrow_date = DATE_SUB(CURDATE(), INTERVAL 20 DAY) LIMIT 1), DATE_SUB(CURDATE(), INTERVAL 14 DAY), 'Good'),
-- user_id 2, book_id 1, borrowed 15 days ago: due_date = 8 days ago, return 9 days ago (1 day early = ON TIME)
((SELECT borrow_id FROM borrow_records WHERE user_id = 2 AND book_id = 1 AND borrow_date = DATE_SUB(CURDATE(), INTERVAL 15 DAY) LIMIT 1), DATE_SUB(CURDATE(), INTERVAL 9 DAY), 'Good'),
-- user_id 2, book_id 1, borrowed 10 days ago: due_date = 3 days ago, return 4 days ago (1 day early = ON TIME)
((SELECT borrow_id FROM borrow_records WHERE user_id = 2 AND book_id = 1 AND borrow_date = DATE_SUB(CURDATE(), INTERVAL 10 DAY) LIMIT 1), DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'Good'),
-- user_id 3, book_id 1, borrowed 7 days ago: due_date = today, return yesterday (1 day early = ON TIME)
((SELECT borrow_id FROM borrow_records WHERE user_id = 3 AND book_id = 1 AND borrow_date = DATE_SUB(CURDATE(), INTERVAL 7 DAY) LIMIT 1), DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Good'),
-- user_id 1, book_id 1, borrowed 5 days ago: due_date = 2 days from now, return yesterday (3 days early = ON TIME)
((SELECT borrow_id FROM borrow_records WHERE user_id = 1 AND book_id = 1 AND borrow_date = DATE_SUB(CURDATE(), INTERVAL 5 DAY) LIMIT 1), DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Good'),
-- user_id 2, book_id 1, borrowed 3 days ago: due_date = 4 days from now, return yesterday (5 days early = ON TIME)
((SELECT borrow_id FROM borrow_records WHERE user_id = 2 AND book_id = 1 AND borrow_date = DATE_SUB(CURDATE(), INTERVAL 3 DAY) LIMIT 1), DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Good'),

-- More on-time returns
-- user_id 1, book_id 2, borrowed 25 days ago: due_date = 18 days ago, return 19 days ago (1 day early = ON TIME)
((SELECT borrow_id FROM borrow_records WHERE user_id = 1 AND book_id = 2 AND borrow_date = DATE_SUB(CURDATE(), INTERVAL 25 DAY) LIMIT 1), DATE_SUB(CURDATE(), INTERVAL 19 DAY), 'Good'),
-- user_id 2, book_id 3, borrowed 22 days ago: due_date = 15 days ago, return 16 days ago (1 day early = ON TIME)
((SELECT borrow_id FROM borrow_records WHERE user_id = 2 AND book_id = 3 AND borrow_date = DATE_SUB(CURDATE(), INTERVAL 22 DAY) LIMIT 1), DATE_SUB(CURDATE(), INTERVAL 16 DAY), 'Good'),
-- user_id 1, book_id 4, borrowed 18 days ago: due_date = 11 days ago, return 12 days ago (1 day early = ON TIME)
((SELECT borrow_id FROM borrow_records WHERE user_id = 1 AND book_id = 4 AND borrow_date = DATE_SUB(CURDATE(), INTERVAL 18 DAY) LIMIT 1), DATE_SUB(CURDATE(), INTERVAL 12 DAY), 'Good'),
-- user_id 2, book_id 5, borrowed 14 days ago: due_date = 7 days ago, return 8 days ago (1 day early = ON TIME)
((SELECT borrow_id FROM borrow_records WHERE user_id = 2 AND book_id = 5 AND borrow_date = DATE_SUB(CURDATE(), INTERVAL 14 DAY) LIMIT 1), DATE_SUB(CURDATE(), INTERVAL 8 DAY), 'Good'),
-- user_id 3, book_id 6, borrowed 11 days ago: due_date = 4 days ago, return 5 days ago (1 day early = ON TIME)
((SELECT borrow_id FROM borrow_records WHERE user_id = 3 AND book_id = 6 AND borrow_date = DATE_SUB(CURDATE(), INTERVAL 11 DAY) LIMIT 1), DATE_SUB(CURDATE(), INTERVAL 5 DAY), 'Good'),
-- user_id 4, book_id 7, borrowed 9 days ago: due_date = 2 days ago, return 3 days ago (1 day early = ON TIME)
((SELECT borrow_id FROM borrow_records WHERE user_id = 4 AND book_id = 7 AND borrow_date = DATE_SUB(CURDATE(), INTERVAL 9 DAY) LIMIT 1), DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'Good'),

-- Late returns (return_date > due_date) - Department 3 (Business Admin, user_id 4)
-- user_id 4, book_id 2, borrowed 12 days ago: due_date = 5 days ago, return 3 days ago (2 days LATE)
((SELECT borrow_id FROM borrow_records WHERE user_id = 4 AND book_id = 2 AND borrow_date = DATE_SUB(CURDATE(), INTERVAL 12 DAY) LIMIT 1), DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'Good'),
-- user_id 3, book_id 3, borrowed 8 days ago: due_date = 1 day ago, return today (1 day LATE, and damaged)
((SELECT borrow_id FROM borrow_records WHERE user_id = 3 AND book_id = 3 AND borrow_date = DATE_SUB(CURDATE(), INTERVAL 8 DAY) LIMIT 1), CURDATE(), 'Damaged'),

-- One more return
-- user_id 3, book_id 20, borrowed 13 days ago: due_date = 6 days ago, return 7 days ago (1 day early = ON TIME)
((SELECT borrow_id FROM borrow_records WHERE user_id = 3 AND book_id = 20 AND borrow_date = DATE_SUB(CURDATE(), INTERVAL 13 DAY) LIMIT 1), DATE_SUB(CURDATE(), INTERVAL 7 DAY), 'Good');

-- ============================================
-- 7. INSERT 5 FINES
-- ============================================
-- Note: Fines reference borrow_id from borrow_records using subqueries
-- Fines are typically for late returns or damaged books
INSERT INTO `fines` (`borrow_id`, `fine_amount`, `status`) VALUES
-- Fines for late returns
-- user_id 4, book_id 2, borrowed 12 days ago: Late return (2 days late), paid
((SELECT borrow_id FROM borrow_records WHERE user_id = 4 AND book_id = 2 AND borrow_date = DATE_SUB(CURDATE(), INTERVAL 12 DAY) LIMIT 1), 50.00, 'paid'),
-- user_id 3, book_id 3, borrowed 8 days ago: Late return (1 day late) with damage, unpaid
((SELECT borrow_id FROM borrow_records WHERE user_id = 3 AND book_id = 3 AND borrow_date = DATE_SUB(CURDATE(), INTERVAL 8 DAY) LIMIT 1), 100.00, 'unpaid'),

-- Fines for other scenarios (minor fines for various reasons)
-- user_id 1, book_id 1, borrowed 30 days ago: Minor fine, paid
((SELECT borrow_id FROM borrow_records WHERE user_id = 1 AND book_id = 1 AND borrow_date = DATE_SUB(CURDATE(), INTERVAL 30 DAY) LIMIT 1), 25.00, 'paid'),
-- user_id 3, book_id 1, borrowed 7 days ago: Unpaid fine
((SELECT borrow_id FROM borrow_records WHERE user_id = 3 AND book_id = 1 AND borrow_date = DATE_SUB(CURDATE(), INTERVAL 7 DAY) LIMIT 1), 30.00, 'unpaid'),
-- user_id 2, book_id 5, borrowed 14 days ago: Paid fine
((SELECT borrow_id FROM borrow_records WHERE user_id = 2 AND book_id = 5 AND borrow_date = DATE_SUB(CURDATE(), INTERVAL 14 DAY) LIMIT 1), 40.00, 'paid');

-- ============================================
-- SUMMARY OF DATA PATTERNS:
-- ============================================
-- Most Borrowed Book: book_id = 1 "Introduction to Programming" (7 borrows)
-- Least Borrowed Book: book_id = 20 "Rare Technical Manual" (1 borrow)
-- 
-- Department that Borrows More: Department 1 (Computer Science) - 14 borrows
--   (Assuming user_id 1, 2, 3 belong to Department 1)
-- Department that Borrows Less: Department 5 (Natural Sciences) - 2 borrows
--   (Assuming user_id 5 belongs to Department 5)
--
-- Department that Returns On Time: Department 1 (Computer Science) - all returns on time
--   (Users from this department consistently return books before due date)
-- Department that Returns Late: Department 3 (Business Administration) - has late returns
--   (Assuming user_id 4 belongs to Department 3, has 2 late returns)
-- ============================================

