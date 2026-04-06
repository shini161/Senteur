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
TRUNCATE TABLE product_variant_images;
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
(6, 'Maison Francis Kurkdjian'),
(7, 'Roja Parfums'),
(8, 'Xerjoff'),
(9, 'Montale'),
(10, 'Electimuss'),
(11, 'Initio Parfums Privés'),
(12, 'Parfums de Marly');

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
(1, 'Bergamot', 'uploads/notes/bergamot.png'),
(2, 'Pepper', 'uploads/notes/pepper.png'),
(3, 'Lavender', 'uploads/notes/lavender.png'),
(4, 'Cedarwood', 'uploads/notes/cedarwood.png'),
(5, 'Vanilla', 'uploads/notes/vanilla.png'),
(6, 'Patchouli', 'uploads/notes/patchouli.png'),
(7, 'Jasmine', 'uploads/notes/jasmine.png'),
(8, 'Ambroxan', 'uploads/notes/ambroxan.png'),
(9, 'Pineapple', 'uploads/notes/pineapple.png'),
(10, 'Musk', 'uploads/notes/musk.png');

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
    family_name,
    name,
    slug,
    description,
    gender
) VALUES
(1, 1, 2, 'Dior Sauvage', 'Dior Sauvage Eau de Parfum', 'dior-sauvage-edp', 'Fresh spicy fragrance with bergamot, pepper and ambroxan.', 'male'),
(2, 2, 2, 'Bleu de Chanel', 'Bleu de Chanel Eau de Parfum', 'bleu-de-chanel-edp', 'Woody aromatic fragrance with citrus, incense and cedar.', 'male'),
(3, 3, 2, 'YSL Libre', 'YSL Libre Eau de Parfum', 'ysl-libre-edp', 'Floral lavender perfume with vanilla and musk.', 'female'),
(4, 4, 3, 'Creed Aventus', 'Creed Aventus Parfum', 'creed-aventus-parfum', 'Fruity smoky fragrance with pineapple, birch and musk.', 'male'),
(5, 5, 3, 'Tom Ford Oud Wood', 'Tom Ford Oud Wood Parfum', 'tom-ford-oud-wood-parfum', 'Warm woody fragrance with oud, vanilla and spices.', 'unisex'),
(6, 6, 2, 'Baccarat Rouge 540', 'Baccarat Rouge 540 Eau de Parfum', 'baccarat-rouge-540-edp', 'Amber floral fragrance with jasmine, saffron and cedar.', 'unisex'),
(7, 6, 3, 'Baccarat Rouge 540', 'Baccarat Rouge 540 Extrait de Parfum', 'baccarat-rouge-540-extrait', 'A richer and denser Baccarat Rouge 540 interpretation with deeper amber and musk facets.', 'unisex'),
(8, 7, 3, 'Roja Elysium', 'Roja Elysium Parfum Cologne', 'roja-elysium-parfum-cologne', 'Bright citrus aromatic fragrance with refined woods and musk.', 'male'),
(9, 7, 3, 'Roja Enigma', 'Roja Enigma Parfum Cologne', 'roja-enigma-parfum-cologne', 'Warm amber-spicy composition with vanilla, cognac and woods.', 'male'),
(10, 8, 2, 'Xerjoff Naxos', 'Xerjoff Naxos Eau de Parfum', 'xerjoff-naxos-edp', 'Honeyed tobacco fragrance with lavender, citrus and vanilla.', 'unisex'),
(11, 8, 2, 'Xerjoff Erba Pura', 'Xerjoff Erba Pura Eau de Parfum', 'xerjoff-erba-pura-edp', 'Fruity-musky fragrance with radiant sweetness and soft warmth.', 'unisex'),
(12, 9, 2, 'Montale Arabians Tonka', 'Montale Arabians Tonka Eau de Parfum', 'montale-arabians-tonka-edp', 'Dense amber-oud profile with rose, sugar and tonka facets.', 'unisex'),
(13, 10, 3, 'Mercurial Cashmere', 'Electimuss Mercurial Cashmere Parfum', 'electimuss-mercurial-cashmere-parfum', 'Smooth amber-woody fragrance with creamy vanilla and musk.', 'unisex'),
(14, 11, 2, 'Side Effect', 'Initio Side Effect Eau de Parfum', 'initio-side-effect-edp', 'Rich spicy blend with cinnamon, vanilla, tobacco and rum nuances.', 'unisex'),
(15, 12, 2, 'Layton', 'Parfums de Marly Layton Eau de Parfum', 'parfums-de-marly-layton-edp', 'Elegant sweet-spicy profile with apple, vanilla and woods.', 'male');

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
(1, 1, 60, 85.00, 100),
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
(12, 6, 200, 430.00, 5),

(13, 7, 70, 325.00, 10),
(14, 7, 200, 560.00, 4),

(15, 8, 100, 285.00, 9),
(16, 9, 100, 305.00, 7),
(17, 10, 100, 205.00, 11),

(18, 11, 50, 165.00, 22),
(19, 11, 100, 245.00, 13),

(20, 12, 100, 150.00, 16),

(21, 13, 100, 230.00, 9),

(22, 14, 50, 185.00, 17),
(23, 14, 90, 255.00, 10),

(24, 15, 75, 180.00, 19),
(25, 15, 125, 245.00, 11);

-- ======================
-- PRODUCT IMAGES
-- ======================
INSERT INTO product_images (
    id,
    product_id,
    image_url,
    position
) VALUES
(1, 1, 'uploads/products/dior-sauvage-eau-de-parfum.jpg', 0),
(2, 2, 'uploads/products/chanel-bleu-de-chanel-eau-de-parfum.jpg', 0),
(3, 3, 'uploads/products/ysl-libre-eau-de-parfum.jpg', 0),
(4, 4, 'uploads/products/creed-aventus-parfum.jpg', 0),
(5, 5, 'uploads/products/tom-ford-oud-wood-parfum.jpg', 0),
(6, 6, 'uploads/products/maison-francis-kurkdjian-baccarat-rouge-540-eau-de-parfum.jpg', 0),
(7, 7, 'uploads/products/maison-francis-kurkdjian-baccarat-rouge-540-extrait-de-parfum.jpg', 0),
(8, 8, 'uploads/products/roja-parfums-elysium-parfum-cologne.jpg', 0),
(9, 9, 'uploads/products/roja-parfums-enigma-parfum-cologne.jpg', 0),
(10, 10, 'uploads/products/xerjoff-naxos-eau-de-parfum.jpg', 0),
(11, 11, 'uploads/products/xerjoff-erba-pura-eau-de-parfum.jpg', 0),
(12, 12, 'uploads/products/montale-arabians-tonka-eau-de-parfum.jpg', 0),
(13, 13, 'uploads/products/electimuss-mercurial-cashmere-parfum.jpg', 0),
(14, 14, 'uploads/products/initio-side-effect-eau-de-parfum.jpg', 0),
(15, 15, 'uploads/products/parfums-de-marly-layton-eau-de-parfum.jpg', 0);

-- ======================
-- PRODUCT VARIANT IMAGES
-- ======================
INSERT INTO product_variant_images (
    id,
    product_variant_id,
    image_url,
    position
) VALUES
(1, 1, 'uploads/products/dior-sauvage-eau-de-parfum-60ml.jpg', 0),
(2, 2, 'uploads/products/dior-sauvage-eau-de-parfum-100ml.jpg', 0),

(3, 3, 'uploads/products/chanel-bleu-de-chanel-eau-de-parfum-50ml.jpg', 0),
(4, 4, 'uploads/products/chanel-bleu-de-chanel-eau-de-parfum-100ml.jpg', 0),

(5, 5, 'uploads/products/ysl-libre-eau-de-parfum-50ml.jpg', 0),
(6, 6, 'uploads/products/ysl-libre-eau-de-parfum-90ml.jpg', 0),

(7, 7, 'uploads/products/creed-aventus-parfum-50ml.jpg', 0),
(8, 8, 'uploads/products/creed-aventus-parfum-100ml.jpg', 0),

(9, 9, 'uploads/products/tom-ford-oud-wood-parfum-50ml.jpg', 0),
(10, 10, 'uploads/products/tom-ford-oud-wood-parfum-100ml.jpg', 0),

(11, 11, 'uploads/products/maison-francis-kurkdjian-baccarat-rouge-540-eau-de-parfum-70ml.jpg', 0),
(12, 12, 'uploads/products/maison-francis-kurkdjian-baccarat-rouge-540-eau-de-parfum-200ml.jpg', 0),

(13, 13, 'uploads/products/maison-francis-kurkdjian-baccarat-rouge-540-extrait-de-parfum-70ml.jpg', 0),
(14, 14, 'uploads/products/maison-francis-kurkdjian-baccarat-rouge-540-extrait-de-parfum-200ml.jpg', 0),

(15, 15, 'uploads/products/roja-parfums-elysium-parfum-cologne-100ml.jpg', 0),
(16, 16, 'uploads/products/roja-parfums-enigma-parfum-cologne-100ml.jpg', 0),
(17, 17, 'uploads/products/xerjoff-naxos-eau-de-parfum-100ml.jpg', 0),

(18, 18, 'uploads/products/xerjoff-erba-pura-eau-de-parfum-50ml.jpg', 0),
(19, 19, 'uploads/products/xerjoff-erba-pura-eau-de-parfum-100ml.jpg', 0),

(20, 20, 'uploads/products/montale-arabians-tonka-eau-de-parfum-100ml.jpg', 0),

(21, 21, 'uploads/products/electimuss-mercurial-cashmere-parfum-100ml.jpg', 0),

(22, 22, 'uploads/products/initio-side-effect-eau-de-parfum-50ml.jpg', 0),
(23, 23, 'uploads/products/initio-side-effect-eau-de-parfum-90ml.jpg', 0),

(24, 24, 'uploads/products/parfums-de-marly-layton-eau-de-parfum-75ml.jpg', 0),
(25, 25, 'uploads/products/parfums-de-marly-layton-eau-de-parfum-125ml.jpg', 0);

-- ======================
-- PRODUCT CATEGORIES
-- ======================
INSERT INTO product_categories (product_id, category_id) VALUES
(1, 1), (1, 6),
(2, 1), (2, 2),
(3, 3), (3, 6),
(4, 2), (4, 5),
(5, 2), (5, 4), (5, 5),
(6, 3), (6, 4), (6, 5),
(7, 4), (7, 5),
(8, 1), (8, 5),
(9, 4), (9, 5),
(10, 4), (10, 5),
(11, 3), (11, 4), (11, 5),
(12, 4), (12, 5),
(13, 2), (13, 4), (13, 5),
(14, 4), (14, 5),
(15, 2), (15, 4), (15, 5);

-- ======================
-- PRODUCT NOTES
-- ======================
INSERT INTO product_notes (product_id, note_id, note_type) VALUES
(1, 1, 'top'), (1, 2, 'middle'), (1, 8, 'base'),
(2, 1, 'top'), (2, 4, 'middle'), (2, 6, 'base'),
(3, 3, 'top'), (3, 7, 'middle'), (3, 5, 'base'),
(4, 9, 'top'), (4, 4, 'middle'), (4, 10, 'base'),
(5, 2, 'top'), (5, 4, 'middle'), (5, 5, 'base'),
(6, 7, 'top'), (6, 4, 'middle'), (6, 10, 'base'),
(7, 7, 'top'), (7, 8, 'middle'), (7, 10, 'base'),
(8, 1, 'top'), (8, 3, 'middle'), (8, 10, 'base'),
(9, 2, 'top'), (9, 5, 'middle'), (9, 6, 'base'),
(10, 3, 'top'), (10, 5, 'middle'), (10, 10, 'base'),
(11, 1, 'top'), (11, 7, 'middle'), (11, 10, 'base'),
(12, 7, 'top'), (12, 5, 'middle'), (12, 10, 'base'),
(13, 2, 'top'), (13, 5, 'middle'), (13, 10, 'base'),
(14, 2, 'top'), (14, 5, 'middle'), (14, 10, 'base'),
(15, 1, 'top'), (15, 5, 'middle'), (15, 4, 'base');

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
(3, 2, 3, 4, 'Very feminine', 'Sweet and floral, works really well in spring.'),
(4, 2, 6, 5, 'Airy and addictive', 'Sweet but radiant, projects beautifully.'),
(5, 1, 8, 4, 'Very uplifting', 'Bright citrus opening and elegant drydown.'),
(6, 2, 10, 5, 'Amazing tobacco honey blend', 'Comforting and luxurious, excellent in cooler weather.'),
(7, 1, 12, 4, 'Dense and sweet', 'Strong scent bubble, definitely for amber lovers.'),
(8, 2, 15, 5, 'Mass appealing but classy', 'Easy to wear, polished and smooth.'),
(9, 1, 14, 5, 'Rich and sexy', 'Deep sweet-spicy style with great character.');

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
(5, 'ORD0000005', 2, 3, 'cancelled', 95.00, 0.00, 95.00, NULL, NULL, NULL, NOW() - INTERVAL 3 DAY),
(6, 'ORD0000006', 1, 1, 'delivered', 205.00, 0.00, 205.00, NOW() - INTERVAL 15 DAY, NOW() - INTERVAL 13 DAY, NOW() - INTERVAL 10 DAY, NOW() - INTERVAL 15 DAY),
(7, 'ORD0000007', 2, 3, 'delivered', 180.00, 0.00, 180.00, NOW() - INTERVAL 12 DAY, NOW() - INTERVAL 9 DAY, NOW() - INTERVAL 6 DAY, NOW() - INTERVAL 12 DAY),
(8, 'ORD0000008', 1, 2, 'delivered', 185.00, 0.00, 185.00, NOW() - INTERVAL 8 DAY, NOW() - INTERVAL 6 DAY, NOW() - INTERVAL 3 DAY, NOW() - INTERVAL 8 DAY);

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
(1, 1, 1, 'Dior Sauvage Eau de Parfum', 60, 2, 85.00),
(2, 1, 4, 'Bleu de Chanel Eau de Parfum', 100, 1, 130.00),

(3, 2, 11, 'Baccarat Rouge 540 Eau de Parfum', 70, 1, 245.00),

(4, 3, 9, 'Tom Ford Oud Wood Parfum', 50, 1, 210.00),

(5, 4, 12, 'Baccarat Rouge 540 Eau de Parfum', 200, 1, 430.00),

(6, 5, 5, 'YSL Libre Eau de Parfum', 50, 1, 95.00),

(7, 6, 17, 'Xerjoff Naxos Eau de Parfum', 100, 1, 205.00),

(8, 7, 24, 'Parfums de Marly Layton Eau de Parfum', 75, 1, 180.00),

(9, 8, 22, 'Initio Side Effect Eau de Parfum', 50, 1, 185.00);

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
),
(
    6,
    6,
    'stripe',
    JSON_OBJECT(
        'event', 'checkout.session.completed',
        'payment_intent', 'pi_mock_paid_6',
        'note', 'Seeded delivered order payment'
    ),
    'paid',
    205.00,
    'EUR',
    'cs_test_mock_paid_6',
    'pi_mock_paid_6',
    NOW() - INTERVAL 15 DAY,
    NOW() - INTERVAL 15 DAY
),
(
    7,
    7,
    'stripe',
    JSON_OBJECT(
        'event', 'checkout.session.completed',
        'payment_intent', 'pi_mock_paid_7',
        'note', 'Seeded delivered order payment'
    ),
    'paid',
    180.00,
    'EUR',
    'cs_test_mock_paid_7',
    'pi_mock_paid_7',
    NOW() - INTERVAL 12 DAY,
    NOW() - INTERVAL 12 DAY
),
(
    8,
    8,
    'stripe',
    JSON_OBJECT(
        'event', 'checkout.session.completed',
        'payment_intent', 'pi_mock_paid_8',
        'note', 'Seeded delivered order payment'
    ),
    'paid',
    185.00,
    'EUR',
    'cs_test_mock_paid_8',
    'pi_mock_paid_8',
    NOW() - INTERVAL 8 DAY,
    NOW() - INTERVAL 8 DAY
);