<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireAdmin();

$conn = getDBConnection();
$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $author_name = $_POST['author_name'];
            
            $stmt = $conn->prepare("INSERT INTO author (author_name) VALUES (?)");
            $stmt->bind_param("s", $author_name);
            
            if ($stmt->execute()) {
                $message = "Author added successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $message_type = "danger";
            }
            $stmt->close();
        } elseif ($_POST['action'] === 'update') {
            $author_id = intval($_POST['author_id']);
            $author_name = $_POST['author_name'];
            
            $stmt = $conn->prepare("UPDATE author SET author_name=? WHERE author_id=?");
            $stmt->bind_param("si", $author_name, $author_id);
            
            if ($stmt->execute()) {
                $message = "Author updated successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $message_type = "danger";
            }
            $stmt->close();
        } elseif ($_POST['action'] === 'delete') {
            $author_id = intval($_POST['author_id']);
            $stmt = $conn->prepare("DELETE FROM author WHERE author_id=?");
            $stmt->bind_param("i", $author_id);
            
            if ($stmt->execute()) {
                $message = "Author deleted successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $message_type = "danger";
            }
            $stmt->close();
        }
    }
}

// Get all authors
$authors_result = $conn->query("SELECT * FROM author ORDER BY author_name");
?>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="mb-4">Authors Management</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Add Author Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Add New Author</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="add">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="author_name" class="form-label">Author Name *</label>
                                <input type="text" class="form-control" id="author_name" name="author_name" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Author</button>
                    </form>
                </div>
            </div>
            
            <!-- Authors List -->
            <div class="card">
                <div class="card-header">
                    <h4>Authors List</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Author Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($authors_result && $authors_result->num_rows > 0): ?>
                                    <?php while ($author = $authors_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $author['author_id']; ?></td>
                                            <td><?php echo htmlspecialchars($author['author_name']); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning" onclick="editAuthor(<?php echo htmlspecialchars(json_encode($author)); ?>)">Edit</button>
                                                <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this author?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="author_id" value="<?php echo $author['author_id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No authors found.</td>
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

<!-- Edit Author Modal -->
<div class="modal fade" id="editAuthorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Author</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="author_id" id="edit_author_id">
                    <div class="mb-3">
                        <label for="edit_author_name" class="form-label">Author Name *</label>
                        <input type="text" class="form-control" id="edit_author_name" name="author_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Author</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editAuthor(author) {
    document.getElementById('edit_author_id').value = author.author_id;
    document.getElementById('edit_author_name').value = author.author_name;
    
    var editModal = new bootstrap.Modal(document.getElementById('editAuthorModal'));
    editModal.show();
}
</script>

