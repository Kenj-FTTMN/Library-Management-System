<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $conn = getDBConnection();
        
        // Check if user exists
        $stmt = $conn->prepare("SELECT u.*, r.role_name, d.department_name 
                                FROM users u 
                                LEFT JOIN roles r ON u.role_id = r.role_id 
                                LEFT JOIN department d ON u.department_id = d.department_id 
                                WHERE u.email = ? AND u.password = ?");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Map role_name to role constant
            $role_name = strtolower($user['role_name'] ?? 'student');
            $role = null;
            
            if (strpos($role_name, 'admin') !== false || $role_name === 'admin') {
                $role = ROLE_ADMIN;
            } elseif (strpos($role_name, 'faculty') !== false || $role_name === 'faculty') {
                $role = ROLE_FACULTY;
            } else {
                $role = ROLE_STUDENT;
            }
            
            // Login user
            loginUser($user['user_id'], $role, [
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email'],
                'role_name' => $user['role_name'],
                'department_name' => $user['department_name']
            ]);
            
            // Redirect based on role to different landing pages
            if ($role === ROLE_ADMIN) {
                header('Location: index.php?page=admin-dashboard');
            } elseif ($role === ROLE_FACULTY) {
                header('Location: index.php?page=faculty-dashboard');
            } else {
                header('Location: index.php?page=student-dashboard');
            }
            exit();
        } else {
            $error = 'Invalid email or password.';
        }
        
        $stmt->close();
    }
}
?>

<?php
// Set page config for login
$page_title = 'Login - Library Management System';
$page_description = 'Login to your account';
$page_body_class = 'login-page';
include __DIR__ . '/../includes/head.php';
?>
    <style>
        /* Inline CSS for Login Page - Fix opacity, text colors, and gradient gap */
        html {
            margin: 0 !important;
            padding: 0 !important;
            height: 100% !important;
            min-height: 100vh !important;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            background-attachment: fixed !important;
            background-repeat: no-repeat !important;
            background-size: cover !important;
        }
        
        body.login-page {
            margin: 0 !important;
            padding: 20px !important;
            min-height: 100vh !important;
            background: transparent !important;
        }
        
        body.login-page .login-card {
            opacity: 1 !important;
            visibility: visible !important;
        }
        
        body.login-page .login-header h2 {
            color: #ffffff !important;
        }
        
        body.login-page .login-header p {
            color: #ffffff !important;
        }
        
        body.login-page .login-body {
            color: #212529 !important;
        }
        
        body.login-page .login-body .form-label {
            color: #495057 !important;
        }
        
        body.login-page .login-body .role-info h6 {
            color: #667eea !important;
        }
        
        body.login-page .login-body .role-info li {
            color: #6c757d !important;
        }
        
        body.login-page .login-body .role-info small {
            color: #6c757d !important;
        }
        
        body.login-page .login-body .alert {
            color: inherit !important;
        }
    </style>
    <div class="login-container">
        <div class="login-card" data-aos="fade-up">
            <div class="login-header">
                <i class="bi bi-book"></i>
                <h2>Library Management System</h2>
                <p class="mb-0">Sign in to your account</p>
            </div>
            <div class="login-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($success); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="index.php?page=login">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required autofocus>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-login w-100 mb-3">
                        <i class="bi bi-box-arrow-in-right"></i> Sign In
                    </button>
                </form>
                
                <div class="role-info">
                    <h6><i class="bi bi-info-circle"></i> Available Roles:</h6>
                    <ul>
                        <li><strong>Admin:</strong> Full system access</li>
                        <li><strong>Faculty:</strong> Borrow books, view history</li>
                        <li><strong>Student:</strong> Borrow books, view history</li>
                    </ul>
                    <small class="text-muted">Note: Use existing user credentials or create users through the admin panel.</small>
                </div>
            </div>
        </div>
    </div>
    
    <?php include __DIR__ . '/../includes/scripts.php'; ?>

