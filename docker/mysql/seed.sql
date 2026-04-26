-- Demo seed data for local development and portfolio screenshots.
USE senteur;

SET NAMES utf8mb4;

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
(5, 'Citrus'),
(6, 'Aromatic'),
(7, 'Fruity'),
(8, 'Spicy'),
(9, 'Sweet'),
(10, 'Gourmand'),
(11, 'Musky'),
(12, 'Oud'),
(13, 'Green'),
(14, 'Tobacco'),
(15, 'Smoky');

-- ======================
-- NOTES
-- ======================
INSERT INTO notes (id, name, slug, image_url) VALUES
(1, 'Bergamot', 'bergamot', 'uploads/notes/bergamot.webp'),
(2, 'Pepper', 'pepper', 'uploads/notes/pepper.webp'),
(3, 'Lavender', 'lavender', 'uploads/notes/lavender.webp'),
(4, 'Cedar', 'cedar', 'uploads/notes/cedar.webp'),
(5, 'Vanilla', 'vanilla', 'uploads/notes/vanilla.webp'),
(6, 'Patchouli', 'patchouli', 'uploads/notes/patchouli.webp'),
(7, 'Jasmine', 'jasmine', 'uploads/notes/jasmine.webp'),
(8, 'Ambroxan', 'ambroxan', 'uploads/notes/ambroxan.webp'),
(9, 'Pineapple', 'pineapple', 'uploads/notes/pineapple.webp'),
(10, 'Musk', 'musk', 'uploads/notes/musk.webp'),
(11, 'Apple', 'apple', 'uploads/notes/apple.webp'),
(12, 'Mandarin Orange', 'mandarin-orange', 'uploads/notes/mandarin-orange.webp'),
(13, 'Geranium', 'geranium', 'uploads/notes/geranium.webp'),
(14, 'Violet', 'violet', 'uploads/notes/violet.webp'),
(15, 'Cardamom', 'cardamom', 'uploads/notes/cardamom.webp'),
(16, 'Sandalwood', 'sandalwood', 'uploads/notes/sandalwood.webp'),
(17, 'Guaiac Wood', 'guaiac-wood', 'uploads/notes/guaiac-wood.webp'),
(18, 'Coumarin', 'coumarin', 'uploads/notes/coumarin.webp'),
(19, 'Ambermax', 'ambermax', 'uploads/notes/ambermax.webp'),
(20, 'Pink Pepper', 'pink-pepper', 'uploads/notes/pink-pepper.webp'),
(21, 'Tuberose', 'tuberose', 'uploads/notes/tuberose.webp'),
(22, 'Iris', 'iris', 'uploads/notes/iris.webp'),
(23, 'Ambergris', 'ambergris', 'uploads/notes/ambergris.webp'),
(24, 'Caramel', 'caramel', 'uploads/notes/caramel.webp'),
(25, 'Cashmere Wood', 'cashmere-wood', 'uploads/notes/cashmere-wood.webp'),
(26, 'Tonka Bean', 'tonka-bean', 'uploads/notes/tonka-bean.webp'),
(27, 'Agarwood (Oud)', 'agarwood-oud', 'uploads/notes/agarwood-oud.webp'),
(28, 'Saffron', 'saffron', 'uploads/notes/saffron.webp'),
(29, 'Bulgarian Rose', 'bulgarian-rose', 'uploads/notes/bulgarian-rose.webp'),
(30, 'Sugar', 'sugar', 'uploads/notes/sugar.webp'),
(31, 'Amber', 'amber', 'uploads/notes/amber.webp'),
(32, 'Oakmoss', 'oakmoss', 'uploads/notes/oakmoss.webp'),
(33, 'Sicilian Orange', 'sicilian-orange', 'uploads/notes/sicilian-orange.webp'),
(34, 'Lemon', 'lemon', 'uploads/notes/lemon.webp'),
(35, 'Fruits', 'fruits', 'uploads/notes/fruits.webp'),
(36, 'Honey', 'honey', 'uploads/notes/honey.webp'),
(37, 'Cinnamon', 'cinnamon', 'uploads/notes/cinnamon.webp'),
(38, 'Cashmeran', 'cashmeran', 'uploads/notes/cashmeran.webp'),
(39, 'Tobacco Leaf', 'tobacco-leaf', 'uploads/notes/tobacco-leaf.webp'),
(40, 'Grapefruit', 'grapefruit', 'uploads/notes/grapefruit.webp'),
(41, 'Lime', 'lime', 'uploads/notes/lime.webp'),
(42, 'Artemisia', 'artemisia', 'uploads/notes/artemisia.webp'),
(43, 'Thyme', 'thyme', 'uploads/notes/thyme.webp'),
(44, 'Black Currant', 'black-currant', 'uploads/notes/black-currant.webp'),
(45, 'Orange Blossom', 'orange-blossom', 'uploads/notes/orange-blossom.webp'),
(46, 'Lily of the Valley', 'lily-of-the-valley', 'uploads/notes/lily-of-the-valley.webp'),
(47, 'Rose de Mai', 'rose-de-mai', 'uploads/notes/rose-de-mai.webp'),
(48, 'Juniper Berry', 'juniper-berry', 'uploads/notes/juniper-berry.webp'),
(49, 'Vetiver', 'vetiver', 'uploads/notes/vetiver.webp'),
(50, 'Galbanum', 'galbanum', 'uploads/notes/galbanum.webp'),
(51, 'Leather', 'leather', 'uploads/notes/leather.webp'),
(52, 'Labdanum', 'labdanum', 'uploads/notes/labdanum.webp'),
(53, 'Benzoin', 'benzoin', 'uploads/notes/benzoin.webp'),
(54, 'Heliotrope', 'heliotrope', 'uploads/notes/heliotrope.webp'),
(55, 'Ginger', 'ginger', 'uploads/notes/ginger.webp'),
(56, 'Neroli', 'neroli', 'uploads/notes/neroli.webp'),
(57, 'Cognac', 'cognac', 'uploads/notes/cognac.webp'),
(58, 'Bitter Almond', 'bitter-almond', 'uploads/notes/bitter-almond.webp'),
(59, 'Egyptian Jasmine', 'egyptian-jasmine', 'uploads/notes/egyptian-jasmine.webp'),
(60, 'Woody Notes', 'woody-notes', 'uploads/notes/woody-notes.webp'),
(61, 'Amberwood', 'amberwood', 'uploads/notes/amberwood.webp'),
(62, 'Hedione', 'hedione', 'uploads/notes/hedione.webp'),
(63, 'Fir Resin', 'fir-resin', 'uploads/notes/fir-resin.webp'),
(64, 'Petitgrain', 'petitgrain', 'uploads/notes/petitgrain.webp'),
(65, 'Mint', 'mint', 'uploads/notes/mint.webp'),
(66, 'Aldehydes', 'aldehydes', 'uploads/notes/aldehydes.webp'),
(67, 'Coriander', 'coriander', 'uploads/notes/coriander.webp'),
(68, 'Nutmeg', 'nutmeg', 'uploads/notes/nutmeg.webp'),
(69, 'Melon', 'melon', 'uploads/notes/melon.webp'),
(70, 'Incense', 'incense', 'uploads/notes/incense.webp'),
(71, 'Sichuan Pepper', 'sichuan-pepper', 'uploads/notes/sichuan-pepper.webp'),
(72, 'Star Anise', 'star-anise', 'uploads/notes/star-anise.webp'),
(73, 'Birch', 'birch', 'uploads/notes/birch.webp'),
(74, 'Tobacco', 'tobacco', 'uploads/notes/tobacco.webp'),
(75, 'White Musk', 'white-musk', 'uploads/notes/white-musk.webp'),
(76, 'Madagascar Vanilla', 'madagascar-vanilla', 'uploads/notes/madagascar-vanilla.webp'),
(77, 'Calabrian Bergamot', 'calabrian-bergamot', 'uploads/notes/calabrian-bergamot.webp'),
(78, 'Sicilian Lemon', 'sicilian-lemon', 'uploads/notes/sicilian-lemon.webp'),
(79, 'Jasmine Sambac', 'jasmine-sambac', 'uploads/notes/jasmine-sambac.webp'),
(80, 'Virginia Cedar', 'virginia-cedar', 'uploads/notes/virginia-cedar.webp'),
(81, 'Brazilian Rosewood', 'brazilian-rosewood', 'uploads/notes/brazilian-rosewood.webp'),
(82, 'Rum', 'rum', 'uploads/notes/rum.webp'),
(83, 'Cypriol Oil', 'cypriol-oil', 'uploads/notes/cypriol-oil.webp'),
(84, 'Rose', 'rose', 'uploads/notes/rose.webp'),
(85, 'Moroccan Jasmine', 'moroccan-jasmine', 'uploads/notes/moroccan-jasmine.webp'),
(86, 'Sugar Cane', 'sugar-cane', 'uploads/notes/sugar-cane.webp');

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
    concentration_label,
    slug,
    description,
    gender
) VALUES
(1, 1, 2, 'Dior Sauvage', 'Sauvage', 'Eau de Parfum', 'dior-sauvage-edp', 'Sauvage Eau de Parfum by Dior is a Oriental Fougere fragrance for men. Sauvage Eau de Parfum was launched in 2018. The nose behind this fragrance is François Demachy.', 'male'),
(2, 2, 2, 'Bleu de Chanel', 'Bleu de Chanel', 'Eau de Parfum', 'bleu-de-chanel-edp', 'Bleu de Chanel Eau de Parfum by Chanel is a Woody Aromatic fragrance for men. Bleu de Chanel Eau de Parfum was launched in 2014. The nose behind this fragrance is Jacques Polge.', 'male'),
(3, 3, 2, 'Libre', 'Libre', 'Eau de Parfum', 'ysl-libre-edp', 'Libre by Yves Saint Laurent is a Oriental Fougere fragrance for women. Libre was launched in 2019. Libre was created by Anne Flipo and Carlos Benaïm.', 'female'),
(4, 4, 3, 'Aventus', 'Aventus', 'Parfum', 'creed-aventus-parfum', 'Aventus by Creed is a Chypre Fruity fragrance for men. Aventus was launched in 2010. Aventus was created by Jean-Christophe Hérault and Erwin Creed.', 'male'),
(5, 5, 3, 'Oud Wood', 'Oud Wood', 'Parfum', 'tom-ford-oud-wood-parfum', 'Oud Wood by Tom Ford is a Oriental Woody fragrance for women and men. Oud Wood was launched in 2007. The nose behind this fragrance is Richard Herpin.', 'unisex'),
(6, 6, 2, 'Baccarat Rouge 540', 'Baccarat Rouge 540', 'Eau de Parfum', 'baccarat-rouge-540-edp', 'Baccarat Rouge 540 by Maison Francis Kurkdjian is a Oriental Floral fragrance for women and men. Baccarat Rouge 540 was launched in 2015. The nose behind this fragrance is Francis Kurkdjian.', 'unisex'),
(7, 6, 3, 'Baccarat Rouge 540', 'Baccarat Rouge 540', 'Extrait de Parfum', 'baccarat-rouge-540-extrait', 'Baccarat Rouge 540 Extrait de Parfum by Maison Francis Kurkdjian is a Oriental Floral fragrance for women and men. Baccarat Rouge 540 Extrait de Parfum was launched in 2017. The nose behind this fragrance is Francis Kurkdjian.', 'unisex'),
(8, 7, 3, 'Elysium', 'Elysium', 'Parfum Cologne', 'roja-elysium-parfum-cologne', 'Elysium Pour Homme Parfum Cologne by Roja Dove is a Aromatic Fougere fragrance for men. Elysium Pour Homme Parfum Cologne was launched in 2017. The nose behind this fragrance is Roja.', 'male'),
(9, 7, 3, 'Enigma', 'Enigma', 'Parfum Cologne', 'roja-enigma-parfum-cologne', 'Enigma Pour Homme Parfum Cologne by Roja Dove is a Oriental Spicy fragrance for men. Enigma Pour Homme Parfum Cologne was launched in 2019. The nose behind this fragrance is Roja.', 'male'),
(10, 8, 2, 'Naxos', 'Naxos', 'Eau de Parfum', 'xerjoff-naxos-edp', 'XJ 1861 Naxos by Xerjoff is a Citrus Gourmand fragrance for women and men. XJ 1861 Naxos was launched in 2015.', 'unisex'),
(11, 8, 2, 'Erba Pura', 'Erba Pura', 'Eau de Parfum', 'xerjoff-erba-pura-edp', 'Erba Pura by Xerjoff is a Oriental fragrance for women and men. Erba Pura was launched in 2019. Erba Pura was created by Christian Carbonnel and Laura Santander.', 'unisex'),
(12, 9, 2, 'Arabians Tonka', 'Arabians Tonka', 'Eau de Parfum', 'montale-arabians-tonka-edp', 'Arabians Tonka by Montale is a Oriental Woody fragrance for women and men. Arabians Tonka was launched in 2019.', 'unisex'),
(13, 10, 3, 'Mercurial Cashmere', 'Mercurial Cashmere', 'Parfum', 'electimuss-mercurial-cashmere-parfum', 'Mercurial Cashmere by Electimuss is a Floral Woody Musk fragrance for women and men. Mercurial Cashmere was launched in 2021. The nose behind this fragrance is Sofia Bardelli.', 'unisex'),
(14, 11, 2, 'Side Effect', 'Side Effect', 'Eau de Parfum', 'initio-side-effect-edp', 'Side Effect by Initio Parfums Prives is a Oriental fragrance for women and men. Side Effect was launched in 2016.', 'unisex'),
(15, 12, 2, 'Layton', 'Layton', 'Eau de Parfum', 'parfums-de-marly-layton-edp', 'Layton by Parfums de Marly is a Oriental Floral fragrance for women and men. Layton was launched in 2016. The nose behind this fragrance is Hamid Merati-Kashani.', 'male');

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
(1, 1, 'uploads/products/dior-sauvage-eau-de-parfum.webp', 0),
(2, 2, 'uploads/products/chanel-bleu-de-chanel-eau-de-parfum.webp', 0),
(3, 3, 'uploads/products/ysl-libre-eau-de-parfum.webp', 0),
(4, 4, 'uploads/products/creed-aventus-parfum.webp', 0),
(5, 5, 'uploads/products/tom-ford-oud-wood-parfum.webp', 0),
(6, 6, 'uploads/products/maison-francis-kurkdjian-baccarat-rouge-540-eau-de-parfum.webp', 0),
(7, 7, 'uploads/products/maison-francis-kurkdjian-baccarat-rouge-540-extrait-de-parfum.webp', 0),
(8, 8, 'uploads/products/roja-parfums-elysium-parfum-cologne.webp', 0),
(9, 9, 'uploads/products/roja-parfums-enigma-parfum-cologne.webp', 0),
(10, 10, 'uploads/products/xerjoff-naxos-eau-de-parfum.webp', 0),
(11, 11, 'uploads/products/xerjoff-erba-pura-eau-de-parfum.webp', 0),
(12, 12, 'uploads/products/montale-arabians-tonka-eau-de-parfum.webp', 0),
(13, 13, 'uploads/products/electimuss-mercurial-cashmere-parfum.webp', 0),
(14, 14, 'uploads/products/initio-side-effect-eau-de-parfum.webp', 0),
(15, 15, 'uploads/products/parfums-de-marly-layton-eau-de-parfum.webp', 0);

-- ======================
-- PRODUCT VARIANT IMAGES
-- ======================
INSERT INTO product_variant_images (
    id,
    product_variant_id,
    image_url,
    position
) VALUES
(1, 1, 'uploads/products/dior-sauvage-eau-de-parfum-60ml.webp', 0),
(2, 2, 'uploads/products/dior-sauvage-eau-de-parfum-100ml.webp', 0),

(3, 3, 'uploads/products/chanel-bleu-de-chanel-eau-de-parfum-50ml.webp', 0),
(4, 4, 'uploads/products/chanel-bleu-de-chanel-eau-de-parfum-100ml.webp', 0),

(5, 5, 'uploads/products/ysl-libre-eau-de-parfum-50ml.webp', 0),
(6, 6, 'uploads/products/ysl-libre-eau-de-parfum-90ml.webp', 0),

(7, 7, 'uploads/products/creed-aventus-parfum-50ml.webp', 0),
(8, 8, 'uploads/products/creed-aventus-parfum-100ml.webp', 0),

(9, 9, 'uploads/products/tom-ford-oud-wood-parfum-50ml.webp', 0),
(10, 10, 'uploads/products/tom-ford-oud-wood-parfum-100ml.webp', 0),

(11, 11, 'uploads/products/maison-francis-kurkdjian-baccarat-rouge-540-eau-de-parfum-70ml.webp', 0),
(12, 12, 'uploads/products/maison-francis-kurkdjian-baccarat-rouge-540-eau-de-parfum-200ml.webp', 0),

(13, 13, 'uploads/products/maison-francis-kurkdjian-baccarat-rouge-540-extrait-de-parfum-70ml.webp', 0),
(14, 14, 'uploads/products/maison-francis-kurkdjian-baccarat-rouge-540-extrait-de-parfum-200ml.webp', 0),

(15, 15, 'uploads/products/roja-parfums-elysium-parfum-cologne-100ml.webp', 0),
(16, 16, 'uploads/products/roja-parfums-enigma-parfum-cologne-100ml.webp', 0),
(17, 17, 'uploads/products/xerjoff-naxos-eau-de-parfum-100ml.webp', 0),

(18, 18, 'uploads/products/xerjoff-erba-pura-eau-de-parfum-50ml.webp', 0),
(19, 19, 'uploads/products/xerjoff-erba-pura-eau-de-parfum-100ml.webp', 0),

(20, 20, 'uploads/products/montale-arabians-tonka-eau-de-parfum-100ml.webp', 0),

(21, 21, 'uploads/products/electimuss-mercurial-cashmere-parfum-100ml.webp', 0),

(22, 22, 'uploads/products/initio-side-effect-eau-de-parfum-50ml.webp', 0),
(23, 23, 'uploads/products/initio-side-effect-eau-de-parfum-90ml.webp', 0),

(24, 24, 'uploads/products/parfums-de-marly-layton-eau-de-parfum-75ml.webp', 0),
(25, 25, 'uploads/products/parfums-de-marly-layton-eau-de-parfum-125ml.webp', 0);

-- ======================
-- PRODUCT CATEGORIES
-- ======================
INSERT INTO product_categories (product_id, category_id) VALUES
(1, 1), (1, 4), (1, 6), (1, 8),
(2, 1), (2, 2), (2, 5), (2, 6),
(3, 3), (3, 5), (3, 6), (3, 9),
(4, 1), (4, 2), (4, 7), (4, 15),
(5, 2), (5, 4), (5, 8), (5, 12),
(6, 2), (6, 3), (6, 4), (6, 9),
(7, 3), (7, 4), (7, 9), (7, 11),
(8, 1), (8, 5), (8, 6), (8, 13),
(9, 4), (9, 8), (9, 9), (9, 14),
(10, 5), (10, 6), (10, 10), (10, 14),
(11, 5), (11, 7), (11, 9), (11, 11),
(12, 4), (12, 8), (12, 9), (12, 12),
(13, 2), (13, 3), (13, 9), (13, 11),
(14, 2), (14, 8), (14, 9), (14, 14),
(15, 1), (15, 2), (15, 3), (15, 8);

-- ======================
-- PRODUCT NOTES
-- ======================
INSERT INTO product_notes (product_id, note_id, note_type) VALUES
-- 1. Dior Sauvage
(1, (SELECT id FROM notes WHERE slug = 'bergamot'), 'top'),
(1, (SELECT id FROM notes WHERE slug = 'sichuan-pepper'), 'heart'),
(1, (SELECT id FROM notes WHERE slug = 'lavender'), 'heart'),
(1, (SELECT id FROM notes WHERE slug = 'star-anise'), 'heart'),
(1, (SELECT id FROM notes WHERE slug = 'nutmeg'), 'heart'),
(1, (SELECT id FROM notes WHERE slug = 'ambroxan'), 'base'),
(1, (SELECT id FROM notes WHERE slug = 'vanilla'), 'base'),

-- 2. Bleu de Chanel
(2, (SELECT id FROM notes WHERE slug = 'grapefruit'), 'top'),
(2, (SELECT id FROM notes WHERE slug = 'lemon'), 'top'),
(2, (SELECT id FROM notes WHERE slug = 'mint'), 'top'),
(2, (SELECT id FROM notes WHERE slug = 'bergamot'), 'top'),
(2, (SELECT id FROM notes WHERE slug = 'pink-pepper'), 'top'),
(2, (SELECT id FROM notes WHERE slug = 'aldehydes'), 'top'),
(2, (SELECT id FROM notes WHERE slug = 'coriander'), 'top'),
(2, (SELECT id FROM notes WHERE slug = 'ginger'), 'heart'),
(2, (SELECT id FROM notes WHERE slug = 'nutmeg'), 'heart'),
(2, (SELECT id FROM notes WHERE slug = 'jasmine'), 'heart'),
(2, (SELECT id FROM notes WHERE slug = 'melon'), 'heart'),
(2, (SELECT id FROM notes WHERE slug = 'incense'), 'base'),
(2, (SELECT id FROM notes WHERE slug = 'amber'), 'base'),
(2, (SELECT id FROM notes WHERE slug = 'cedar'), 'base'),
(2, (SELECT id FROM notes WHERE slug = 'sandalwood'), 'base'),
(2, (SELECT id FROM notes WHERE slug = 'amberwood'), 'base'),
(2, (SELECT id FROM notes WHERE slug = 'patchouli'), 'base'),
(2, (SELECT id FROM notes WHERE slug = 'labdanum'), 'base'),

-- 3. YSL Libre
(3, (SELECT id FROM notes WHERE slug = 'lavender'), 'top'),
(3, (SELECT id FROM notes WHERE slug = 'mandarin-orange'), 'top'),
(3, (SELECT id FROM notes WHERE slug = 'black-currant'), 'top'),
(3, (SELECT id FROM notes WHERE slug = 'petitgrain'), 'top'),
(3, (SELECT id FROM notes WHERE slug = 'lavender'), 'heart'),
(3, (SELECT id FROM notes WHERE slug = 'orange-blossom'), 'heart'),
(3, (SELECT id FROM notes WHERE slug = 'jasmine'), 'heart'),
(3, (SELECT id FROM notes WHERE slug = 'madagascar-vanilla'), 'base'),
(3, (SELECT id FROM notes WHERE slug = 'musk'), 'base'),
(3, (SELECT id FROM notes WHERE slug = 'cedar'), 'base'),
(3, (SELECT id FROM notes WHERE slug = 'ambergris'), 'base'),

-- 4. Creed Aventus
(4, (SELECT id FROM notes WHERE slug = 'bergamot'), 'top'),
(4, (SELECT id FROM notes WHERE slug = 'black-currant'), 'top'),
(4, (SELECT id FROM notes WHERE slug = 'apple'), 'top'),
(4, (SELECT id FROM notes WHERE slug = 'lemon'), 'top'),
(4, (SELECT id FROM notes WHERE slug = 'pink-pepper'), 'top'),
(4, (SELECT id FROM notes WHERE slug = 'pineapple'), 'heart'),
(4, (SELECT id FROM notes WHERE slug = 'patchouli'), 'heart'),
(4, (SELECT id FROM notes WHERE slug = 'moroccan-jasmine'), 'heart'),
(4, (SELECT id FROM notes WHERE slug = 'birch'), 'base'),
(4, (SELECT id FROM notes WHERE slug = 'musk'), 'base'),
(4, (SELECT id FROM notes WHERE slug = 'oakmoss'), 'base'),
(4, (SELECT id FROM notes WHERE slug = 'cedar'), 'base'),
(4, (SELECT id FROM notes WHERE slug = 'ambroxan'), 'base'),

-- 5. Tom Ford Oud Wood
(5, (SELECT id FROM notes WHERE slug = 'agarwood-oud'), 'general'),
(5, (SELECT id FROM notes WHERE slug = 'brazilian-rosewood'), 'general'),
(5, (SELECT id FROM notes WHERE slug = 'cardamom'), 'general'),
(5, (SELECT id FROM notes WHERE slug = 'sandalwood'), 'general'),
(5, (SELECT id FROM notes WHERE slug = 'sichuan-pepper'), 'general'),
(5, (SELECT id FROM notes WHERE slug = 'vanilla'), 'general'),
(5, (SELECT id FROM notes WHERE slug = 'vetiver'), 'general'),
(5, (SELECT id FROM notes WHERE slug = 'tonka-bean'), 'general'),
(5, (SELECT id FROM notes WHERE slug = 'amber'), 'general'),

-- 6. Baccarat Rouge 540 EDP
(6, (SELECT id FROM notes WHERE slug = 'saffron'), 'top'),
(6, (SELECT id FROM notes WHERE slug = 'jasmine'), 'top'),
(6, (SELECT id FROM notes WHERE slug = 'amberwood'), 'heart'),
(6, (SELECT id FROM notes WHERE slug = 'ambergris'), 'heart'),
(6, (SELECT id FROM notes WHERE slug = 'hedione'), 'heart'),
(6, (SELECT id FROM notes WHERE slug = 'fir-resin'), 'base'),
(6, (SELECT id FROM notes WHERE slug = 'cedar'), 'base'),
(6, (SELECT id FROM notes WHERE slug = 'sugar'), 'base'),
(6, (SELECT id FROM notes WHERE slug = 'ambroxan'), 'base'),
(6, (SELECT id FROM notes WHERE slug = 'oakmoss'), 'base'),

-- 7. Baccarat Rouge 540 Extrait
(7, (SELECT id FROM notes WHERE slug = 'bitter-almond'), 'top'),
(7, (SELECT id FROM notes WHERE slug = 'saffron'), 'top'),
(7, (SELECT id FROM notes WHERE slug = 'egyptian-jasmine'), 'heart'),
(7, (SELECT id FROM notes WHERE slug = 'virginia-cedar'), 'heart'),
(7, (SELECT id FROM notes WHERE slug = 'ambergris'), 'base'),
(7, (SELECT id FROM notes WHERE slug = 'woody-notes'), 'base'),
(7, (SELECT id FROM notes WHERE slug = 'musk'), 'base'),
(7, (SELECT id FROM notes WHERE slug = 'ambroxan'), 'base'),
(7, (SELECT id FROM notes WHERE slug = 'cashmeran'), 'base'),

-- 8. Roja Elysium
(8, (SELECT id FROM notes WHERE slug = 'grapefruit'), 'top'),
(8, (SELECT id FROM notes WHERE slug = 'lime'), 'top'),
(8, (SELECT id FROM notes WHERE slug = 'bergamot'), 'top'),
(8, (SELECT id FROM notes WHERE slug = 'lemon'), 'top'),
(8, (SELECT id FROM notes WHERE slug = 'galbanum'), 'top'),
(8, (SELECT id FROM notes WHERE slug = 'artemisia'), 'top'),
(8, (SELECT id FROM notes WHERE slug = 'thyme'), 'top'),
(8, (SELECT id FROM notes WHERE slug = 'vetiver'), 'heart'),
(8, (SELECT id FROM notes WHERE slug = 'juniper-berry'), 'heart'),
(8, (SELECT id FROM notes WHERE slug = 'black-currant'), 'heart'),
(8, (SELECT id FROM notes WHERE slug = 'apple'), 'heart'),
(8, (SELECT id FROM notes WHERE slug = 'cedar'), 'heart'),
(8, (SELECT id FROM notes WHERE slug = 'pink-pepper'), 'heart'),
(8, (SELECT id FROM notes WHERE slug = 'cypriol-oil'), 'heart'),
(8, (SELECT id FROM notes WHERE slug = 'lily-of-the-valley'), 'heart'),
(8, (SELECT id FROM notes WHERE slug = 'jasmine'), 'heart'),
(8, (SELECT id FROM notes WHERE slug = 'rose'), 'heart'),
(8, (SELECT id FROM notes WHERE slug = 'ambergris'), 'base'),
(8, (SELECT id FROM notes WHERE slug = 'leather'), 'base'),
(8, (SELECT id FROM notes WHERE slug = 'vanilla'), 'base'),
(8, (SELECT id FROM notes WHERE slug = 'benzoin'), 'base'),
(8, (SELECT id FROM notes WHERE slug = 'labdanum'), 'base'),

-- 9. Roja Enigma
(9, (SELECT id FROM notes WHERE slug = 'bergamot'), 'top'),
(9, (SELECT id FROM notes WHERE slug = 'heliotrope'), 'heart'),
(9, (SELECT id FROM notes WHERE slug = 'jasmine'), 'heart'),
(9, (SELECT id FROM notes WHERE slug = 'geranium'), 'heart'),
(9, (SELECT id FROM notes WHERE slug = 'neroli'), 'heart'),
(9, (SELECT id FROM notes WHERE slug = 'rose-de-mai'), 'heart'),
(9, (SELECT id FROM notes WHERE slug = 'cognac'), 'base'),
(9, (SELECT id FROM notes WHERE slug = 'benzoin'), 'base'),
(9, (SELECT id FROM notes WHERE slug = 'tobacco'), 'base'),
(9, (SELECT id FROM notes WHERE slug = 'vanilla'), 'base'),
(9, (SELECT id FROM notes WHERE slug = 'patchouli'), 'base'),
(9, (SELECT id FROM notes WHERE slug = 'sandalwood'), 'base'),
(9, (SELECT id FROM notes WHERE slug = 'ambergris'), 'base'),
(9, (SELECT id FROM notes WHERE slug = 'ginger'), 'base'),
(9, (SELECT id FROM notes WHERE slug = 'cardamom'), 'base'),
(9, (SELECT id FROM notes WHERE slug = 'pepper'), 'base'),

-- 10. Xerjoff Naxos
(10, (SELECT id FROM notes WHERE slug = 'lavender'), 'top'),
(10, (SELECT id FROM notes WHERE slug = 'bergamot'), 'top'),
(10, (SELECT id FROM notes WHERE slug = 'lemon'), 'top'),
(10, (SELECT id FROM notes WHERE slug = 'honey'), 'heart'),
(10, (SELECT id FROM notes WHERE slug = 'cinnamon'), 'heart'),
(10, (SELECT id FROM notes WHERE slug = 'cashmeran'), 'heart'),
(10, (SELECT id FROM notes WHERE slug = 'jasmine-sambac'), 'heart'),
(10, (SELECT id FROM notes WHERE slug = 'tobacco-leaf'), 'base'),
(10, (SELECT id FROM notes WHERE slug = 'vanilla'), 'base'),
(10, (SELECT id FROM notes WHERE slug = 'tonka-bean'), 'base'),

-- 11. Xerjoff Erba Pura
(11, (SELECT id FROM notes WHERE slug = 'sicilian-orange'), 'top'),
(11, (SELECT id FROM notes WHERE slug = 'calabrian-bergamot'), 'top'),
(11, (SELECT id FROM notes WHERE slug = 'sicilian-lemon'), 'top'),
(11, (SELECT id FROM notes WHERE slug = 'fruits'), 'heart'),
(11, (SELECT id FROM notes WHERE slug = 'white-musk'), 'base'),
(11, (SELECT id FROM notes WHERE slug = 'madagascar-vanilla'), 'base'),
(11, (SELECT id FROM notes WHERE slug = 'amber'), 'base'),

-- 12. Montale Arabians Tonka
(12, (SELECT id FROM notes WHERE slug = 'saffron'), 'top'),
(12, (SELECT id FROM notes WHERE slug = 'bergamot'), 'top'),
(12, (SELECT id FROM notes WHERE slug = 'agarwood-oud'), 'heart'),
(12, (SELECT id FROM notes WHERE slug = 'bulgarian-rose'), 'heart'),
(12, (SELECT id FROM notes WHERE slug = 'tonka-bean'), 'base'),
(12, (SELECT id FROM notes WHERE slug = 'sugar-cane'), 'base'),
(12, (SELECT id FROM notes WHERE slug = 'amber'), 'base'),
(12, (SELECT id FROM notes WHERE slug = 'white-musk'), 'base'),
(12, (SELECT id FROM notes WHERE slug = 'oakmoss'), 'base'),

-- 13. Electimuss Mercurial Cashmere
(13, (SELECT id FROM notes WHERE slug = 'cardamom'), 'top'),
(13, (SELECT id FROM notes WHERE slug = 'bergamot'), 'top'),
(13, (SELECT id FROM notes WHERE slug = 'pink-pepper'), 'top'),
(13, (SELECT id FROM notes WHERE slug = 'tuberose'), 'heart'),
(13, (SELECT id FROM notes WHERE slug = 'iris'), 'heart'),
(13, (SELECT id FROM notes WHERE slug = 'violet'), 'heart'),
(13, (SELECT id FROM notes WHERE slug = 'ambergris'), 'heart'),
(13, (SELECT id FROM notes WHERE slug = 'caramel'), 'base'),
(13, (SELECT id FROM notes WHERE slug = 'vanilla'), 'base'),
(13, (SELECT id FROM notes WHERE slug = 'cashmere-wood'), 'base'),
(13, (SELECT id FROM notes WHERE slug = 'tonka-bean'), 'base'),
(13, (SELECT id FROM notes WHERE slug = 'musk'), 'base'),
(13, (SELECT id FROM notes WHERE slug = 'cedar'), 'base'),
(13, (SELECT id FROM notes WHERE slug = 'agarwood-oud'), 'base'),

-- 14. Initio Side Effect
(14, (SELECT id FROM notes WHERE slug = 'rum'), 'general'),
(14, (SELECT id FROM notes WHERE slug = 'hedione'), 'general'),
(14, (SELECT id FROM notes WHERE slug = 'saffron'), 'general'),
(14, (SELECT id FROM notes WHERE slug = 'tobacco'), 'general'),
(14, (SELECT id FROM notes WHERE slug = 'cinnamon'), 'general'),
(14, (SELECT id FROM notes WHERE slug = 'sandalwood'), 'general'),

-- 15. Parfums de Marly Layton
(15, (SELECT id FROM notes WHERE slug = 'apple'), 'top'),
(15, (SELECT id FROM notes WHERE slug = 'lavender'), 'top'),
(15, (SELECT id FROM notes WHERE slug = 'bergamot'), 'top'),
(15, (SELECT id FROM notes WHERE slug = 'mandarin-orange'), 'top'),
(15, (SELECT id FROM notes WHERE slug = 'geranium'), 'heart'),
(15, (SELECT id FROM notes WHERE slug = 'violet'), 'heart'),
(15, (SELECT id FROM notes WHERE slug = 'jasmine'), 'heart'),
(15, (SELECT id FROM notes WHERE slug = 'vanilla'), 'base'),
(15, (SELECT id FROM notes WHERE slug = 'cardamom'), 'base'),
(15, (SELECT id FROM notes WHERE slug = 'sandalwood'), 'base'),
(15, (SELECT id FROM notes WHERE slug = 'pepper'), 'base'),
(15, (SELECT id FROM notes WHERE slug = 'guaiac-wood'), 'base'),
(15, (SELECT id FROM notes WHERE slug = 'patchouli'), 'base'),
(15, (SELECT id FROM notes WHERE slug = 'coumarin'), 'base'),
(15, (SELECT id FROM notes WHERE slug = 'ambermax'), 'base');

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
    brand_name_snapshot,
    concentration_label_snapshot,
    image_url_snapshot,
    size_ml_snapshot,
    quantity,
    price_at_purchase
) VALUES
(1, 1, 1, 'Sauvage', 'Dior', 'Eau de Parfum', 'uploads/products/dior-sauvage-eau-de-parfum-60ml.webp', 60, 2, 85.00),
(2, 1, 4, 'Bleu de Chanel', 'Chanel', 'Eau de Parfum', 'uploads/products/chanel-bleu-de-chanel-eau-de-parfum-100ml.webp', 100, 1, 130.00),

(3, 2, 11, 'Baccarat Rouge 540', 'Maison Francis Kurkdjian', 'Eau de Parfum', 'uploads/products/maison-francis-kurkdjian-baccarat-rouge-540-eau-de-parfum-70ml.webp', 70, 1, 245.00),

(4, 3, 9, 'Oud Wood', 'Tom Ford', 'Parfum', 'uploads/products/tom-ford-oud-wood-parfum-50ml.webp', 50, 1, 210.00),

(5, 4, 12, 'Baccarat Rouge 540', 'Maison Francis Kurkdjian', 'Eau de Parfum', 'uploads/products/maison-francis-kurkdjian-baccarat-rouge-540-eau-de-parfum-200ml.webp', 200, 1, 430.00),

(6, 5, 5, 'Libre', 'Yves Saint Laurent', 'Eau de Parfum', 'uploads/products/ysl-libre-eau-de-parfum-50ml.webp', 50, 1, 95.00),

(7, 6, 17, 'Naxos', 'Xerjoff', 'Eau de Parfum', 'uploads/products/xerjoff-naxos-eau-de-parfum-100ml.webp', 100, 1, 205.00),

(8, 7, 24, 'Layton', 'Parfums de Marly', 'Eau de Parfum', 'uploads/products/parfums-de-marly-layton-eau-de-parfum-75ml.webp', 75, 1, 180.00),

(9, 8, 22, 'Side Effect', 'Initio Parfums Privés', 'Eau de Parfum', 'uploads/products/initio-side-effect-eau-de-parfum-50ml.webp', 50, 1, 185.00);

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
