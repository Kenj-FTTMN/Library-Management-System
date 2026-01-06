<?php
/**
 * Authentication and Authorization System
 * Library Management System
 */

session_start();

// Define roles
define('ROLE_ADMIN', 'admin');
define('ROLE_FACULTY', 'faculty');
define('ROLE_STUDENT', 'student');
define('ROLE_LIBRARIAN', 'librarian');

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

/**
 * Get current user role
 */
function getCurrentRole() {
    return $_SESSION['role'] ?? null;
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Check if user has specific role
 */
function hasRole($role) {
    return isLoggedIn() && getCurrentRole() === $role;
}

/**
 * Check if user has admin role
 */
function isAdmin() {
    return hasRole(ROLE_ADMIN);
}

/**
 * Check if user has faculty role
 */
function isFaculty() {
    return hasRole(ROLE_FACULTY);
}

/**
 * Check if user has student role
 */
function isStudent() {
    return hasRole(ROLE_STUDENT);
}

/**
 * Check if user has librarian role
 */
function isLibrarian() {
    return hasRole(ROLE_LIBRARIAN);
}

/**
 * Require login - redirect to login if not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php?page=login');
        exit();
    }
}

/**
 * Require specific role - redirect if user doesn't have required role
 */
function requireRole($requiredRole) {
    requireLogin();
    if (!hasRole($requiredRole)) {
        header('Location: index.php?page=unauthorized');
        exit();
    }
}

/**
 * Require admin access
 */
function requireAdmin() {
    requireRole(ROLE_ADMIN);
}

/**
 * Require librarian access
 */
function requireLibrarian() {
    requireRole(ROLE_LIBRARIAN);
}

/**
 * Require admin or librarian access
 */
function requireAdminOrLibrarian() {
    requireLogin();
    if (!isAdmin() && !isLibrarian()) {
        header('Location: index.php?page=unauthorized');
        exit();
    }
}

/**
 * Check if user can access management pages (admin and librarian)
 */
function canManage() {
    return isAdmin() || isLibrarian();
}

/**
 * Check if user can borrow books (student and faculty only)
 */
function canBorrow() {
    return isStudent() || isFaculty();
}

/**
 * Check if user can manage books (admin and librarian)
 */
function canManageBooks() {
    return isAdmin(); // librarians no longer manage books
}

/**
 * Check if user can manage categories (admin and librarian)
 */
function canManageCategories() {
    return isAdmin(); // librarians no longer manage categories
}

/**
 * Check if user can manage users (admin and librarian)
 */
function canManageUsers() {
    return isAdmin(); // librarians no longer manage users
}

/**
 * Check if user can process borrows/returns (admin and librarian)
 */
function canProcessBorrows() {
    return isAdmin(); // restrict processing borrows to admin
}

/**
 * Check if user can manage fines (admin and librarian)
 */
function canManageFines() {
    return isAdmin(); // restrict fines management to admin
}

/**
 * Check if user can view books (all logged in users)
 */
function canViewBooks() {
    return isLoggedIn();
}

/**
 * Login user
 */
function loginUser($user_id, $role, $user_data = []) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['role'] = $role;
    $_SESSION['user_data'] = $user_data;
    $_SESSION['login_time'] = time();
}

/**
 * Logout user
 */
function logoutUser() {
    session_unset();
    session_destroy();
}

/**
 * Get user data from session
 */
function getUserData() {
    return $_SESSION['user_data'] ?? [];
}
?>

