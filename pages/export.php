<?php
// ABSOLUTELY NO OUTPUT BEFORE THIS FILE

// --- Include database and auth ---
require_once 'C:/xampp/htdocs/Library-Management-System/config/database.php';
require_once 'C:/xampp/htdocs/Library-Management-System/config/auth_core.php';

// Only Admin & Librarian can export
if (!canManage()) {
    http_response_code(403);
    exit('Unauthorized access. Only Admin or Librarian can export.');
}

// --- Get export type ---
$type = $_GET['type'] ?? '';

$conn = getDBConnection();

// --- Define export configurations ---
$exports = [

    // Borrow Records
    'borrows' => [
        'filename' => 'borrow_records.csv',
        'headers' => ['Borrow ID', 'User ID', 'Book ID', 'Borrow Date', 'Due Date', 'Status'],
        'query' => "
            SELECT borrow_id, user_id, book_id, borrow_date, due_date, status
            FROM borrow_records
            ORDER BY borrow_id
        "
    ],

    // Users
    'users' => [
        'filename' => 'users.csv',
        'headers' => ['User ID', 'First Name', 'Last Name', 'Email', 'Role'],
        'query' => "
            SELECT user_id, first_name, last_name, email, role
            FROM users
            ORDER BY user_id
        "
    ],

    // Books
    'books' => [
        'filename' => 'books.csv',
        'headers' => ['Book ID', 'Title', 'Author ID', 'Category', 'ISBN', 'Quantity', 'Created At'],
        'query' => "
            SELECT book_id, title, author_id, category_id, isbn, quantity, created_at
            FROM books
            ORDER BY book_id
        "
    ],

    // Returns
    'returns' => [
        'filename' => 'returns.csv',
        'headers' => ['Return ID', 'Borrow ID', 'Return Date', 'Book Condition'],
        'query' => "
            SELECT return_id, borrow_id, return_date, bookcondition
            FROM returns
            ORDER BY return_id
        "
    ],
    // Fines
    'fines' => [
        'filename' => 'fines.csv',
        'headers' => ['Fine ID', 'Borrow ID', 'Fine Amount',  'Paid Status'],
        'query' => "
            SELECT fine_id, borrow_id, fine_amount, status
            FROM fines
            ORDER BY fine_id
        "
    ],
];

// --- Validate export type ---
if (!array_key_exists($type, $exports)) {
    http_response_code(400);
    exit('Invalid export type.');
}

// --- CSV headers (MUST COME FIRST) ---
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $exports[$type]['filename']);

$output = fopen('php://output', 'w');

// Write CSV column headers
fputcsv($output, $exports[$type]['headers']);

// Execute query and write rows
$result = $conn->query($exports[$type]['query']);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

fclose($output);
exit;
