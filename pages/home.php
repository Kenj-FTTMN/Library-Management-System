<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

// Require login
requireLogin();

// Redirect to role-specific dashboard
$role = getCurrentRole();
if ($role === ROLE_ADMIN) {
    header('Location: index.php?page=admin-dashboard');
    exit();
} elseif ($role === ROLE_FACULTY) {
    header('Location: index.php?page=faculty-dashboard');
    exit();
} elseif ($role === ROLE_STUDENT) {
    header('Location: index.php?page=student-dashboard');
    exit();
}

$conn = getDBConnection();

// Get statistics
$stats = [];

// Total Books
$result = $conn->query("SELECT COUNT(*) as total FROM books");
$stats['books'] = $result->fetch_assoc()['total'];

// Total Users
$result = $conn->query("SELECT COUNT(*) as total FROM users");
$stats['users'] = $result->fetch_assoc()['total'];

// Total Authors
$result = $conn->query("SELECT COUNT(*) as total FROM author");
$stats['authors'] = $result->fetch_assoc()['total'];

// Total Categories
$result = $conn->query("SELECT COUNT(*) as total FROM categories");
$stats['categories'] = $result->fetch_assoc()['total'];

// Pending Borrows
$result = $conn->query("SELECT COUNT(*) as total FROM borrow_records WHERE status = 'Pending'");
$stats['pending_borrows'] = $result->fetch_assoc()['total'];

// Unpaid Fines
$result = $conn->query("SELECT COUNT(*) as total FROM fines WHERE status = 'unpaid'");
$stats['unpaid_fines'] = $result->fetch_assoc()['total'];

// Total Borrow Records
$result = $conn->query("SELECT COUNT(*) as total FROM borrow_records");
$stats['total_borrows'] = $result->fetch_assoc()['total'];

// Total Returns
$result = $conn->query("SELECT COUNT(*) as total FROM returns");
$stats['returns'] = $result->fetch_assoc()['total'];
?>

<!-- Hero Section -->
<section id="hero" class="hero section">
  <div class="hero-wrapper">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-12 hero-content text-center" data-aos="fade-up" data-aos-delay="100">
          <h1>Library Management System</h1>
          <p>Manage your library efficiently with our comprehensive management system</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Dashboard Stats Section -->
<section id="dashboard" class="section">
  <div class="container">
    <h2 class="text-center mb-5" data-aos="fade-down" data-aos-delay="50">Dashboard Overview</h2>
    
    <div class="row gy-4">
      <!-- Books Card -->
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100" data-aos-anchor-placement="top-bottom">
        <div class="card text-center h-100 dashboard-card" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
          <div class="card-body">
            <div class="feature-icon mb-3" style="animation: bounceIn 1s ease-out;">
              <i class="bi bi-book-fill" style="font-size: 3rem; color: #0d6efd;"></i>
            </div>
            <h3 class="card-title counter" data-target="<?php echo $stats['books']; ?>">0</h3>
            <p class="card-text">Total Books</p>
            <a href="index.php?page=books" class="btn btn-primary">Manage Books</a>
          </div>
        </div>
      </div>

      <!-- Users Card -->
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200" data-aos-anchor-placement="top-bottom">
        <div class="card text-center h-100 dashboard-card" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
          <div class="card-body">
            <div class="feature-icon mb-3" style="animation: bounceIn 1s ease-out 0.1s both;">
              <i class="bi bi-people-fill" style="font-size: 3rem; color: #198754;"></i>
            </div>
            <h3 class="card-title counter" data-target="<?php echo $stats['users']; ?>">0</h3>
            <p class="card-text">Total Users</p>
            <a href="index.php?page=users" class="btn btn-success">Manage Users</a>
          </div>
        </div>
      </div>

      <!-- Authors Card -->
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300" data-aos-anchor-placement="top-bottom">
        <div class="card text-center h-100 dashboard-card" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
          <div class="card-body">
            <div class="feature-icon mb-3" style="animation: bounceIn 1s ease-out 0.2s both;">
              <i class="bi bi-person-badge-fill" style="font-size: 3rem; color: #ffc107;"></i>
            </div>
            <h3 class="card-title counter" data-target="<?php echo $stats['authors']; ?>">0</h3>
            <p class="card-text">Total Authors</p>
            <a href="index.php?page=authors" class="btn btn-warning">Manage Authors</a>
          </div>
        </div>
      </div>

      <!-- Categories Card -->
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400" data-aos-anchor-placement="top-bottom">
        <div class="card text-center h-100 dashboard-card" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
          <div class="card-body">
            <div class="feature-icon mb-3" style="animation: bounceIn 1s ease-out 0.3s both;">
              <i class="bi bi-tags-fill" style="font-size: 3rem; color: #dc3545;"></i>
            </div>
            <h3 class="card-title counter" data-target="<?php echo $stats['categories']; ?>">0</h3>
            <p class="card-text">Total Categories</p>
            <a href="index.php?page=categories" class="btn btn-danger">Manage Categories</a>
          </div>
        </div>
      </div>

      <!-- Pending Borrows Card -->
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="500" data-aos-anchor-placement="top-bottom">
        <div class="card text-center h-100 dashboard-card" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
          <div class="card-body">
            <div class="feature-icon mb-3" style="animation: bounceIn 1s ease-out 0.4s both;">
              <i class="bi bi-clock-history" style="font-size: 3rem; color: #0dcaf0;"></i>
            </div>
            <h3 class="card-title counter" data-target="<?php echo $stats['pending_borrows']; ?>">0</h3>
            <p class="card-text">Pending Borrows</p>
            <a href="index.php?page=borrow" class="btn btn-info">View Borrows</a>
          </div>
        </div>
      </div>

      <!-- Unpaid Fines Card -->
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="600" data-aos-anchor-placement="top-bottom">
        <div class="card text-center h-100 dashboard-card" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
          <div class="card-body">
            <div class="feature-icon mb-3" style="animation: bounceIn 1s ease-out 0.5s both;">
              <i class="bi bi-exclamation-triangle-fill" style="font-size: 3rem; color: #fd7e14;"></i>
            </div>
            <h3 class="card-title counter" data-target="<?php echo $stats['unpaid_fines']; ?>">0</h3>
            <p class="card-text">Unpaid Fines</p>
            <a href="index.php?page=fines" class="btn btn-warning">Manage Fines</a>
          </div>
        </div>
      </div>

      <!-- Total Borrows Card -->
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="700" data-aos-anchor-placement="top-bottom">
        <div class="card text-center h-100 dashboard-card" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
          <div class="card-body">
            <div class="feature-icon mb-3" style="animation: bounceIn 1s ease-out 0.6s both;">
              <i class="bi bi-journal-text" style="font-size: 3rem; color: #6f42c1;"></i>
            </div>
            <h3 class="card-title counter" data-target="<?php echo $stats['total_borrows']; ?>">0</h3>
            <p class="card-text">Total Borrow Records</p>
            <a href="index.php?page=borrow" class="btn btn-primary">View All</a>
          </div>
        </div>
      </div>

      <!-- Returns Card -->
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="800" data-aos-anchor-placement="top-bottom">
        <div class="card text-center h-100 dashboard-card" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
          <div class="card-body">
            <div class="feature-icon mb-3" style="animation: bounceIn 1s ease-out 0.7s both;">
              <i class="bi bi-arrow-return-left" style="font-size: 3rem; color: #20c997;"></i>
            </div>
            <h3 class="card-title counter" data-target="<?php echo $stats['returns']; ?>">0</h3>
            <p class="card-text">Total Returns</p>
            <a href="index.php?page=returns" class="btn btn-success">Manage Returns</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- Quick Actions Section -->
<section id="quick-actions" class="section bg-light">
  <div class="container">
    <h2 class="text-center mb-5">Quick Actions</h2>
    
    <div class="row gy-4">
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="card h-100">
          <div class="card-body text-center">
            <i class="bi bi-plus-circle-fill" style="font-size: 3rem; color: #0d6efd;"></i>
            <h4 class="mt-3">Add New Book</h4>
            <p class="text-muted">Add a new book to the library collection</p>
            <a href="index.php?page=books" class="btn btn-primary">Go to Books</a>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
        <div class="card h-100">
          <div class="card-body text-center">
            <i class="bi bi-person-plus-fill" style="font-size: 3rem; color: #198754;"></i>
            <h4 class="mt-3">Register New User</h4>
            <p class="text-muted">Add a new user to the system</p>
            <a href="index.php?page=users" class="btn btn-success">Go to Users</a>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
        <div class="card h-100">
          <div class="card-body text-center">
            <i class="bi bi-book-half" style="font-size: 3rem; color: #ffc107;"></i>
            <h4 class="mt-3">Record Borrow</h4>
            <p class="text-muted">Record a new book borrowing</p>
            <a href="index.php?page=borrow" class="btn btn-warning">Go to Borrows</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
