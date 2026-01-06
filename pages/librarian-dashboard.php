<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireLibrarian();

$conn = getDBConnection();

// Get statistics
$stats = [];

// Total Books
$result = $conn->query("SELECT COUNT(*) as total FROM books");
$stats['books'] = $result->fetch_assoc()['total'];

// Total Users
$result = $conn->query("SELECT COUNT(*) as total FROM users");
$stats['users'] = $result->fetch_assoc()['total'];

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

// Most Borrowed Books (Top 5)
$result = $conn->query("
    SELECT b.title, COUNT(br.borrow_id) as borrow_count 
    FROM books b 
    LEFT JOIN borrow_records br ON b.book_id = br.book_id 
    GROUP BY b.book_id, b.title 
    ORDER BY borrow_count DESC 
    LIMIT 5
");
$most_borrowed = [];
while ($row = $result->fetch_assoc()) {
    $most_borrowed[] = $row;
}

// Borrowing Trends by Department
$result = $conn->query("
    SELECT d.department_name, COUNT(br.borrow_id) as borrow_count 
    FROM department d 
    LEFT JOIN users u ON d.department_id = u.department_id 
    LEFT JOIN borrow_records br ON u.user_id = br.user_id 
    GROUP BY d.department_id, d.department_name 
    ORDER BY borrow_count DESC
");
$dept_trends = [];
while ($row = $result->fetch_assoc()) {
    $dept_trends[] = $row;
}

// Late Returns Statistics
$result = $conn->query("
    SELECT COUNT(*) as total_late 
    FROM borrow_records br 
    LEFT JOIN returns r ON br.borrow_id = r.borrow_id 
    WHERE br.due_date < CURDATE() 
    AND (r.return_date IS NULL OR r.return_date > br.due_date)
");
$late_returns = $result->fetch_assoc()['total_late'];

$user_data = getUserData();
?>

<!-- Librarian Dashboard -->
<section id="librarian-dashboard" class="section">
  <div class="container">
    <!-- Welcome Section -->
    <div class="row mb-4" data-aos="fade-down">
      <div class="col-12">
        <div class="card border-info">
          <div class="card-body">
            <h2 class="mb-3">
              <i class="bi bi-book-half text-info"></i> 
              Welcome, <?php echo htmlspecialchars($user_data['first_name'] ?? 'Librarian'); ?>!
            </h2>
            <p class="lead text-muted">Manage books, categories, users, process borrows/returns, handle fines, and generate reports.</p>
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
            <p class="card-text mb-0">Total Books</p>
          </div>
        </div>
      </div>

      <!-- Categories Card -->
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="150" data-aos-anchor-placement="top-bottom">
        <div class="card text-center h-100 dashboard-card" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
          <div class="card-body">
            <div class="feature-icon mb-3" style="animation: bounceIn 1s ease-out 0.1s both;">
              <i class="bi bi-tags-fill" style="font-size: 3rem; color: #6f42c1;"></i>
            </div>
            <h3 class="card-title counter" data-target="<?php echo $stats['categories']; ?>">0</h3>
            <p class="card-text mb-0">Categories</p>
          </div>
        </div>
      </div>

      <!-- Users Card -->
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200" data-aos-anchor-placement="top-bottom">
        <div class="card text-center h-100 dashboard-card" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
          <div class="card-body">
            <div class="feature-icon mb-3" style="animation: bounceIn 1s ease-out 0.2s both;">
              <i class="bi bi-people-fill" style="font-size: 3rem; color: #198754;"></i>
            </div>
            <h3 class="card-title counter" data-target="<?php echo $stats['users']; ?>">0</h3>
            <p class="card-text mb-0">Total Users</p>
          </div>
        </div>
      </div>

      <!-- Pending Borrows Card -->
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="250" data-aos-anchor-placement="top-bottom">
        <div class="card text-center h-100 dashboard-card" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
          <div class="card-body">
            <div class="feature-icon mb-3" style="animation: bounceIn 1s ease-out 0.3s both;">
              <i class="bi bi-clock-history" style="font-size: 3rem; color: #0dcaf0;"></i>
            </div>
            <h3 class="card-title counter" data-target="<?php echo $stats['pending_borrows']; ?>">0</h3>
            <p class="card-text">Pending Borrows</p>
            <a href="index.php?page=borrow" class="btn btn-info">Process Borrows</a>
          </div>
        </div>
      </div>

      <!-- Unpaid Fines Card -->
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300" data-aos-anchor-placement="top-bottom">
        <div class="card text-center h-100 dashboard-card" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
          <div class="card-body">
            <div class="feature-icon mb-3" style="animation: bounceIn 1s ease-out 0.4s both;">
              <i class="bi bi-exclamation-triangle-fill" style="font-size: 3rem; color: #fd7e14;"></i>
            </div>
            <h3 class="card-title counter" data-target="<?php echo $stats['unpaid_fines']; ?>">0</h3>
            <p class="card-text">Unpaid Fines</p>
            <a href="index.php?page=fines" class="btn btn-warning">Manage Fines</a>
          </div>
        </div>
      </div>

      <!-- Returns Card -->
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="350" data-aos-anchor-placement="top-bottom">
        <div class="card text-center h-100 dashboard-card" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
          <div class="card-body">
            <div class="feature-icon mb-3" style="animation: bounceIn 1s ease-out 0.5s both;">
              <i class="bi bi-arrow-return-left" style="font-size: 3rem; color: #20c997;"></i>
            </div>
            <h3 class="card-title counter" data-target="<?php echo $stats['returns']; ?>">0</h3>
            <p class="card-text">Total Returns</p>
            <a href="index.php?page=returns" class="btn btn-success">Process Returns</a>
          </div>
        </div>
      </div>

      <!-- Late Returns Card -->
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400" data-aos-anchor-placement="top-bottom">
        <div class="card text-center h-100 dashboard-card" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
          <div class="card-body">
            <div class="feature-icon mb-3" style="animation: bounceIn 1s ease-out 0.6s both;">
              <i class="bi bi-calendar-x-fill" style="font-size: 3rem; color: #dc3545;"></i>
            </div>
            <h3 class="card-title counter" data-target="<?php echo $late_returns; ?>">0</h3>
            <p class="card-text">Late Returns</p>
            <a href="index.php?page=borrow" class="btn btn-danger">View Details</a>
          </div>
        </div>
      </div>

      <!-- Total Borrows Card -->
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="450" data-aos-anchor-placement="top-bottom">
        <div class="card text-center h-100 dashboard-card" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
          <div class="card-body">
            <div class="feature-icon mb-3" style="animation: bounceIn 1s ease-out 0.7s both;">
              <i class="bi bi-journal-text" style="font-size: 3rem; color: #0d6efd;"></i>
            </div>
            <h3 class="card-title counter" data-target="<?php echo $stats['total_borrows']; ?>">0</h3>
            <p class="card-text">Total Borrows</p>
            <a href="index.php?page=borrow" class="btn btn-primary">View All</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-5">
      <div class="col-12">
        <h3 class="mb-4" data-aos="fade-up">Quick Actions</h3>
        <div class="row gy-3">
          <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
            <a href="index.php?page=borrow" class="btn btn-outline-info w-100">
              <i class="bi bi-journal-plus"></i> Process Borrow
            </a>
          </div>
          <div class="col-md-3" data-aos="fade-up" data-aos-delay="500">
            <a href="index.php?page=returns" class="btn btn-outline-secondary w-100">
              <i class="bi bi-arrow-return-left"></i> Process Return
            </a>
          </div>
          <div class="col-md-3" data-aos="fade-up" data-aos-delay="600">
            <a href="index.php?page=fines" class="btn btn-outline-warning w-100">
              <i class="bi bi-cash-coin"></i> Manage Fines
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Reports & Analytics Section -->
    <div class="row mt-5">
      <div class="col-12">
        <h3 class="mb-4" data-aos="fade-up">Reports &amp; Analytics</h3>
      </div>
      
      <!-- Most Borrowed Books -->
      <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-graph-up"></i> Most Borrowed Books</h5>
          </div>
          <div class="card-body">
            <?php if (count($most_borrowed) > 0): ?>
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Rank</th>
                      <th>Book Title</th>
                      <th>Borrow Count</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($most_borrowed as $index => $book): ?>
                      <tr>
                        <td><strong>#<?php echo $index + 1; ?></strong></td>
                        <td><?php echo htmlspecialchars($book['title']); ?></td>
                        <td><span class="badge bg-primary"><?php echo $book['borrow_count']; ?></span></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <p class="text-muted">No borrowing data available yet.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Borrowing Trends by Department -->
      <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
        <div class="card">
          <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Borrowing Trends by Department</h5>
          </div>
          <div class="card-body">
            <?php if (count($dept_trends) > 0): ?>
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Department</th>
                      <th>Borrow Count</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($dept_trends as $dept): ?>
                      <tr>
                        <td><?php echo htmlspecialchars($dept['department_name'] ?: 'N/A'); ?></td>
                        <td><span class="badge bg-success"><?php echo $dept['borrow_count']; ?></span></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <p class="text-muted">No department data available yet.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Late Returns Statistics & Export -->
      <div class="col-lg-12 mt-4" data-aos="fade-up" data-aos-delay="300">
        <div class="card">
          <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Late Returns &amp; Exports</h5>
          </div>
          <div class="card-body">
            <div class="row gy-3 align-items-center">
              <div class="col-md-4">
                <h4 class="text-danger mb-1"><?php echo $late_returns; ?></h4>
                <p class="text-muted mb-0">Total Late Returns</p>
              </div>
              <div class="col-md-4">
                <a href="index.php?page=borrow" class="btn btn-danger w-100">
                  <i class="bi bi-eye"></i> View Late Returns Details
                </a>
              </div>
              <div class="col-md-4">
                <div class="d-grid gap-2">
                  <a href="index.php?page=export&amp;type=books" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Export Books
                  </a>
                  <a href="index.php?page=export&amp;type=borrows" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Export Borrow Records
                  </a>
                  <a href="index.php?page=export&amp;type=returns" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Export Returns
                  </a>
                  <a href="index.php?page=export&amp;type=fines" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Export Fines
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

