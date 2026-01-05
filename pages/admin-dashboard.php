<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireAdmin();

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

$user_data = getUserData();
?>

<!-- Admin Dashboard -->
<section id="admin-dashboard" class="section">
  <div class="container">
    <!-- Welcome Section -->
    <div class="row mb-4" data-aos="fade-down">
      <div class="col-12">
        <div class="card border-primary">
          <div class="card-body">
            <h2 class="mb-3">
              <i class="bi bi-shield-check text-primary"></i> 
              Welcome, <?php echo htmlspecialchars($user_data['first_name'] ?? 'Admin'); ?>!
            </h2>
            <p class="lead text-muted">You have full administrative access to manage the library system.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Dashboard Stats -->
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

      <!-- Pending Borrows Card -->
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300" data-aos-anchor-placement="top-bottom">
        <div class="card text-center h-100 dashboard-card" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
          <div class="card-body">
            <div class="feature-icon mb-3" style="animation: bounceIn 1s ease-out 0.2s both;">
              <i class="bi bi-clock-history" style="font-size: 3rem; color: #0dcaf0;"></i>
            </div>
            <h3 class="card-title counter" data-target="<?php echo $stats['pending_borrows']; ?>">0</h3>
            <p class="card-text">Pending Borrows</p>
            <a href="index.php?page=borrow" class="btn btn-info">View Borrows</a>
          </div>
        </div>
      </div>

      <!-- Unpaid Fines Card -->
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400" data-aos-anchor-placement="top-bottom">
        <div class="card text-center h-100 dashboard-card" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
          <div class="card-body">
            <div class="feature-icon mb-3" style="animation: bounceIn 1s ease-out 0.3s both;">
              <i class="bi bi-exclamation-triangle-fill" style="font-size: 3rem; color: #fd7e14;"></i>
            </div>
            <h3 class="card-title counter" data-target="<?php echo $stats['unpaid_fines']; ?>">0</h3>
            <p class="card-text">Unpaid Fines</p>
            <a href="index.php?page=fines" class="btn btn-warning">Manage Fines</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-5">
      <div class="col-12">
        <h3 class="mb-4" data-aos="fade-up">Quick Actions</h3>
        <div class="row gy-3">
          <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
            <a href="index.php?page=books" class="btn btn-outline-primary w-100">
              <i class="bi bi-plus-circle"></i> Add New Book
            </a>
          </div>
          <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
            <a href="index.php?page=users" class="btn btn-outline-success w-100">
              <i class="bi bi-person-plus"></i> Add New User
            </a>
          </div>
          <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
            <a href="index.php?page=borrow" class="btn btn-outline-info w-100">
              <i class="bi bi-journal-plus"></i> Record Borrow
            </a>
          </div>
          <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
            <a href="index.php?page=returns" class="btn btn-outline-secondary w-100">
              <i class="bi bi-arrow-return-left"></i> Process Return
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

