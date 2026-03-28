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

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,

    public_id CHAR(10) UNIQUE NOT NULL,

    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',

    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20) NULL,
    password_hash TEXT NOT NULL,

    email_verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,

    brand_id INT NOT NULL,
    fragrance_type_id INT NULL,

    name VARCHAR(150) NOT NULL,
    slug VARCHAR(180) UNIQUE NOT NULL,
    description TEXT,
    gender ENUM('male', 'female', 'unisex') NOT NULL,

    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE RESTRICT,
    FOREIGN KEY (fragrance_type_id) REFERENCES fragrance_types(id),

    FULLTEXT INDEX idx_products_name_description (name, description)
);

CREATE TABLE product_variants (
    id INT AUTO_INCREMENT PRIMARY KEY,

    product_id INT NOT NULL,

    size_ml INT NOT NULL,
    price DECIMAL(10,2) NOT NULL CHECK (price >= 0),
    stock INT NOT NULL CHECK (stock >= 0),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,

    UNIQUE (product_id, size_ml)
);

CREATE TABLE product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,

    product_id INT NOT NULL,

    image_url VARCHAR(255) NOT NULL,
    position INT NOT NULL DEFAULT 0,

    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,

    UNIQUE (product_id, position)
);

-- ======================
-- INDEXES
-- ======================

-- ======================

SET FOREIGN_KEY_CHECKS = 1;