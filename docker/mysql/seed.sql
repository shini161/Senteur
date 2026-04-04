USE senteur;

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

SET FOREIGN_KEY_CHECKS = 1;

-- ======================
-- BRANDS
-- ======================
INSERT INTO brands (id, name) VALUES
(1, 'Dior'),
(2, 'Chanel'),
(3, 'Yves Saint Laurent'),
(4, 'Creed'),
(5, 'Tom Ford'),
(6, 'Maison Francis Kurkdjian');

-- ======================
-- FRAGRANCE TYPES
-- ======================
INSERT INTO fragrance_types (id, name) VALUES
(1, 'Eau de Toilette'),
(2, 'Eau de Parfum'),
(3, 'Parfum');

-- ======================
-- CATEGORIES
-- ======================
INSERT INTO categories (id, name) VALUES
(1, 'Fresh'),
(2, 'Woody'),
(3, 'Floral'),
(4, 'Amber'),
(5, 'Luxury'),
(6, 'Everyday');

-- ======================
-- NOTES
-- ======================
INSERT INTO notes (id, name, image_url) VALUES
(1, 'Bergamot', 'notes/bergamot.png'),
(2, 'Pepper', 'notes/pepper.png'),
(3, 'Lavender', 'notes/lavender.png'),
(4, 'Cedarwood', 'notes/cedarwood.png'),
(5, 'Vanilla', 'notes/vanilla.png'),
(6, 'Patchouli', 'notes/patchouli.png'),
(7, 'Jasmine', 'notes/jasmine.png'),
(8, 'Ambroxan', 'notes/ambroxan.png'),
(9, 'Pineapple', 'notes/pineapple.png'),
(10, 'Musk', 'notes/musk.png');

-- ======================
-- USERS
-- password for all seeded users: password123
-- ======================
INSERT INTO users (
    id,
    public_id,
    role,
    username,
    email,
    phone,
    password_hash
) VALUES
(1, 'USR0000001', 'user', 'mario', 'mario@example.com', '+391111111111', '$2y$12$6Be1de/LGhApjxRtWdOTk.a3GfCf3qXIF1uxl2hxlFksRHhcKiMOe'),
(2, 'USR0000002', 'user', 'giulia', 'giulia@example.com', '+392222222222', '$2y$12$6Be1de/LGhApjxRtWdOTk.a3GfCf3qXIF1uxl2hxlFksRHhcKiMOe'),
(3, 'USR0000003', 'admin', 'admin', 'admin@example.com', '+393333333333', '$2y$12$6Be1de/LGhApjxRtWdOTk.a3GfCf3qXIF1uxl2hxlFksRHhcKiMOe');

-- ======================
-- USER ADDRESSES
-- ======================
INSERT INTO user_addresses (
    id,
    user_id,
    full_name,
    address_line,
    city,
    postal_code,
    country,
    is_default
) VALUES
(1, 1, 'Mario Rossi', 'Via Roma 10', 'Milan', '20100', 'Italy', TRUE),
(2, 1, 'Mario Rossi', 'Corso Buenos Aires 24', 'Milan', '20124', 'Italy', FALSE),
(3, 2, 'Giulia Bianchi', 'Via Toledo 88', 'Naples', '80134', 'Italy', TRUE);

-- ======================
-- PRODUCTS
-- ======================
INSERT INTO products (
    id,
    brand_id,
    fragrance_type_id,
    name,
    slug,
    description,
    gender
) VALUES
(1, 1, 2, 'Dior Sauvage', 'dior-sauvage', 'Fresh spicy fragrance with bergamot, pepper and ambroxan.', 'male'),
(2, 2, 2, 'Bleu de Chanel', 'bleu-de-chanel', 'Woody aromatic fragrance with citrus, incense and cedar.', 'male'),
(3, 3, 2, 'YSL Libre', 'ysl-libre', 'Floral lavender perfume with vanilla and musk.', 'female'),
(4, 4, 3, 'Creed Aventus', 'creed-aventus', 'Fruity smoky fragrance with pineapple, birch and musk.', 'male'),
(5, 5, 3, 'Tom Ford Oud Wood', 'tom-ford-oud-wood', 'Warm woody fragrance with oud, vanilla and spices.', 'unisex'),
(6, 6, 2, 'Baccarat Rouge 540', 'baccarat-rouge-540', 'Amber floral fragrance with jasmine, saffron and cedar.', 'unisex');

-- ======================
-- PRODUCT VARIANTS
-- ======================
INSERT INTO product_variants (
    id,
    product_id,
    size_ml,
    price,
    stock
) VALUES
(1, 1, 50, 85.00, 100),
(2, 1, 100, 120.00, 50),

(3, 2, 50, 90.00, 80),
(4, 2, 100, 130.00, 40),

(5, 3, 50, 95.00, 70),
(6, 3, 90, 140.00, 30),

(7, 4, 50, 220.00, 20),
(8, 4, 100, 320.00, 10),

(9, 5, 50, 210.00, 15),
(10, 5, 100, 305.00, 8),

(11, 6, 70, 245.00, 12),
(12, 6, 200, 430.00, 5);

-- ======================
-- PRODUCT IMAGES
-- ======================
INSERT INTO product_images (
    id,
    product_id,
    image_url,
    position
) VALUES
(1, 1, 'products/sauvage.jpg', 0),
(2, 2, 'products/bleu.jpg', 0),
(3, 3, 'products/libre.jpg', 0),
(4, 4, 'products/aventus.jpg', 0),
(5, 5, 'products/oud-wood.jpg', 0),
(6, 6, 'products/baccarat-rouge-540.jpg', 0);

-- ======================
-- PRODUCT CATEGORIES
-- ======================
INSERT INTO product_categories (product_id, category_id) VALUES
(1, 1),
(1, 6),
(2, 1),
(2, 2),
(3, 3),
(3, 6),
(4, 2),
(4, 5),
(5, 2),
(5, 4),
(5, 5),
(6, 3),
(6, 4),
(6, 5);

-- ======================
-- PRODUCT NOTES
-- ======================
INSERT INTO product_notes (product_id, note_id, note_type) VALUES
(1, 1, 'top'),
(1, 2, 'middle'),
(1, 8, 'base'),

(2, 1, 'top'),
(2, 4, 'middle'),
(2, 6, 'base'),

(3, 3, 'top'),
(3, 7, 'middle'),
(3, 5, 'base'),

(4, 9, 'top'),
(4, 4, 'middle'),
(4, 10, 'base'),

(5, 2, 'top'),
(5, 4, 'middle'),
(5, 5, 'base'),

(6, 7, 'top'),
(6, 4, 'middle'),
(6, 10, 'base');

-- ======================
-- REVIEWS
-- ======================
INSERT INTO reviews (
    id,
    user_id,
    product_id,
    rating,
    title,
    comment
) VALUES
(1, 1, 1, 5, 'Excellent daily wear', 'Fresh, loud enough, and easy to wear.'),
(2, 1, 4, 5, 'Signature scent material', 'Very strong and elegant, great performance.'),
(3, 2, 3, 4, 'Very feminine', 'Sweet and floral, works really well in spring.');

-- ======================
-- CARTS
-- ======================
INSERT INTO carts (
    id,
    session_id,
    user_id,
    expires_at
) VALUES
(1, NULL, 1, DATE_ADD(NOW(), INTERVAL 7 DAY)),
(2, NULL, 2, DATE_ADD(NOW(), INTERVAL 7 DAY));

-- ======================
-- CART ITEMS
-- ======================
INSERT INTO cart_items (
    cart_id,
    product_variant_id,
    quantity
) VALUES
(1, 2, 1),
(1, 5, 2),
(2, 11, 1);

-- ======================
-- ORDERS
-- ======================
INSERT INTO orders (
    id,
    public_id,
    user_id,
    shipping_address_id,
    status,
    subtotal_amount,
    shipping_cost,
    total_amount,
    paid_at,
    shipped_at,
    delivered_at,
    created_at
) VALUES
(1, 'ORD0000001', 1, 1, 'processing', 300.00, 0.00, 300.00, NOW() - INTERVAL 5 DAY, NULL, NULL, NOW() - INTERVAL 5 DAY),
(2, 'ORD0000002', 1, 2, 'shipped', 245.00, 0.00, 245.00, NOW() - INTERVAL 10 DAY, NOW() - INTERVAL 7 DAY, NULL, NOW() - INTERVAL 10 DAY),
(3, 'ORD0000003', 1, 1, 'delivered', 210.00, 0.00, 210.00, NOW() - INTERVAL 20 DAY, NOW() - INTERVAL 17 DAY, NOW() - INTERVAL 14 DAY, NOW() - INTERVAL 20 DAY),
(4, 'ORD0000004', 2, 3, 'pending', 430.00, 0.00, 430.00, NULL, NULL, NULL, NOW() - INTERVAL 1 DAY),
(5, 'ORD0000005', 2, 3, 'cancelled', 95.00, 0.00, 95.00, NULL, NULL, NULL, NOW() - INTERVAL 3 DAY);

-- ======================
-- ORDER ITEMS
-- ======================
INSERT INTO order_items (
    id,
    order_id,
    product_variant_id,
    product_name_snapshot,
    size_ml_snapshot,
    quantity,
    price_at_purchase
) VALUES
(1, 1, 1, 'Dior Sauvage', 50, 2, 85.00),
(2, 1, 4, 'Bleu de Chanel', 100, 1, 130.00),

(3, 2, 11, 'Baccarat Rouge 540', 70, 1, 245.00),

(4, 3, 9, 'Tom Ford Oud Wood', 50, 1, 210.00),

(5, 4, 12, 'Baccarat Rouge 540', 200, 1, 430.00),

(6, 5, 5, 'YSL Libre', 50, 1, 95.00);

-- ======================
-- PAYMENTS
-- ======================
INSERT INTO payments (
    id,
    order_id,
    provider,
    provider_payload,
    status,
    amount,
    currency,
    stripe_session_id,
    transaction_id,
    paid_at,
    created_at
) VALUES
(
    1,
    1,
    'stripe',
    JSON_OBJECT(
        'event', 'checkout.session.completed',
        'payment_intent', 'pi_mock_paid_1',
        'note', 'Seeded paid payment'
    ),
    'paid',
    300.00,
    'EUR',
    'cs_test_mock_paid_1',
    'pi_mock_paid_1',
    NOW() - INTERVAL 5 DAY,
    NOW() - INTERVAL 5 DAY
),
(
    2,
    2,
    'stripe',
    JSON_OBJECT(
        'event', 'checkout.session.completed',
        'payment_intent', 'pi_mock_paid_2',
        'note', 'Seeded shipped order payment'
    ),
    'paid',
    245.00,
    'EUR',
    'cs_test_mock_paid_2',
    'pi_mock_paid_2',
    NOW() - INTERVAL 10 DAY,
    NOW() - INTERVAL 10 DAY
),
(
    3,
    3,
    'stripe',
    JSON_OBJECT(
        'event', 'checkout.session.completed',
        'payment_intent', 'pi_mock_paid_3',
        'note', 'Seeded delivered order payment'
    ),
    'paid',
    210.00,
    'EUR',
    'cs_test_mock_paid_3',
    'pi_mock_paid_3',
    NOW() - INTERVAL 20 DAY,
    NOW() - INTERVAL 20 DAY
),
(
    4,
    4,
    'stripe',
    JSON_OBJECT(
        'event', 'checkout.session.created',
        'note', 'Seeded pending payment'
    ),
    'pending',
    430.00,
    'EUR',
    'cs_test_mock_pending_4',
    NULL,
    NULL,
    NOW() - INTERVAL 1 DAY
),
(
    5,
    5,
    'stripe',
    JSON_OBJECT(
        'event', 'checkout.session.expired',
        'note', 'Seeded failed payment'
    ),
    'failed',
    95.00,
    'EUR',
    'cs_test_mock_failed_5',
    NULL,
    NULL,
    NOW() - INTERVAL 3 DAY
);