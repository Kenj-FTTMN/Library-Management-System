<?php
/**
 * Site Configuration
 * Central configuration file for the College website
 */

// Site Information
define('SITE_NAME', 'Library Management System');
define('SITE_URL', 'http://localhost/Library-Management-System');
define('SITE_EMAIL', 'info@library.com');
define('SITE_PHONE', '+1 5589 55488 55');
define('SITE_ADDRESS', 'A108 Adam Street, New York, NY 535022');

// Paths
define('BASE_PATH', dirname(__DIR__));
define('INCLUDES_PATH', BASE_PATH . '/includes');

// Determine assets path dynamically from SITE_URL
// Fallback to relative path if SITE_URL parsing fails
$parsed_url = parse_url(SITE_URL);
if ($parsed_url && isset($parsed_url['path'])) {
    $base_path = rtrim($parsed_url['path'], '/');
    define('ASSETS_PATH', $base_path . '/assets');
} else {
    // Fallback: use relative path from document root
    $script_dir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
    $base_path = ($script_dir === '/' || $script_dir === '\\') ? '' : $script_dir;
    define('ASSETS_PATH', rtrim($base_path, '/') . '/assets');
}

// Include database connection
require_once __DIR__ . '/database.php';

// Include auth functions if available (for navigation menu)
if (file_exists(__DIR__ . '/auth.php')) {
    require_once __DIR__ . '/auth.php';
}

// Page titles and descriptions
$page_config = [
    'index' => [
        'title' => 'Home - Library Management System',
        'description' => 'Manage your library efficiently',
        'body_class' => 'index-page'
    ],
    'books' => [
        'title' => 'Books Management - Library Management System',
        'description' => 'Manage library books',
        'body_class' => 'books-page'
    ],
    'authors' => [
        'title' => 'Authors Management - Library Management System',
        'description' => 'Manage authors',
        'body_class' => 'authors-page'
    ],
    'categories' => [
        'title' => 'Categories Management - Library Management System',
        'description' => 'Manage book categories',
        'body_class' => 'categories-page'
    ],
    'users' => [
        'title' => 'Users Management - Library Management System',
        'description' => 'Manage library users',
        'body_class' => 'users-page'
    ],
    'borrow' => [
        'title' => 'Borrow Records - Library Management System',
        'description' => 'Manage book borrowings',
        'body_class' => 'borrow-page'
    ],
    'returns' => [
        'title' => 'Returns Management - Library Management System',
        'description' => 'Manage book returns',
        'body_class' => 'returns-page'
    ],
    'fines' => [
        'title' => 'Fines Management - Library Management System',
        'description' => 'Manage fines',
        'body_class' => 'fines-page'
    ],
    'departments' => [
        'title' => 'Departments Management - Library Management System',
        'description' => 'Manage departments',
        'body_class' => 'departments-page'
    ],
    'roles' => [
        'title' => 'Roles Management - Library Management System',
        'description' => 'Manage user roles',
        'body_class' => 'roles-page'
    ],
    'login' => [
        'title' => 'Login - Library Management System',
        'description' => 'Login to your account',
        'body_class' => 'login-page'
    ],
    'logout' => [
        'title' => 'Logout - Library Management System',
        'description' => 'Logout from system',
        'body_class' => 'logout-page'
    ],
    'unauthorized' => [
        'title' => 'Access Denied - Library Management System',
        'description' => 'You do not have permission',
        'body_class' => 'unauthorized-page'
    ],
    'admin-dashboard' => [
        'title' => 'Admin Dashboard - Library Management System',
        'description' => 'Administrative dashboard',
        'body_class' => 'admin-dashboard-page'
    ],
    'faculty-dashboard' => [
        'title' => 'Faculty Dashboard - Library Management System',
        'description' => 'Faculty dashboard',
        'body_class' => 'faculty-dashboard-page'
    ],
    'student-dashboard' => [
        'title' => 'Student Dashboard - Library Management System',
        'description' => 'Student dashboard',
        'body_class' => 'student-dashboard-page'
    ],
    'contact' => [
        'title' => 'Contact - Library Management System',
        'description' => 'Get in touch with us',
        'body_class' => 'contact-page'
    ],
    'privacy' => [
        'title' => 'Privacy Policy - Library Management System',
        'description' => 'Privacy policy',
        'body_class' => 'privacy-page'
    ],
    'terms-of-service' => [
        'title' => 'Terms of Service - Library Management System',
        'description' => 'Terms of service',
        'body_class' => 'terms-of-service-page'
    ],
    '404' => [
        'title' => '404 - Page Not Found',
        'description' => 'Page not found',
        'body_class' => 'error-404-page'
    ]
];

// Helper function to get page config
function get_page_config($page) {
    global $page_config;
    return isset($page_config[$page]) ? $page_config[$page] : [
        'title' => SITE_NAME,
        'description' => '',
        'body_class' => ''
    ];
}

// Helper function to get current page
function get_current_page() {
    if (isset($_GET['page'])) {
        return $_GET['page'];
    }
    return 'index'; // Default to home
}

// Navigation menu structure - role-based
$nav_menu = [
    [
        'title' => 'Home',
        'url' => 'index.php',
        'page' => 'home',
        'active' => false
    ],
    [
        'title' => 'Books',
        'url' => 'index.php?page=books',
        'page' => 'books',
        'active' => false
    ],
    [
        'title' => 'Management',
        'url' => '#',
        'page' => '',
        'active' => false,
        'require_admin' => true, // Only admins can see this
        'dropdown' => [
            ['title' => 'Authors', 'url' => 'index.php?page=authors', 'page' => 'authors', 'require_admin' => true],
            ['title' => 'Categories', 'url' => 'index.php?page=categories', 'page' => 'categories', 'require_admin' => true],
            ['title' => 'Users', 'url' => 'index.php?page=users', 'page' => 'users', 'require_admin' => true],
            ['title' => 'Departments', 'url' => 'index.php?page=departments', 'page' => 'departments', 'require_admin' => true],
            ['title' => 'Roles', 'url' => 'index.php?page=roles', 'page' => 'roles', 'require_admin' => true]
        ]
    ],
    [
        'title' => 'Transactions',
        'url' => '#',
        'page' => '',
        'active' => false,
        'dropdown' => [
            ['title' => 'Borrow Records', 'url' => 'index.php?page=borrow', 'page' => 'borrow'],
            ['title' => 'Returns', 'url' => 'index.php?page=returns', 'page' => 'returns'],
            ['title' => 'Fines', 'url' => 'index.php?page=fines', 'page' => 'fines']
        ]
    ],
    [
        'title' => 'Contact',
        'url' => 'index.php?page=contact',
        'page' => 'contact',
        'active' => false
    ]
];

// Helper function to set active menu item
function set_active_menu(&$menu, $current_page) {
    foreach ($menu as &$item) {
        $item['active'] = (isset($item['page']) && $item['page'] === $current_page);
        if (isset($item['dropdown'])) {
            foreach ($item['dropdown'] as &$subitem) {
                $subitem['active'] = (isset($subitem['page']) && $subitem['page'] === $current_page);
            }
        }
    }
}
?>

