<?php
require_once __DIR__ . '/auth_core.php';

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php?page=login');
        exit();
    }
}

function requireRole($requiredRole) {
    requireLogin();
    if (!hasRole($requiredRole)) {
        header('Location: index.php?page=unauthorized');
        exit();
    }
}

function requireAdmin() {
    requireRole(ROLE_ADMIN);
}

function requireLibrarian() {
    requireRole(ROLE_LIBRARIAN);
}

function requireAdminOrLibrarian() {
    requireLogin();
    if (!isAdmin() && !isLibrarian()) {
        header('Location: index.php?page=unauthorized');
        exit();
    }
}