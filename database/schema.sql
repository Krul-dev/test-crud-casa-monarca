/*
 * Database initialization script for the CRUD application.
 *
 * Responsibilities:
 * - Create the application database if it does not exist.
 * - Create the tables required by the application.
 */

-- Create the application database.
CREATE DATABASE IF NOT EXISTS app
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- Select the application database.
USE app;

-- Create the users table.
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
