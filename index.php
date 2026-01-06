<?php
/**
 * Main Index File with GET Parameter Routing
 * Single entry point for all pages
 */

// Load configuration
require_once 'config/config.php';
require_once 'config/auth.php';

// Handle login/logout pages separately (they don't need header/footer)
if (isset($_GET['page']) && ($_GET['page'] === 'login' || $_GET['page'] === 'logout')) {
    if ($_GET['page'] === 'login') {
        include 'pages/login.php';
    } elseif ($_GET['page'] === 'logout') {
        include 'pages/logout.php';
    }
    exit();
}

// Get page parameter or default to 'home'
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Sanitize page parameter
$page = preg_replace('/[^a-z0-9\-]/', '', strtolower($page));

// Require login for all pages except login
if ($page !== 'login' && !isLoggedIn()) {
    header('Location: index.php?page=login');
    exit();
}

// Get page configuration
$current_page = ($page === 'home') ? 'index' : $page;
$page_config = get_page_config($current_page);
$page_title = $page_config['title'];
$page_description = $page_config['description'];
$page_body_class = $page_config['body_class'];

// Set breadcrumbs if not home page
if ($page !== 'home') {
    $breadcrumbs = [
        ['title' => 'Home', 'url' => 'index.php'],
        ['title' => ucwords(str_replace('-', ' ', $page)), 'url' => '']
    ];
}

// Include head section
include 'includes/head.php';

// Include header
include 'includes/header.php';
?>

<main class="main">

  <?php 
  // Include breadcrumbs if not home page
  if (isset($breadcrumbs)) {
    include 'includes/breadcrumbs.php';
  }
  ?>

  <section class="section">
    <div class="row">
      <div class="col-lg-12">
        <?php 
        // Route to appropriate page content
        if (isset($_GET['page'])) {
          $page = $_GET['page'];
          switch($page) {
            // Library Management Pages
            case 'books':
              include 'pages/books.php';
              break;
            case 'authors':
              include 'pages/authors.php';
              break;
            case 'categories':
              include 'pages/categories.php';
              break;
            case 'users':
              include 'pages/users.php';
              break;
            case 'borrow':
              include 'pages/borrow.php';
              break;
            case 'returns':
              include 'pages/returns.php';
              break;
            case 'fines':
              include 'pages/fines.php';
              break;
            case 'departments':
              include 'pages/departments.php';
              break;
            case 'roles':
              include 'pages/roles.php';
              break;
            case 'admin-dashboard':
              include 'pages/admin-dashboard.php';
              break;
            case 'librarian-dashboard':
              include 'pages/librarian-dashboard.php';
              break;
            case 'faculty-dashboard':
              include 'pages/faculty-dashboard.php';
              break;
            case 'student-dashboard':
              include 'pages/student-dashboard.php';
              break;
            case 'unauthorized':
              include 'pages/unauthorized.php';
              break;
            case 'contact':
              include 'pages/contact.php';
              break;
            case 'privacy':
              include 'pages/privacy.php';
              break;
            case 'terms-of-service':
              include 'pages/terms-of-service.php';
              break;
            case 'export':
              include 'pages/export.php';
              break;
            case '404':
            case 'error':
              include 'pages/404.php';
              break;
            default:
              // If page not found, show 404
              include 'pages/404.php';
              break;
          }
        } else {
          // Default home page content
          include 'pages/home.php';
        }
        ?>
      </div>
    </div>
  </section>

</main>

<?php
// Include footer
include 'includes/footer.php';

// Include scripts
include 'includes/scripts.php';
?>
