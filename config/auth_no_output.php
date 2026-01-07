<?php
session_start();

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';
}

function isLibrarian() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Librarian';
}

function requireAdminOrLibrarian() {
    if (!isAdmin() && !isLibrarian()) {
        http_response_code(403);
        exit('Unauthorized access.');
    }
}
