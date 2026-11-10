-- Run this ONCE on a database that was made with the old library.sql (before librarians existed).
-- In phpMyAdmin: select the database, click Import, choose this file and press Go.
-- It keeps all your users, books and loans.

ALTER TABLE users ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'member' AFTER otp_code;

-- Demo accounts (see README for the passwords)
-- Librarian: manages books, members and loans
-- Student:   a normal member who borrows books
INSERT INTO users (full_name, email, university_id, password_hash, is_verified, role)
VALUES ('Head Librarian', 'librarian@bookshelf.local', 'LIB-0001',
        '$2y$12$ZYIQwwaRaZc8T0W29H62We2vNNRcH9KUP8au0rOKIjQcvrjBWZV26', 1, 'librarian'),
       ('Demo Student', 'student@bookshelf.local', 'STU-0001',
        '$2y$12$MvWM3uFIlSaP62jlL0EI.ekh/LD/lD68GrrTN/35neFl4XFyswsiG', 1, 'member');
