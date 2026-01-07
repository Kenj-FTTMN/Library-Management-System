<?php
session_start();

/* Roles */
define('ROLE_ADMIN', 'admin');
define('ROLE_FACULTY', 'faculty');
define('ROLE_STUDENT', 'student');
define('ROLE_LIBRARIAN', 'librarian');

/* Basic checks */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

function getCurrentRole() {
    return $_SESSION['role'] ?? null;
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function hasRole($role) {
    return isLoggedIn() && getCurrentRole() === $role;
}

/* Role helpers */
function isAdmin() {
    return hasRole(ROLE_ADMIN);
}

function isFaculty() {
    return hasRole(ROLE_FACULTY);
}

function isStudent() {
    return hasRole(ROLE_STUDENT);
}

function isLibrarian() {
    return hasRole(ROLE_LIBRARIAN);
}

/* Permission helpers */
function canManage() {
    return isAdmin() || isLibrarian();
}

function canBorrow() {
    return isStudent() || isFaculty();
}

function canManageBooks() {
    return isAdmin();
}

function canManageCategories() {
    return isAdmin();
}

function canManageUsers() {
    return isAdmin();
}

function canProcessBorrows() {
    return isAdmin();
}

function canManageFines() {
    return isAdmin();
}

function canViewBooks() {
    return isLoggedIn();
}

/* Session helpers */
function loginUser($user_id, $role, $user_data = []) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['role'] = $role;
    $_SESSION['user_data'] = $user_data;
    $_SESSION['login_time'] = time();
}

function logoutUser() {
    session_unset();
    session_destroy();
}

function getUserData() {
    return $_SESSION['user_data'] ?? [];
}
