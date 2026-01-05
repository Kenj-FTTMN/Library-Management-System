<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireLogin();

$conn = getDBConnection();
$message = '';
$message_type = '';
$isAdmin = isAdmin();
$isLibrarian = isLibrarian();
$canProcessBorrows = canProcessBorrows();
$current_user_id = getCurrentUserId();

// Handle pre-filled return from borrow page
$prefill_borrow_id = isset($_GET['borrow_id']) ? intval($_GET['borrow_id']) : null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $borrow_id = $_POST['borrow_id'];
            $return_date = $_POST['return_date'];
            $bookcondition = $_POST['bookcondition'];
            
            // Start transaction
            $conn->begin_transaction();
            
            try {
                // Insert return record
                $stmt = $conn->prepare("INSERT INTO returns (borrow_id, return_date, bookcondition) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $borrow_id, $return_date, $bookcondition);
                $stmt->execute();
                $stmt->close();
                
                // Update borrow record status to Returned
                $stmt = $conn->prepare("UPDATE borrow_records SET status = 'Returned' WHERE borrow_id = ?");
                $stmt->bind_param("i", $borrow_id);
                $stmt->execute();
                $stmt->close();
                
                // Commit transaction
                $conn->commit();
                $message = "Return record added successfully!";
                $message_type = "success";
            } catch (Exception $e) {
                $conn->rollback();
                $message = "Error: " . $e->getMessage();
                $message_type = "danger";
            }
        } elseif ($_POST['action'] === 'update') {
            $return_id = intval($_POST['return_id']);
            $borrow_id = $_POST['borrow_id'];
            $return_date = $_POST['return_date'];
            $bookcondition = $_POST['bookcondition'];
            
            $stmt = $conn->prepare("UPDATE returns SET borrow_id=?, return_date=?, bookcondition=? WHERE return_id=?");
            $stmt->bind_param("issi", $borrow_id, $return_date, $bookcondition, $return_id);
            
            if ($stmt->execute()) {
                $message = "Return record updated successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $message_type = "danger";
            }
            $stmt->close();
        } elseif ($_POST['action'] === 'delete') {
            $return_id = intval($_POST['return_id']);
            $stmt = $conn->prepare("DELETE FROM returns WHERE return_id=?");
            $stmt->bind_param("i", $return_id);
            
            if ($stmt->execute()) {
                $message = "Return record deleted successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $message_type = "danger";
            }
            $stmt->close();
        }
    }
}

// Get returns - students/faculty see only their own, admin/librarian see all
if ($canProcessBorrows) {
    $returns_query = "SELECT r.*, br.user_id, br.book_id, u.first_name, u.last_name, b.title as book_title 
                    FROM returns r 
                    LEFT JOIN borrow_records br ON r.borrow_id = br.borrow_id 
                    LEFT JOIN users u ON br.user_id = u.user_id 
                    LEFT JOIN books b ON br.book_id = b.book_id 
                    ORDER BY r.return_id DESC";
    $returns_result = $conn->query($returns_query);
} else {
    $returns_query = "SELECT r.*, br.user_id, br.book_id, u.first_name, u.last_name, b.title as book_title 
                    FROM returns r 
                    LEFT JOIN borrow_records br ON r.borrow_id = br.borrow_id 
                    LEFT JOIN users u ON br.user_id = u.user_id 
                    LEFT JOIN books b ON br.book_id = b.book_id 
                    WHERE br.user_id = ?
                    ORDER BY r.return_id DESC";
    $stmt = $conn->prepare($returns_query);
    $stmt->bind_param("i", $current_user_id);
    $stmt->execute();
    $returns_result = $stmt->get_result();
    $stmt->close();
}

// Get borrow records for dropdown - students/faculty see only their pending borrows, admin/librarian see all
if ($canProcessBorrows) {
    $borrow_query = "SELECT br.*, u.first_name, u.last_name, b.title as book_title 
                     FROM borrow_records br 
                     LEFT JOIN users u ON br.user_id = u.user_id 
                     LEFT JOIN books b ON br.book_id = b.book_id 
                     WHERE br.status = 'Pending'
                     ORDER BY br.borrow_id DESC";
} else {
    $borrow_query = "SELECT br.*, u.first_name, u.last_name, b.title as book_title 
                     FROM borrow_records br 
                     LEFT JOIN users u ON br.user_id = u.user_id 
                     LEFT JOIN books b ON br.book_id = b.book_id 
                     WHERE br.user_id = ? AND br.status = 'Pending'
                     ORDER BY br.borrow_id DESC";
}

if ($canProcessBorrows) {
    $borrow_result = $conn->query($borrow_query);
} else {
    $stmt = $conn->prepare($borrow_query);
    $stmt->bind_param("i", $current_user_id);
    $stmt->execute();
    $borrow_result = $stmt->get_result();
    $stmt->close();
}

$borrows = [];
while ($row = $borrow_result->fetch_assoc()) {
    $borrows[] = $row;
}
?>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="mb-4">Returns Management</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Add Return Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Add New Return Record</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="add">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="borrow_id" class="form-label">Borrow Record *</label>
                                <select class="form-select" id="borrow_id" name="borrow_id" required>
                                    <option value="">Select Borrow Record</option>
                                    <?php foreach ($borrows as $borrow): ?>
                                        <option value="<?php echo $borrow['borrow_id']; ?>" <?php echo ($prefill_borrow_id == $borrow['borrow_id']) ? 'selected' : ''; ?>>
                                            ID: <?php echo $borrow['borrow_id']; ?> - 
                                            <?php if ($canProcessBorrows): ?>
                                                <?php echo htmlspecialchars(($borrow['first_name'] ?? '') . ' ' . ($borrow['last_name'] ?? '')); ?> - 
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($borrow['book_title'] ?? 'N/A'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="return_date" class="form-label">Return Date *</label>
                                <input type="date" class="form-control" id="return_date" name="return_date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="bookcondition" class="form-label">Book Condition *</label>
                                <select class="form-select" id="bookcondition" name="bookcondition" required>
                                    <option value="Good">Good</option>
                                    <option value="Damaged">Damaged</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Return Record</button>
                    </form>
                </div>
            </div>
            
            <!-- Returns List -->
            <div class="card">
                <div class="card-header">
                    <h4>Returns List</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Borrow ID</th>
                                    <th>User</th>
                                    <th>Book</th>
                                    <th>Return Date</th>
                                    <th>Condition</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($returns_result && $returns_result->num_rows > 0): ?>
                                    <?php while ($return = $returns_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $return['return_id']; ?></td>
                                            <td><?php echo $return['borrow_id']; ?></td>
                                            <td><?php echo htmlspecialchars(($return['first_name'] ?? '') . ' ' . ($return['last_name'] ?? '')); ?></td>
                                            <td><?php echo htmlspecialchars($return['book_title'] ?? 'N/A'); ?></td>
                                            <td><?php echo $return['return_date'] ? date('Y-m-d', strtotime($return['return_date'])) : 'N/A'; ?></td>
                                            <td><span class="badge bg-<?php echo $return['bookcondition'] === 'Good' ? 'success' : 'danger'; ?>"><?php echo htmlspecialchars($return['bookcondition']); ?></span></td>
                                            <td>
                                                <?php if ($canProcessBorrows): ?>
                                                    <button type="button" class="btn btn-sm btn-warning" onclick="editReturn(<?php echo htmlspecialchars(json_encode($return)); ?>)">Edit</button>
                                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this return record?');">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="return_id" value="<?php echo $return['return_id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="text-muted">View Only</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No return records found.</td>
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

<!-- Edit Return Modal -->
<div class="modal fade" id="editReturnModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Return Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="return_id" id="edit_return_id">
                    <div class="mb-3">
                        <label for="edit_borrow_id" class="form-label">Borrow Record *</label>
                        <select class="form-select" id="edit_borrow_id" name="borrow_id" required>
                            <option value="">Select Borrow Record</option>
                            <?php foreach ($borrows as $borrow): ?>
                                <option value="<?php echo $borrow['borrow_id']; ?>">
                                    ID: <?php echo $borrow['borrow_id']; ?> - 
                                    <?php echo htmlspecialchars(($borrow['first_name'] ?? '') . ' ' . ($borrow['last_name'] ?? '')); ?> - 
                                    <?php echo htmlspecialchars($borrow['book_title'] ?? 'N/A'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_return_date" class="form-label">Return Date *</label>
                        <input type="date" class="form-control" id="edit_return_date" name="return_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_bookcondition" class="form-label">Book Condition *</label>
                        <select class="form-select" id="edit_bookcondition" name="bookcondition" required>
                            <option value="Good">Good</option>
                            <option value="Damaged">Damaged</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Return Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editReturn(returnRecord) {
    document.getElementById('edit_return_id').value = returnRecord.return_id;
    document.getElementById('edit_borrow_id').value = returnRecord.borrow_id || '';
    document.getElementById('edit_return_date').value = returnRecord.return_date || '';
    document.getElementById('edit_bookcondition').value = returnRecord.bookcondition || 'Good';
    
    var editModal = new bootstrap.Modal(document.getElementById('editReturnModal'));
    editModal.show();
}
</script>

