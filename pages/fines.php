<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireLogin();

$conn = getDBConnection();
$message = '';
$message_type = '';
$canManageFines = canManageFines();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $borrow_id = $_POST['borrow_id'];
            $fine_amount = $_POST['fine_amount'];
            $status = $_POST['status'];
            
            $stmt = $conn->prepare("INSERT INTO fines (borrow_id, fine_amount, status) VALUES (?, ?, ?)");
            $stmt->bind_param("ids", $borrow_id, $fine_amount, $status);
            
            if ($stmt->execute()) {
                $message = "Fine added successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $message_type = "danger";
            }
            $stmt->close();
        } elseif ($_POST['action'] === 'update') {
            $fine_id = intval($_POST['fine_id']);
            $borrow_id = $_POST['borrow_id'];
            $fine_amount = $_POST['fine_amount'];
            $status = $_POST['status'];
            
            $stmt = $conn->prepare("UPDATE fines SET borrow_id=?, fine_amount=?, status=? WHERE fine_id=?");
            $stmt->bind_param("idsi", $borrow_id, $fine_amount, $status, $fine_id);
            
            if ($stmt->execute()) {
                $message = "Fine updated successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $message_type = "danger";
            }
            $stmt->close();
        } elseif ($_POST['action'] === 'delete') {
            $fine_id = intval($_POST['fine_id']);
            $stmt = $conn->prepare("DELETE FROM fines WHERE fine_id=?");
            $stmt->bind_param("i", $fine_id);
            
            if ($stmt->execute()) {
                $message = "Fine deleted successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $message_type = "danger";
            }
            $stmt->close();
        }
    }
}

// Get all fines with borrow record details
$fines_query = "SELECT f.*, br.user_id, br.book_id, u.first_name, u.last_name, b.title as book_title 
                FROM fines f 
                LEFT JOIN borrow_records br ON f.borrow_id = br.borrow_id 
                LEFT JOIN users u ON br.user_id = u.user_id 
                LEFT JOIN books b ON br.book_id = b.book_id 
                ORDER BY f.fine_id DESC";
$fines_result = $conn->query($fines_query);

// Get borrow records for dropdown
$borrow_result = $conn->query("SELECT br.*, u.first_name, u.last_name, b.title as book_title 
                               FROM borrow_records br 
                               LEFT JOIN users u ON br.user_id = u.user_id 
                               LEFT JOIN books b ON br.book_id = b.book_id 
                               ORDER BY br.borrow_id DESC");
$borrows = [];
while ($row = $borrow_result->fetch_assoc()) {
    $borrows[] = $row;
}
?>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="mb-4">Fines Management</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Add Fine Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Add New Fine</h4>
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
                                        <option value="<?php echo $borrow['borrow_id']; ?>">
                                            ID: <?php echo $borrow['borrow_id']; ?> - 
                                            <?php echo htmlspecialchars(($borrow['first_name'] ?? '') . ' ' . ($borrow['last_name'] ?? '')); ?> - 
                                            <?php echo htmlspecialchars($borrow['book_title'] ?? 'N/A'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fine_amount" class="form-label">Fine Amount *</label>
                                <input type="number" step="0.01" class="form-control" id="fine_amount" name="fine_amount" min="0" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="unpaid">Unpaid</option>
                                    <option value="paid">Paid</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Fine</button>
                    </form>
                </div>
            </div>
            
            <!-- Fines List -->
            <div class="card">
                <div class="card-header">
                    <h4>Fines List</h4>
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
                                    <th>Fine Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($fines_result && $fines_result->num_rows > 0): ?>
                                    <?php while ($fine = $fines_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $fine['fine_id']; ?></td>
                                            <td><?php echo $fine['borrow_id']; ?></td>
                                            <td><?php echo htmlspecialchars(($fine['first_name'] ?? '') . ' ' . ($fine['last_name'] ?? '')); ?></td>
                                            <td><?php echo htmlspecialchars($fine['book_title'] ?? 'N/A'); ?></td>
                                            <td>$<?php echo number_format($fine['fine_amount'], 2); ?></td>
                                            <td><span class="badge bg-<?php echo $fine['status'] === 'paid' ? 'success' : 'danger'; ?>"><?php echo ucfirst(htmlspecialchars($fine['status'])); ?></span></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning" onclick="editFine(<?php echo htmlspecialchars(json_encode($fine)); ?>)">Edit</button>
                                                <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this fine?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="fine_id" value="<?php echo $fine['fine_id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No fines found.</td>
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

<!-- Edit Fine Modal -->
<div class="modal fade" id="editFineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Fine</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="fine_id" id="edit_fine_id">
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
                        <label for="edit_fine_amount" class="form-label">Fine Amount *</label>
                        <input type="number" step="0.01" class="form-control" id="edit_fine_amount" name="fine_amount" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status *</label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="unpaid">Unpaid</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Fine</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editFine(fine) {
    document.getElementById('edit_fine_id').value = fine.fine_id;
    document.getElementById('edit_borrow_id').value = fine.borrow_id || '';
    document.getElementById('edit_fine_amount').value = fine.fine_amount || '';
    document.getElementById('edit_status').value = fine.status || 'unpaid';
    
    var editModal = new bootstrap.Modal(document.getElementById('editFineModal'));
    editModal.show();
}
</script>

