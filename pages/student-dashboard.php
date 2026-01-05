<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireLogin();
if (!isStudent() && !isAdmin()) {
    header('Location: index.php?page=unauthorized');
    exit();
}

$conn = getDBConnection();
$current_user_id = getCurrentUserId();
$user_data = getUserData();

// Get student-specific statistics
$stats = [];

// My Borrowed Books
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM borrow_records WHERE user_id = ? AND status = 'Pending'");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats['my_borrows'] = $result->fetch_assoc()['total'];
$stmt->close();

// My Returned Books
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM borrow_records WHERE user_id = ? AND status = 'Returned'");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats['my_returns'] = $result->fetch_assoc()['total'];
$stmt->close();

// My Pending Returns
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM borrow_records br 
                       LEFT JOIN returns r ON br.borrow_id = r.borrow_id 
                       WHERE br.user_id = ? AND br.status = 'Pending' AND r.return_id IS NULL");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats['pending_returns'] = $result->fetch_assoc()['total'];
$stmt->close();

// Total Available Books
$result = $conn->query("SELECT COUNT(*) as total FROM books WHERE quantity > 0");
$stats['available_books'] = $result->fetch_assoc()['total'];

// Get my recent borrows
$stmt = $conn->prepare("SELECT br.*, b.title, b.isbn, br.due_date 
                       FROM borrow_records br 
                       LEFT JOIN books b ON br.book_id = b.book_id 
                       WHERE br.user_id = ? 
                       ORDER BY br.borrow_date DESC 
                       LIMIT 5");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$recent_borrows = $stmt->get_result();
$stmt->close();
?>

<!-- Student Dashboard -->
<section id="student-dashboard" class="section">
  <div class="container">
    <!-- Welcome Section -->
    <div class="row mb-4" data-aos="fade-down">
      <div class="col-12">
        <div class="card border-info">
          <div class="card-body">
            <h2 class="mb-3">
              <i class="bi bi-person-circle text-info"></i> 
              Welcome, <?php echo htmlspecialchars($user_data['first_name'] ?? 'Student'); ?>!
            </h2>
            <p class="lead text-muted">Student Dashboard - Browse books and manage your borrowings.</p>
            <p class="mb-0"><strong>Department:</strong> <?php echo htmlspecialchars($user_data['department_name'] ?? 'N/A'); ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Dashboard Stats -->
    <h2 class="text-center mb-5" data-aos="fade-down" data-aos-delay="50">My Library Overview</h2>
    
    <div class="row gy-4">
      <!-- My Borrows Card -->
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="card text-center h-100 dashboard-card">
          <div class="card-body">
            <div class="feature-icon mb-3">
              <i class="bi bi-book-half" style="font-size: 3rem; color: #0d6efd;"></i>
            </div>
            <h3 class="card-title counter" data-target="<?php echo $stats['my_borrows']; ?>">0</h3>
            <p class="card-text">Currently Borrowed</p>
            <a href="index.php?page=borrow" class="btn btn-primary">View My Borrows</a>
          </div>
        </div>
      </div>

      <!-- My Returns Card -->
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
        <div class="card text-center h-100 dashboard-card">
          <div class="card-body">
            <div class="feature-icon mb-3">
              <i class="bi bi-check-circle-fill" style="font-size: 3rem; color: #198754;"></i>
            </div>
            <h3 class="card-title counter" data-target="<?php echo $stats['my_returns']; ?>">0</h3>
            <p class="card-text">Returned Books</p>
            <a href="index.php?page=returns" class="btn btn-success">View Returns</a>
          </div>
        </div>
      </div>

      <!-- Pending Returns Card -->
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
        <div class="card text-center h-100 dashboard-card">
          <div class="card-body">
            <div class="feature-icon mb-3">
              <i class="bi bi-clock-history" style="font-size: 3rem; color: #ffc107;"></i>
            </div>
            <h3 class="card-title counter" data-target="<?php echo $stats['pending_returns']; ?>">0</h3>
            <p class="card-text">Pending Returns</p>
            <a href="index.php?page=returns" class="btn btn-warning">Return Books</a>
          </div>
        </div>
      </div>

      <!-- Available Books Card -->
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
        <div class="card text-center h-100 dashboard-card">
          <div class="card-body">
            <div class="feature-icon mb-3">
              <i class="bi bi-book-fill" style="font-size: 3rem; color: #0dcaf0;"></i>
            </div>
            <h3 class="card-title counter" data-target="<?php echo $stats['available_books']; ?>">0</h3>
            <p class="card-text">Available Books</p>
            <a href="index.php?page=books" class="btn btn-info">Browse Books</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Borrows -->
    <div class="row mt-5">
      <div class="col-12">
        <div class="card" data-aos="fade-up">
          <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="bi bi-clock-history"></i> My Recent Borrows</h4>
          </div>
          <div class="card-body">
            <?php if ($recent_borrows && $recent_borrows->num_rows > 0): ?>
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Book Title</th>
                      <th>ISBN</th>
                      <th>Borrow Date</th>
                      <th>Due Date</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while ($borrow = $recent_borrows->fetch_assoc()): ?>
                      <tr>
                        <td><?php echo htmlspecialchars($borrow['title'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($borrow['isbn'] ?? 'N/A'); ?></td>
                        <td><?php echo $borrow['borrow_date'] ? date('Y-m-d', strtotime($borrow['borrow_date'])) : 'N/A'; ?></td>
                        <td>
                          <?php 
                          $due_date = $borrow['due_date'] ? strtotime($borrow['due_date']) : null;
                          $is_overdue = $due_date && $due_date < time() && $borrow['status'] === 'Pending';
                          ?>
                          <span class="<?php echo $is_overdue ? 'text-danger fw-bold' : ''; ?>">
                            <?php echo $borrow['due_date'] ? date('Y-m-d', strtotime($borrow['due_date'])) : 'N/A'; ?>
                          </span>
                          <?php if ($is_overdue): ?>
                            <span class="badge bg-danger">Overdue</span>
                          <?php endif; ?>
                        </td>
                        <td><span class="badge bg-<?php echo $borrow['status'] === 'Returned' ? 'success' : 'warning'; ?>"><?php echo htmlspecialchars($borrow['status']); ?></span></td>
                        <td>
                          <?php if ($borrow['status'] === 'Pending'): ?>
                            <a href="index.php?page=returns&borrow_id=<?php echo $borrow['borrow_id']; ?>" class="btn btn-sm btn-success">Return</a>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <p class="text-muted text-center">No borrow records found. Start borrowing books from the library!</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
      <div class="col-12">
        <h3 class="mb-4" data-aos="fade-up">Quick Actions</h3>
        <div class="row gy-3">
          <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
            <a href="index.php?page=books" class="btn btn-outline-primary w-100">
              <i class="bi bi-search"></i> Browse Available Books
            </a>
          </div>
          <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
            <a href="index.php?page=borrow" class="btn btn-outline-success w-100">
              <i class="bi bi-journal-text"></i> My Borrowing History
            </a>
          </div>
          <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
            <a href="index.php?page=returns" class="btn btn-outline-warning w-100">
              <i class="bi bi-arrow-return-left"></i> Return a Book
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

