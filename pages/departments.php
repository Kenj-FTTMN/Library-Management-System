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
            $department_name = $_POST['department_name'];
            
            $stmt = $conn->prepare("INSERT INTO department (department_name) VALUES (?)");
            $stmt->bind_param("s", $department_name);
            
            if ($stmt->execute()) {
                $message = "Department added successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $message_type = "danger";
            }
            $stmt->close();
        } elseif ($_POST['action'] === 'update') {
            $department_id = intval($_POST['department_id']);
            $department_name = $_POST['department_name'];
            
            $stmt = $conn->prepare("UPDATE department SET department_name=? WHERE department_id=?");
            $stmt->bind_param("si", $department_name, $department_id);
            
            if ($stmt->execute()) {
                $message = "Department updated successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $message_type = "danger";
            }
            $stmt->close();
        } elseif ($_POST['action'] === 'delete') {
            $department_id = intval($_POST['department_id']);
            $stmt = $conn->prepare("DELETE FROM department WHERE department_id=?");
            $stmt->bind_param("i", $department_id);
            
            if ($stmt->execute()) {
                $message = "Department deleted successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $message_type = "danger";
            }
            $stmt->close();
        }
    }
}

// Get all departments
$departments_result = $conn->query("SELECT * FROM department ORDER BY department_name");
?>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="mb-4">Departments Management</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Add Department Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Add New Department</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="add">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="department_name" class="form-label">Department Name *</label>
                                <input type="text" class="form-control" id="department_name" name="department_name" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Department</button>
                    </form>
                </div>
            </div>
            
            <!-- Departments List -->
            <div class="card">
                <div class="card-header">
                    <h4>Departments List</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Department Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($departments_result && $departments_result->num_rows > 0): ?>
                                    <?php while ($department = $departments_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $department['department_id']; ?></td>
                                            <td><?php echo htmlspecialchars($department['department_name']); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning" onclick="editDepartment(<?php echo htmlspecialchars(json_encode($department)); ?>)">Edit</button>
                                                <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this department?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="department_id" value="<?php echo $department['department_id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No departments found.</td>
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

<!-- Edit Department Modal -->
<div class="modal fade" id="editDepartmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="department_id" id="edit_department_id">
                    <div class="mb-3">
                        <label for="edit_department_name" class="form-label">Department Name *</label>
                        <input type="text" class="form-control" id="edit_department_name" name="department_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Department</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editDepartment(department) {
    document.getElementById('edit_department_id').value = department.department_id;
    document.getElementById('edit_department_name').value = department.department_name;
    
    var editModal = new bootstrap.Modal(document.getElementById('editDepartmentModal'));
    editModal.show();
}
</script>

