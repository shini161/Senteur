CREATE DATABASE IF NOT EXISTS senteur;
USE senteur;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ======================
-- SCHEMA
-- ======================

CREATE TABLE brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE fragrance_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL
);

-- ======================
-- INDEXES
-- ======================

-- ======================

SET FOREIGN_KEY_CHECKS = 1;