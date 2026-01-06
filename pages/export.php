<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

// Allow only admin or librarian to export
requireAdminOrLibrarian();

$conn = getDBConnection();

$type = $_GET['type'] ?? '';
$type = preg_replace('/[^a-z_]/', '', strtolower($type));

if ($type === '') {
    http_response_code(400);
    echo 'Missing export type.';
    exit();
}

// Configure export based on type
$filename = 'export_' . $type . '_' . date('Ymd_His') . '.csv';
$headers = [];
$sql = '';

switch ($type) {
    case 'books':
        $headers = ['Book ID', 'Title', 'ISBN', 'Publication Year', 'Category ID', 'Author ID', 'Created At'];
        $sql = "SELECT book_id, title, isbn, publication_year, category_id, author_id, created_at FROM books ORDER BY book_id";
        break;
    case 'users':
        // Only admins can export users
        if (!isAdmin()) {
            http_response_code(403);
            echo 'You do not have permission to export users.';
            exit();
        }
        $headers = ['User ID', 'First Name', 'Last Name', 'Email', 'Role ID', 'Department ID', 'Created At'];
        $sql = "SELECT user_id, first_name, last_name, email, role_id, department_id, created_at FROM users ORDER BY user_id";
        break;
    case 'borrows':
        $headers = ['Borrow ID', 'User ID', 'Book ID', 'Borrow Date', 'Due Date', 'Status'];
        $sql = "SELECT borrow_id, user_id, book_id, borrow_date, due_date, status FROM borrow_records ORDER BY borrow_id";
        break;
    case 'returns':
        $headers = ['Return ID', 'Borrow ID', 'Return Date', 'Condition Notes'];
        $sql = "SELECT return_id, borrow_id, return_date, condition_notes FROM returns ORDER BY return_id";
        break;
    case 'fines':
        $headers = ['Fine ID', 'Borrow ID', 'Amount', 'Status', 'Created At'];
        $sql = "SELECT fine_id, borrow_id, amount, status, created_at FROM fines ORDER BY fine_id";
        break;
    default:
        http_response_code(400);
        echo 'Invalid export type.';
        exit();
}

$result = $conn->query($sql);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
fputcsv($output, $headers);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, array_values($row));
    }
}

fclose($output);
exit();



