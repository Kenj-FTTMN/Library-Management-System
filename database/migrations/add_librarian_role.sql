-- Migration: Add Librarian Role and User
-- Description: Adds librarian role and creates a default librarian user account
-- Date: 2025-01-XX

-- Add Librarian Role
INSERT INTO roles (role_name) VALUES ('Librarian');

-- Create Librarian User Account
-- Email: librarian@library.com
-- Password: librarian123
-- Note: In production, passwords should be hashed. This is plain text for development.
INSERT INTO users (first_name, last_name, email, password, role_id, department_id, created_at) 
VALUES (
    'Library',
    'Librarian',
    'librarian@library.com',
    'librarian123',
    (SELECT role_id FROM roles WHERE role_name = 'Librarian'),
    NULL,
    NOW()
);

-- Verify the insertion
SELECT 'Librarian role and user created successfully!' AS status;

