<?php
/**
 * Main Index File with GET Parameter Routing
 * Single entry point for all pages
 */

// Load configuration
require_once 'config/config.php';

// Get page parameter or default to 'home'
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Sanitize page parameter
$page = preg_replace('/[^a-z0-9\-]/', '', strtolower($page));

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
            case 'about':
              include 'pages/about.php';
              break;
            case 'contact':
              include 'pages/contact.php';
              break;
            case 'academics':
              include 'pages/academics.php';
              break;
            case 'admissions':
              include 'pages/admissions.php';
              break;
            case 'alumni':
              include 'pages/alumni.php';
              break;
            case 'events':
              include 'pages/events.php';
              break;
            case 'news':
              include 'pages/news.php';
              break;
            case 'students-life':
              include 'pages/students-life.php';
              break;
            case 'faculty-staff':
              include 'pages/faculty-staff.php';
              break;
            case 'campus-facilities':
              include 'pages/campus-facilities.php';
              break;
            case 'event-details':
              include 'pages/event-details.php';
              break;
            case 'news-details':
              include 'pages/news-details.php';
              break;
            case 'privacy':
              include 'pages/privacy.php';
              break;
            case 'terms-of-service':
              include 'pages/terms-of-service.php';
              break;
            case '404':
            case 'error':
              include 'pages/404.php';
              break;
            case 'starter-page':
              include 'pages/starter-page.php';
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
