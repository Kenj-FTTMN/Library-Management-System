<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireLogin();

$conn = getDBConnection();
$message = '';
$message_type = '';
$isAdmin = isAdmin();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $title = $_POST['title'];
            $author_id = $_POST['author_id'] ?: null;
            $category_id = $_POST['category_id'] ?: null;
            $isbn = $_POST['isbn'] ?: null;
            $quantity = intval($_POST['quantity']);
            
            $stmt = $conn->prepare("INSERT INTO books (title, author_id, category_id, isbn, quantity, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("siisi", $title, $author_id, $category_id, $isbn, $quantity);
            
            if ($stmt->execute()) {
                $message = "Book added successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $message_type = "danger";
            }
            $stmt->close();
        } elseif ($_POST['action'] === 'update') {
            $book_id = intval($_POST['book_id']);
            $title = $_POST['title'];
            $author_id = $_POST['author_id'] ?: null;
            $category_id = $_POST['category_id'] ?: null;
            $isbn = $_POST['isbn'] ?: null;
            $quantity = intval($_POST['quantity']);
            
            $stmt = $conn->prepare("UPDATE books SET title=?, author_id=?, category_id=?, isbn=?, quantity=? WHERE book_id=?");
            $stmt->bind_param("siisii", $title, $author_id, $category_id, $isbn, $quantity, $book_id);
            
            if ($stmt->execute()) {
                $message = "Book updated successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $message_type = "danger";
            }
            $stmt->close();
        } elseif ($_POST['action'] === 'delete') {
            $book_id = intval($_POST['book_id']);
            $stmt = $conn->prepare("DELETE FROM books WHERE book_id=?");
            $stmt->bind_param("i", $book_id);
            
            if ($stmt->execute()) {
                $message = "Book deleted successfully!";
                $message_type = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $message_type = "danger";
            }
            $stmt->close();
        }
    }
}

// Get all books with author and category names
$books_query = "SELECT b.*, a.author_name, c.category_name 
                FROM books b 
                LEFT JOIN author a ON b.author_id = a.author_id 
                LEFT JOIN categories c ON b.category_id = c.category_id 
                ORDER BY b.book_id DESC";
$books_result = $conn->query($books_query);

// Get authors for dropdown
$authors_result = $conn->query("SELECT * FROM author ORDER BY author_name");
$authors = [];
while ($row = $authors_result->fetch_assoc()) {
    $authors[] = $row;
}

// Get categories for dropdown
$categories_result = $conn->query("SELECT * FROM categories ORDER BY category_name");
$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[] = $row;
}
?>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="mb-4">Books Management</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Add Book Form - Admin Only -->
            <?php if ($isAdmin): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Add New Book</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="add">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label">Title *</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="isbn" class="form-label">ISBN</label>
                                <input type="text" class="form-control" id="isbn" name="isbn">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="author_id" class="form-label">Author</label>
                                <select class="form-select" id="author_id" name="author_id">
                                    <option value="">Select Author</option>
                                    <?php foreach ($authors as $author): ?>
                                        <option value="<?php echo $author['author_id']; ?>"><?php echo htmlspecialchars($author['author_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['category_id']; ?>"><?php echo htmlspecialchars($category['category_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="quantity" class="form-label">Quantity *</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" min="0" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Book</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Books List -->
            <div class="card">
                <div class="card-header">
                    <h4>Books List</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Category</th>
                                    <th>ISBN</th>
                                    <th>Quantity</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($books_result && $books_result->num_rows > 0): ?>
                                    <?php while ($book = $books_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $book['book_id']; ?></td>
                                            <td><?php echo htmlspecialchars($book['title']); ?></td>
                                            <td><?php echo htmlspecialchars($book['author_name'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($book['category_name'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($book['isbn'] ?? 'N/A'); ?></td>
                                            <td><?php echo $book['quantity']; ?></td>
                                            <td><?php echo $book['created_at'] ? date('Y-m-d H:i', strtotime($book['created_at'])) : 'N/A'; ?></td>
                                            <td>
                                                <?php if ($isAdmin): ?>
                                                    <button type="button" class="btn btn-sm btn-warning" onclick="editBook(<?php echo htmlspecialchars(json_encode($book)); ?>)">Edit</button>
                                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this book?');">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                    </form>
                                                <?php else: ?>
                                                    <?php if (canBorrow()): ?>
                                                        <a href="index.php?page=borrow&book_id=<?php echo $book['book_id']; ?>" class="btn btn-sm btn-primary">Borrow</a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No books found.</td>
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

<!-- Edit Book Modal -->
<div class="modal fade" id="editBookModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="book_id" id="edit_book_id">
                    <div class="mb-3">
                        <label for="edit_title" class="form-label">Title *</label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_isbn" class="form-label">ISBN</label>
                        <input type="text" class="form-control" id="edit_isbn" name="isbn">
                    </div>
                    <div class="mb-3">
                        <label for="edit_author_id" class="form-label">Author</label>
                        <select class="form-select" id="edit_author_id" name="author_id">
                            <option value="">Select Author</option>
                            <?php foreach ($authors as $author): ?>
                                <option value="<?php echo $author['author_id']; ?>"><?php echo htmlspecialchars($author['author_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_category_id" class="form-label">Category</label>
                        <select class="form-select" id="edit_category_id" name="category_id">
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['category_id']; ?>"><?php echo htmlspecialchars($category['category_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_quantity" class="form-label">Quantity *</label>
                        <input type="number" class="form-control" id="edit_quantity" name="quantity" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Book</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editBook(book) {
    document.getElementById('edit_book_id').value = book.book_id;
    document.getElementById('edit_title').value = book.title;
    document.getElementById('edit_isbn').value = book.isbn || '';
    document.getElementById('edit_author_id').value = book.author_id || '';
    document.getElementById('edit_category_id').value = book.category_id || '';
    document.getElementById('edit_quantity').value = book.quantity;
    
    var editModal = new bootstrap.Modal(document.getElementById('editBookModal'));
    editModal.show();
}
</script>

