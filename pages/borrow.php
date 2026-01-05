<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireLogin();

$conn = getDBConnection();
$message = '';
$message_type = '';
$isAdmin = isAdmin();
$current_user_id = getCurrentUserId();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            // Students and faculty can only borrow for themselves
            $user_id = $isAdmin ? $_POST['user_id'] : $current_user_id;
            $book_id = $_POST['book_id'];
            $borrow_date = $_POST['borrow_date'] ?: date('Y-m-d');
            $due_date = $_POST['due_date'];
            $status = $_POST['status'] ?: 'Pending';
            
            $stmt = $conn->prepare("INSERT INTO borrow_records (user_id, book_id, borrow_date, due_date, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisss", $user_id, $book_id, $borrow_date, $due_date, $status);
            
            if ($stmt->execute()) {
                $message = "Borrow record added successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $message_type = "danger";
            }
            $stmt->close();
        } elseif ($_POST['action'] === 'update') {
            $borrow_id = intval($_POST['borrow_id']);
            $user_id = $_POST['user_id'];
            $book_id = $_POST['book_id'];
            $borrow_date = $_POST['borrow_date'];
            $due_date = $_POST['due_date'];
            $status = $_POST['status'];
            
            $stmt = $conn->prepare("UPDATE borrow_records SET user_id=?, book_id=?, borrow_date=?, due_date=?, status=? WHERE borrow_id=?");
            $stmt->bind_param("iisssi", $user_id, $book_id, $borrow_date, $due_date, $status, $borrow_id);
            
            if ($stmt->execute()) {
                $message = "Borrow record updated successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $message_type = "danger";
            }
            $stmt->close();
        } elseif ($_POST['action'] === 'delete') {
            $borrow_id = intval($_POST['borrow_id']);
            $stmt = $conn->prepare("DELETE FROM borrow_records WHERE borrow_id=?");
            $stmt->bind_param("i", $borrow_id);
            
            if ($stmt->execute()) {
                $message = "Borrow record deleted successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $message_type = "danger";
            }
            $stmt->close();
        }
    }
}

// Get borrow records - students/faculty see only their own, admins see all
if ($isAdmin) {
    $borrow_query = "SELECT br.*, u.first_name, u.last_name, b.title as book_title 
                    FROM borrow_records br 
                    LEFT JOIN users u ON br.user_id = u.user_id 
                    LEFT JOIN books b ON br.book_id = b.book_id 
                    ORDER BY br.borrow_id DESC";
} else {
    $borrow_query = "SELECT br.*, u.first_name, u.last_name, b.title as book_title 
                    FROM borrow_records br 
                    LEFT JOIN users u ON br.user_id = u.user_id 
                    LEFT JOIN books b ON br.book_id = b.book_id 
                    WHERE br.user_id = ?
                    ORDER BY br.borrow_id DESC";
    $stmt = $conn->prepare($borrow_query);
    $stmt->bind_param("i", $current_user_id);
    $stmt->execute();
    $borrow_result = $stmt->get_result();
    $stmt->close();
}

if (!isset($borrow_result)) {
    $borrow_result = $conn->query($borrow_query);
}

// Get users for dropdown
$users_result = $conn->query("SELECT * FROM users ORDER BY first_name, last_name");
$users = [];
while ($row = $users_result->fetch_assoc()) {
    $users[] = $row;
}

// Get books for dropdown
$books_result = $conn->query("SELECT * FROM books ORDER BY title");
$books = [];
while ($row = $books_result->fetch_assoc()) {
    $books[] = $row;
}
?>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="mb-4">Borrow Records Management</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Add Borrow Record Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Add New Borrow Record</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="add">
                        <div class="row">
                            <?php if ($isAdmin): ?>
                            <div class="col-md-6 mb-3">
                                <label for="user_id" class="form-label">User *</label>
                                <select class="form-select" id="user_id" name="user_id" required>
                                    <option value="">Select User</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?php echo $user['user_id']; ?>"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php else: ?>
                            <input type="hidden" name="user_id" value="<?php echo $current_user_id; ?>">
                            <?php endif; ?>
                            <div class="col-md-6 mb-3">
                                <label for="book_id" class="form-label">Book *</label>
                                <select class="form-select" id="book_id" name="book_id" required>
                                    <option value="">Select Book</option>
                                    <?php foreach ($books as $book): ?>
                                        <option value="<?php echo $book['book_id']; ?>"><?php echo htmlspecialchars($book['title']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="borrow_date" class="form-label">Borrow Date *</label>
                                <input type="date" class="form-control" id="borrow_date" name="borrow_date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="due_date" class="form-label">Due Date *</label>
                                <input type="date" class="form-control" id="due_date" name="due_date" value="<?php echo date('Y-m-d', strtotime('+14 days')); ?>" required>
                            </div>
                        </div>
                        <?php if ($isAdmin): ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="Pending">Pending</option>
                                    <option value="Returned">Returned</option>
                                </select>
                            </div>
                        </div>
                        <?php else: ?>
                        <input type="hidden" name="status" value="Pending">
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary">Add Borrow Record</button>
                    </form>
                </div>
            </div>
            
            <!-- Borrow Records List -->
            <div class="card">
                <div class="card-header">
                    <h4>Borrow Records List</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Book</th>
                                    <th>Borrow Date</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($borrow_result && $borrow_result->num_rows > 0): ?>
                                    <?php while ($borrow = $borrow_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $borrow['borrow_id']; ?></td>
                                            <td><?php echo htmlspecialchars(($borrow['first_name'] ?? '') . ' ' . ($borrow['last_name'] ?? '')); ?></td>
                                            <td><?php echo htmlspecialchars($borrow['book_title'] ?? 'N/A'); ?></td>
                                            <td><?php echo $borrow['borrow_date'] ? date('Y-m-d', strtotime($borrow['borrow_date'])) : 'N/A'; ?></td>
                                            <td><?php echo $borrow['due_date'] ? date('Y-m-d', strtotime($borrow['due_date'])) : 'N/A'; ?></td>
                                            <td><span class="badge bg-<?php echo $borrow['status'] === 'Returned' ? 'success' : 'warning'; ?>"><?php echo htmlspecialchars($borrow['status']); ?></span></td>
                                            <td>
                                                <?php if ($isAdmin): ?>
                                                    <button type="button" class="btn btn-sm btn-warning" onclick="editBorrow(<?php echo htmlspecialchars(json_encode($borrow)); ?>)">Edit</button>
                                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this borrow record?');">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="borrow_id" value="<?php echo $borrow['borrow_id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                    </form>
                                                <?php else: ?>
                                                    <?php if ($borrow['status'] === 'Pending'): ?>
                                                        <a href="index.php?page=returns&borrow_id=<?php echo $borrow['borrow_id']; ?>" class="btn btn-sm btn-success">Return</a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No borrow records found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Borrow Modal -->
<div class="modal fade" id="editBorrowModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Borrow Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="borrow_id" id="edit_borrow_id">
                    <div class="mb-3">
                        <label for="edit_user_id" class="form-label">User *</label>
                        <select class="form-select" id="edit_user_id" name="user_id" required>
                            <option value="">Select User</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['user_id']; ?>"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_book_id" class="form-label">Book *</label>
                        <select class="form-select" id="edit_book_id" name="book_id" required>
                            <option value="">Select Book</option>
                            <?php foreach ($books as $book): ?>
                                <option value="<?php echo $book['book_id']; ?>"><?php echo htmlspecialchars($book['title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_borrow_date" class="form-label">Borrow Date *</label>
                            <input type="date" class="form-control" id="edit_borrow_date" name="borrow_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_due_date" class="form-label">Due Date *</label>
                            <input type="date" class="form-control" id="edit_due_date" name="due_date" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status *</label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="Pending">Pending</option>
                            <option value="Returned">Returned</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Borrow Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editBorrow(borrow) {
    document.getElementById('edit_borrow_id').value = borrow.borrow_id;
    document.getElementById('edit_user_id').value = borrow.user_id || '';
    document.getElementById('edit_book_id').value = borrow.book_id || '';
    document.getElementById('edit_borrow_date').value = borrow.borrow_date || '';
    document.getElementById('edit_due_date').value = borrow.due_date || '';
    document.getElementById('edit_status').value = borrow.status || 'Pending';
    
    var editModal = new bootstrap.Modal(document.getElementById('editBorrowModal'));
    editModal.show();
}
</script>

