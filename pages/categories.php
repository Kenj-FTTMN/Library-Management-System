<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireAdminOrLibrarian();

$conn = getDBConnection();
$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $category_name = $_POST['category_name'];
            
            $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
            $stmt->bind_param("s", $category_name);
            
            if ($stmt->execute()) {
                $message = "Category added successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $message_type = "danger";
            }
            $stmt->close();
        } elseif ($_POST['action'] === 'update') {
            $category_id = intval($_POST['category_id']);
            $category_name = $_POST['category_name'];
            
            $stmt = $conn->prepare("UPDATE categories SET category_name=? WHERE category_id=?");
            $stmt->bind_param("si", $category_name, $category_id);
            
            if ($stmt->execute()) {
                $message = "Category updated successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $message_type = "danger";
            }
            $stmt->close();
        } elseif ($_POST['action'] === 'delete') {
            $category_id = intval($_POST['category_id']);
            $stmt = $conn->prepare("DELETE FROM categories WHERE category_id=?");
            $stmt->bind_param("i", $category_id);
            
            if ($stmt->execute()) {
                $message = "Category deleted successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $message_type = "danger";
            }
            $stmt->close();
        }
    }
}

// Get all categories
$categories_result = $conn->query("SELECT * FROM categories ORDER BY category_name");
?>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="mb-4">Categories Management</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Add Category Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Add New Category</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="add">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category_name" class="form-label">Category Name *</label>
                                <input type="text" class="form-control" id="category_name" name="category_name" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Category</button>
                    </form>
                </div>
            </div>
            
            <!-- Categories List -->
            <div class="card">
                <div class="card-header">
                    <h4>Categories List</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Category Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($categories_result && $categories_result->num_rows > 0): ?>
                                    <?php while ($category = $categories_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $category['category_id']; ?></td>
                                            <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning" onclick="editCategory(<?php echo htmlspecialchars(json_encode($category)); ?>)">Edit</button>
                                                <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No categories found.</td>
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

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="category_id" id="edit_category_id">
                    <div class="mb-3">
                        <label for="edit_category_name" class="form-label">Category Name *</label>
                        <input type="text" class="form-control" id="edit_category_name" name="category_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editCategory(category) {
    document.getElementById('edit_category_id').value = category.category_id;
    document.getElementById('edit_category_name').value = category.category_name;
    
    var editModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
    editModal.show();
}
</script>

