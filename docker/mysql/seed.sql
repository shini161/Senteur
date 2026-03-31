SET FOREIGN_KEY_CHECKS = 0;

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
-- CATEGORIES
-- ======================
INSERT INTO categories (name) VALUES
('Fresh'),
('Woody'),
('Sweet'),
('Spicy'),
('Citrus');

-- ======================
-- NOTES
-- ======================
INSERT INTO notes (name, image_url) VALUES
('Bergamot', 'notes/bergamot.png'),
('Lemon', 'notes/lemon.png'),
('Lavender', 'notes/lavender.png'),
('Amber', 'notes/amber.png'),
('Vanilla', 'notes/vanilla.png'),
('Sandalwood', 'notes/sandalwood.png'),
('Patchouli', 'notes/patchouli.png');

-- ======================
-- PRODUCTS
-- ======================
INSERT INTO products (brand_id, fragrance_type_id, name, slug, description, gender)
VALUES
(1, 2, 'Dior Sauvage', 'dior-sauvage', 'Fresh spicy fragrance with bergamot and ambroxan.', 'male'),
(2, 2, 'Chanel Bleu de Chanel', 'bleu-de-chanel', 'Woody aromatic fragrance, elegant and timeless.', 'male'),
(3, 2, 'YSL Libre', 'ysl-libre', 'Floral lavender perfume with orange blossom.', 'female'),
(4, 3, 'Creed Aventus', 'creed-aventus', 'Fruity smoky fragrance with pineapple and birch.', 'male');

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
(1, 'products/sauvage_1.jpg', 0),
(1, 'products/sauvage_2.jpg', 1),

(2, 'products/bleu_1.jpg', 0),
(2, 'products/bleu_2.jpg', 1),

(3, 'products/libre_1.jpg', 0),
(3, 'products/libre_2.jpg', 1),

(4, 'products/aventus_1.jpg', 0),
(4, 'products/aventus_2.jpg', 1);

-- ======================
-- PRODUCT CATEGORIES
-- ======================
INSERT INTO product_categories (product_id, category_id)
VALUES
(1, 1), -- Fresh
(1, 4), -- Spicy

(2, 2), -- Woody
(2, 1), -- Fresh

(3, 3), -- Sweet

(4, 2), -- Woody
(4, 3); -- Sweet

-- ======================
-- PRODUCT NOTES
-- ======================
INSERT INTO product_notes (product_id, note_id, note_type)
VALUES
-- Sauvage
(1, 1, 'top'),
(1, 3, 'middle'),
(1, 4, 'base'),

-- Bleu
(2, 1, 'top'),
(2, 3, 'middle'),
(2, 6, 'base'),

-- Libre
(3, 3, 'middle'),
(3, 5, 'base'),

-- Aventus
(4, 2, 'top'),
(4, 7, 'base');

SET FOREIGN_KEY_CHECKS = 1;