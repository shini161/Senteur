SET FOREIGN_KEY_CHECKS = 0;

-- ======================
-- RESET
-- ======================
TRUNCATE TABLE cart_items;
TRUNCATE TABLE carts;
TRUNCATE TABLE payments;
TRUNCATE TABLE order_items;
TRUNCATE TABLE orders;
TRUNCATE TABLE reviews;
TRUNCATE TABLE user_addresses;
TRUNCATE TABLE product_notes;
TRUNCATE TABLE product_categories;
TRUNCATE TABLE product_images;
TRUNCATE TABLE product_variants;
TRUNCATE TABLE products;
TRUNCATE TABLE notes;
TRUNCATE TABLE categories;
TRUNCATE TABLE users;
TRUNCATE TABLE fragrance_types;
TRUNCATE TABLE brands;

-- ======================
-- BRANDS
-- ======================
INSERT INTO brands (name) VALUES
('Dior'),
('Chanel'),
('Yves Saint Laurent'),
('Creed');

-- ======================
-- FRAGRANCE TYPES
-- ======================
INSERT INTO fragrance_types (name) VALUES
('Eau de Toilette'),
('Eau de Parfum'),
('Parfum');

-- ======================
-- USERS
-- ======================
INSERT INTO users (public_id, role, username, email, phone, password_hash)
VALUES
('USR0000001', 'user', 'mario', 'mario@example.com', '+391111111111', '$2y$10$examplehash1');

-- ======================
-- USER ADDRESSES (FIXED)
-- ======================
INSERT INTO user_addresses (
    user_id,
    full_name,
    address_line,
    city,
    postal_code,
    country,
    is_default
)
VALUES
(1, 'Mario Rossi', 'Via Roma 10', 'Milan', '20100', 'Italy', TRUE);

-- ======================
-- PRODUCTS
-- ======================
INSERT INTO products (brand_id, fragrance_type_id, name, slug, description, gender)
VALUES
(1, 2, 'Dior Sauvage', 'dior-sauvage', 'Fresh spicy fragrance.', 'male'),
(2, 2, 'Bleu de Chanel', 'bleu-de-chanel', 'Woody aromatic fragrance.', 'male'),
(3, 2, 'YSL Libre', 'ysl-libre', 'Floral lavender perfume.', 'female'),
(4, 3, 'Creed Aventus', 'creed-aventus', 'Fruity smoky fragrance.', 'male');

-- ======================
-- VARIANTS
-- ======================
INSERT INTO product_variants (product_id, size_ml, price, stock)
VALUES
(1, 50, 85.00, 100),
(1, 100, 120.00, 50),

(2, 50, 90.00, 80),
(2, 100, 130.00, 40),

(3, 50, 95.00, 70),
(3, 90, 140.00, 30),

(4, 50, 220.00, 20),
(4, 100, 320.00, 10);

-- ======================
-- IMAGES
-- ======================
INSERT INTO product_images (product_id, image_url, position)
VALUES
(1, 'products/sauvage.jpg', 0),
(2, 'products/bleu.jpg', 0),
(3, 'products/libre.jpg', 0),
(4, 'products/aventus.jpg', 0);

-- ======================
-- CARTS
-- ======================
INSERT INTO carts (session_id, user_id, expires_at)
VALUES
(NULL, 1, DATE_ADD(NOW(), INTERVAL 7 DAY));

-- ======================
-- CART ITEMS
-- ======================
INSERT INTO cart_items (cart_id, product_variant_id, quantity)
VALUES
(1, 1, 2),
(1, 4, 1);

-- ======================
-- ORDERS
-- ======================
INSERT INTO orders (
    public_id,
    user_id,
    shipping_address_id,
    status,
    subtotal_amount,
    shipping_cost,
    total_amount,
    paid_at
)
VALUES
('ORD0000001', 1, 1, 'processing', 300.00, 0.00, 300.00, NOW());

-- ======================
-- ORDER ITEMS
-- ======================
INSERT INTO order_items (
    order_id,
    product_variant_id,
    product_name_snapshot,
    size_ml_snapshot,
    quantity,
    price_at_purchase
)
VALUES
(1, 1, 'Dior Sauvage', 50, 2, 85.00),
(1, 4, 'Bleu de Chanel', 100, 1, 130.00);

-- ======================
-- PAYMENTS
-- ======================
INSERT INTO payments (
    order_id,
    provider,
    provider_payload,
    status,
    amount,
    currency,
    transaction_id,
    paid_at
)
VALUES
(
    1,
    'stripe',
    JSON_OBJECT('payment_intent', 'pi_mock'),
    'paid',
    300.00,
    'EUR',
    'txn_mock',
    NOW()
);

SET FOREIGN_KEY_CHECKS = 1;