<?php
/**
 * Site Configuration
 * Central configuration file for the College website
 */

// Site Information
define('SITE_NAME', 'College');
define('SITE_URL', 'http://localhost/College');
define('SITE_EMAIL', 'info@example.com');
define('SITE_PHONE', '+1 5589 55488 55');
define('SITE_ADDRESS', 'A108 Adam Street, New York, NY 535022');

// Paths
define('BASE_PATH', dirname(__DIR__));
define('INCLUDES_PATH', BASE_PATH . '/includes');
define('ASSETS_PATH', 'assets');

// Page titles and descriptions
$page_config = [
    'index' => [
        'title' => 'Home - College Bootstrap Template',
        'description' => 'Inspiring Excellence Through Education',
        'body_class' => 'index-page'
    ],
    'about' => [
        'title' => 'About - College Bootstrap Template',
        'description' => 'Learn about our college',
        'body_class' => 'about-page'
    ],
    'contact' => [
        'title' => 'Contact - College Bootstrap Template',
        'description' => 'Get in touch with us',
        'body_class' => 'contact-page'
    ],
    'academics' => [
        'title' => 'Academics - College Bootstrap Template',
        'description' => 'Our academic programs',
        'body_class' => 'academics-page'
    ],
    'admissions' => [
        'title' => 'Admissions - College Bootstrap Template',
        'description' => 'Admission information',
        'body_class' => 'admissions-page'
    ],
    'alumni' => [
        'title' => 'Alumni - College Bootstrap Template',
        'description' => 'Alumni network',
        'body_class' => 'alumni-page'
    ],
    'campus-facilities' => [
        'title' => 'Campus & Facilities - College Bootstrap Template',
        'description' => 'Our campus facilities',
        'body_class' => 'campus-facilities-page'
    ],
    'events' => [
        'title' => 'Events - College Bootstrap Template',
        'description' => 'Upcoming events',
        'body_class' => 'events-page'
    ],
    'event-details' => [
        'title' => 'Event Details - College Bootstrap Template',
        'description' => 'Event information',
        'body_class' => 'event-details-page'
    ],
    'faculty-staff' => [
        'title' => 'Faculty & Staff - College Bootstrap Template',
        'description' => 'Meet our faculty and staff',
        'body_class' => 'faculty-staff-page'
    ],
    'news' => [
        'title' => 'News - College Bootstrap Template',
        'description' => 'Latest news and updates',
        'body_class' => 'news-page'
    ],
    'news-details' => [
        'title' => 'News Details - College Bootstrap Template',
        'description' => 'News article details',
        'body_class' => 'news-details-page'
    ],
    'privacy' => [
        'title' => 'Privacy Policy - College Bootstrap Template',
        'description' => 'Privacy policy',
        'body_class' => 'privacy-page'
    ],
    'students-life' => [
        'title' => 'Students Life - College Bootstrap Template',
        'description' => 'Student life at our college',
        'body_class' => 'students-life-page'
    ],
    'terms-of-service' => [
        'title' => 'Terms of Service - College Bootstrap Template',
        'description' => 'Terms of service',
        'body_class' => 'terms-of-service-page'
    ],
    '404' => [
        'title' => '404 - Page Not Found',
        'description' => 'Page not found',
        'body_class' => 'error-404-page'
    ],
    'starter-page' => [
        'title' => 'Starter Page - College Bootstrap Template',
        'description' => 'Starter page',
        'body_class' => 'starter-page'
    ]
];

// Helper function to get page config
function get_page_config($page) {
    global $page_config;
    return isset($page_config[$page]) ? $page_config[$page] : [
        'title' => SITE_NAME . ' - College Bootstrap Template',
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

// Navigation menu structure
$nav_menu = [
    [
        'title' => 'Home',
        'url' => 'index.php',
        'page' => 'home',
        'active' => false
    ],
    [
        'title' => 'About',
        'url' => 'index.php?page=about',
        'page' => 'about',
        'active' => false,
        'dropdown' => [
            ['title' => 'About Us', 'url' => 'index.php?page=about', 'page' => 'about'],
            ['title' => 'Admissions', 'url' => 'index.php?page=admissions', 'page' => 'admissions'],
            ['title' => 'Academics', 'url' => 'index.php?page=academics', 'page' => 'academics'],
            ['title' => 'Faculty & Staff', 'url' => 'index.php?page=faculty-staff', 'page' => 'faculty-staff'],
            ['title' => 'Campus & Facilities', 'url' => 'index.php?page=campus-facilities', 'page' => 'campus-facilities']
        ]
    ],
    [
        'title' => 'Students Life',
        'url' => 'index.php?page=students-life',
        'page' => 'students-life',
        'active' => false
    ],
    [
        'title' => 'News',
        'url' => 'index.php?page=news',
        'page' => 'news',
        'active' => false
    ],
    [
        'title' => 'Events',
        'url' => 'index.php?page=events',
        'page' => 'events',
        'active' => false
    ],
    [
        'title' => 'Alumni',
        'url' => 'index.php?page=alumni',
        'page' => 'alumni',
        'active' => false
    ],
    [
        'title' => 'More Pages',
        'url' => '#',
        'page' => '',
        'active' => false,
        'dropdown' => [
            ['title' => 'News Details', 'url' => 'index.php?page=news-details', 'page' => 'news-details'],
            ['title' => 'Event Details', 'url' => 'index.php?page=event-details', 'page' => 'event-details'],
            ['title' => 'Privacy', 'url' => 'index.php?page=privacy', 'page' => 'privacy'],
            ['title' => 'Terms of Service', 'url' => 'index.php?page=terms-of-service', 'page' => 'terms-of-service'],
            ['title' => 'Error 404', 'url' => 'index.php?page=404', 'page' => '404'],
            ['title' => 'Starter Page', 'url' => 'index.php?page=starter-page', 'page' => 'starter-page']
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

