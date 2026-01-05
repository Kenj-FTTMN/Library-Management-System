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
            $role_name = $_POST['role_name'];
            
            $stmt = $conn->prepare("INSERT INTO roles (role_name) VALUES (?)");
            $stmt->bind_param("s", $role_name);
            
            if ($stmt->execute()) {
                $message = "Role added successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $message_type = "danger";
            }
            $stmt->close();
        } elseif ($_POST['action'] === 'update') {
            $role_id = intval($_POST['role_id']);
            $role_name = $_POST['role_name'];
            
            $stmt = $conn->prepare("UPDATE roles SET role_name=? WHERE role_id=?");
            $stmt->bind_param("si", $role_name, $role_id);
            
            if ($stmt->execute()) {
                $message = "Role updated successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $message_type = "danger";
            }
            $stmt->close();
        } elseif ($_POST['action'] === 'delete') {
            $role_id = intval($_POST['role_id']);
            $stmt = $conn->prepare("DELETE FROM roles WHERE role_id=?");
            $stmt->bind_param("i", $role_id);
            
            if ($stmt->execute()) {
                $message = "Role deleted successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $message_type = "danger";
            }
            $stmt->close();
        }
    }
}

// Get all roles
$roles_result = $conn->query("SELECT * FROM roles ORDER BY role_name");
?>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="mb-4">Roles Management</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Add Role Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Add New Role</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="add">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="role_name" class="form-label">Role Name *</label>
                                <input type="text" class="form-control" id="role_name" name="role_name" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Role</button>
                    </form>
                </div>
            </div>
            
            <!-- Roles List -->
            <div class="card">
                <div class="card-header">
                    <h4>Roles List</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Role Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($roles_result && $roles_result->num_rows > 0): ?>
                                    <?php while ($role = $roles_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $role['role_id']; ?></td>
                                            <td><?php echo htmlspecialchars($role['role_name']); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning" onclick="editRole(<?php echo htmlspecialchars(json_encode($role)); ?>)">Edit</button>
                                                <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="role_id" value="<?php echo $role['role_id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No roles found.</td>
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

<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="role_id" id="edit_role_id">
                    <div class="mb-3">
                        <label for="edit_role_name" class="form-label">Role Name *</label>
                        <input type="text" class="form-control" id="edit_role_name" name="role_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editRole(role) {
    document.getElementById('edit_role_id').value = role.role_id;
    document.getElementById('edit_role_name').value = role.role_name;
    
    var editModal = new bootstrap.Modal(document.getElementById('editRoleModal'));
    editModal.show();
}
</script>

