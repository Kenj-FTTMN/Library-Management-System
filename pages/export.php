<?php
// ABSOLUTELY NO OUTPUT BEFORE THIS FILE

// --- Include database and auth ---
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

// Only Admin & Librarian can export
if (!canManage()) {
    http_response_code(403);
    exit('Unauthorized access. Only Admin or Librarian can export.');
}

$conn = getDBConnection();

// --- Define all export datasets ---
$datasets = [
    'Books' => [
        'headers' => ['Book ID', 'Title', 'Author Name', 'Category Name', 'ISBN', 'Quantity', 'Created At'],
        'query' => "
            SELECT b.book_id, b.title, 
                   COALESCE(a.author_name, 'N/A') as author_name,
                   COALESCE(c.category_name, 'N/A') as category_name,
                   b.isbn, b.quantity, b.created_at
            FROM books b
            LEFT JOIN author a ON b.author_id = a.author_id
            LEFT JOIN categories c ON b.category_id = c.category_id
            ORDER BY b.book_id
        "
    ],
    'Users' => [
        'headers' => ['User ID', 'First Name', 'Last Name', 'Email', 'Role Name', 'Department Name', 'Created At'],
        'query' => "
            SELECT u.user_id, u.first_name, u.last_name, u.email,
                   COALESCE(r.role_name, 'N/A') as role_name,
                   COALESCE(d.department_name, 'N/A') as department_name,
                   u.created_at
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.role_id
            LEFT JOIN department d ON u.department_id = d.department_id
            ORDER BY u.user_id
        "
    ],
    'Borrow Records' => [
        'headers' => ['Borrow ID', 'User Name', 'Book Title', 'Borrow Date', 'Due Date', 'Status'],
        'query' => "
            SELECT br.borrow_id,
                   CONCAT(u.first_name, ' ', u.last_name) as user_name,
                   b.title as book_title,
                   br.borrow_date, br.due_date, br.status
            FROM borrow_records br
            LEFT JOIN users u ON br.user_id = u.user_id
            LEFT JOIN books b ON br.book_id = b.book_id
            ORDER BY br.borrow_id
        "
    ],
    'Returns' => [
        'headers' => ['Return ID', 'Borrow ID', 'User Name', 'Book Title', 'Return Date', 'Book Condition'],
        'query' => "
            SELECT r.return_id, r.borrow_id,
                   CONCAT(u.first_name, ' ', u.last_name) as user_name,
                   b.title as book_title,
                   r.return_date, r.bookcondition
            FROM returns r
            LEFT JOIN borrow_records br ON r.borrow_id = br.borrow_id
            LEFT JOIN users u ON br.user_id = u.user_id
            LEFT JOIN books b ON br.book_id = b.book_id
            ORDER BY r.return_id
        "
    ],
    'Fines' => [
        'headers' => ['Fine ID', 'Borrow ID', 'User Name', 'Book Title', 'Fine Amount', 'Status'],
        'query' => "
            SELECT f.fine_id, f.borrow_id,
                   CONCAT(u.first_name, ' ', u.last_name) as user_name,
                   b.title as book_title,
                   f.fine_amount, f.status
            FROM fines f
            LEFT JOIN borrow_records br ON f.borrow_id = br.borrow_id
            LEFT JOIN users u ON br.user_id = u.user_id
            LEFT JOIN books b ON br.book_id = b.book_id
            ORDER BY f.fine_id
        "
    ]
];

// Generate filename with timestamp
$filename = 'library_export_' . date('Y-m-d_His') . '.xls';

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');


// Create Excel file using XML Spreadsheet format (Excel 2003+ compatible)
$excel = '<?xml version="1.0"?>' . "\n";
$excel .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
$excel .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
$excel .= ' xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
$excel .= ' xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n";
$excel .= ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
$excel .= ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";
$excel .= '<DocumentProperties><Title>Library Management System Export</Title></DocumentProperties>' . "\n";
$excel .= '<Styles>' . "\n";
$excel .= '<Style ss:ID="Header"><Font ss:Bold="1"/><Interior ss:Color="#CCCCCC" ss:Pattern="Solid"/></Style>' . "\n";
$excel .= '</Styles>' . "\n";

// Process each dataset as a separate worksheet
foreach ($datasets as $sheetName => $dataset) {
    // Clean sheet name (Excel has restrictions)
    $cleanSheetName = substr(preg_replace('/[\\\\\/\?\*\[\]]/', '_', $sheetName), 0, 31);
    
    $excel .= '<Worksheet ss:Name="' . htmlspecialchars($cleanSheetName) . '">' . "\n";
    $excel .= '<Table>' . "\n";
    
    // Write headers with styling
    $excel .= '<Row>' . "\n";
    foreach ($dataset['headers'] as $header) {
        $excel .= '<Cell ss:StyleID="Header"><Data ss:Type="String">' . htmlspecialchars($header) . '</Data></Cell>' . "\n";
    }
    $excel .= '</Row>' . "\n";
    
    // Execute query and write data rows
    $result = $conn->query($dataset['query']);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $excel .= '<Row>' . "\n";
            foreach ($row as $cell) {
                // Determine cell type and value
                if ($cell === null || $cell === '') {
                    $cellValue = '';
                    $cellType = 'String';
                } elseif (is_numeric($cell) && !is_string($cell)) {
                    $cellValue = $cell;
                    $cellType = 'Number';
                } else {
                    $cellValue = htmlspecialchars($cell);
                    $cellType = 'String';
                }
                $excel .= '<Cell><Data ss:Type="' . $cellType . '">' . $cellValue . '</Data></Cell>' . "\n";
            }
            $excel .= '</Row>' . "\n";
        }
    }
    
    $excel .= '</Table>' . "\n";
    $excel .= '</Worksheet>' . "\n";
}

$excel .= '</Workbook>';

// Output the Excel file
echo $excel;
exit;
