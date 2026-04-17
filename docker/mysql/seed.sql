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
(5, 'Luxury'),
(6, 'Everyday');

-- ======================
-- NOTES
-- ======================
INSERT INTO notes (id, name, slug, image_url) VALUES
(1, 'Bergamot', 'bergamot', 'uploads/notes/bergamot.png'),
(2, 'Pepper', 'pepper', 'uploads/notes/pepper.png'),
(3, 'Lavender', 'lavender', 'uploads/notes/lavender.png'),
(4, 'Cedar', 'cedar', 'uploads/notes/cedar.png'),
(5, 'Vanilla', 'vanilla', 'uploads/notes/vanilla.png'),
(6, 'Patchouli', 'patchouli', 'uploads/notes/patchouli.png'),
(7, 'Jasmine', 'jasmine', 'uploads/notes/jasmine.png'),
(8, 'Ambroxan', 'ambroxan', 'uploads/notes/ambroxan.png'),
(9, 'Pineapple', 'pineapple', 'uploads/notes/pineapple.png'),
(10, 'Musk', 'musk', 'uploads/notes/musk.png'),
(11, 'Apple', 'apple', 'uploads/notes/apple.png'),
(12, 'Mandarin Orange', 'mandarin-orange', 'uploads/notes/mandarin-orange.png'),
(13, 'Geranium', 'geranium', 'uploads/notes/geranium.png'),
(14, 'Violet', 'violet', 'uploads/notes/violet.png'),
(15, 'Cardamom', 'cardamom', 'uploads/notes/cardamom.png'),
(16, 'Sandalwood', 'sandalwood', 'uploads/notes/sandalwood.png'),
(17, 'Guaiac Wood', 'guaiac-wood', 'uploads/notes/guaiac-wood.png'),
(18, 'Coumarin', 'coumarin', 'uploads/notes/coumarin.png'),
(19, 'Ambermax', 'ambermax', 'uploads/notes/ambermax.png'),
(20, 'Pink Pepper', 'pink-pepper', 'uploads/notes/pink-pepper.png'),
(21, 'Tuberose', 'tuberose', 'uploads/notes/tuberose.png'),
(22, 'Iris', 'iris', 'uploads/notes/iris.png'),
(23, 'Ambergris', 'ambergris', 'uploads/notes/ambergris.png'),
(24, 'Caramel', 'caramel', 'uploads/notes/caramel.png'),
(25, 'Cashmere Wood', 'cashmere-wood', 'uploads/notes/cashmere-wood.png'),
(26, 'Tonka Bean', 'tonka-bean', 'uploads/notes/tonka-bean.png'),
(27, 'Agarwood (Oud)', 'agarwood-oud', 'uploads/notes/agarwood-oud.png'),
(28, 'Saffron', 'saffron', 'uploads/notes/saffron.png'),
(29, 'Bulgarian Rose', 'bulgarian-rose', 'uploads/notes/bulgarian-rose.png'),
(30, 'Sugar', 'sugar', 'uploads/notes/sugar.png'),
(31, 'Amber', 'amber', 'uploads/notes/amber.png'),
(32, 'Oakmoss', 'oakmoss', 'uploads/notes/oakmoss.png'),
(33, 'Sicilian Orange', 'sicilian-orange', 'uploads/notes/sicilian-orange.png'),
(34, 'Lemon', 'lemon', 'uploads/notes/lemon.png'),
(35, 'Fruits', 'fruits', 'uploads/notes/fruits.png'),
(36, 'Honey', 'honey', 'uploads/notes/honey.png'),
(37, 'Cinnamon', 'cinnamon', 'uploads/notes/cinnamon.png'),
(38, 'Cashmeran', 'cashmeran', 'uploads/notes/cashmeran.png'),
(39, 'Tobacco Leaf', 'tobacco-leaf', 'uploads/notes/tobacco-leaf.png'),
(40, 'Grapefruit', 'grapefruit', 'uploads/notes/grapefruit.png'),
(41, 'Lime', 'lime', 'uploads/notes/lime.png'),
(42, 'Artemisia', 'artemisia', 'uploads/notes/artemisia.png'),
(43, 'Thyme', 'thyme', 'uploads/notes/thyme.png'),
(44, 'Black Currant', 'black-currant', 'uploads/notes/black-currant.png'),
(45, 'Orange Blossom', 'orange-blossom', 'uploads/notes/orange-blossom.png'),
(46, 'Lily of the Valley', 'lily-of-the-valley', 'uploads/notes/lily-of-the-valley.png'),
(47, 'Rose de Mai', 'rose-de-mai', 'uploads/notes/rose-de-mai.png'),
(48, 'Juniper Berry', 'juniper-berry', 'uploads/notes/juniper-berry.png'),
(49, 'Vetiver', 'vetiver', 'uploads/notes/vetiver.png'),
(50, 'Galbanum', 'galbanum', 'uploads/notes/galbanum.png'),
(51, 'Leather', 'leather', 'uploads/notes/leather.png'),
(52, 'Labdanum', 'labdanum', 'uploads/notes/labdanum.png'),
(53, 'Benzoin', 'benzoin', 'uploads/notes/benzoin.png'),
(54, 'Heliotrope', 'heliotrope', 'uploads/notes/heliotrope.png'),
(55, 'Ginger', 'ginger', 'uploads/notes/ginger.png'),
(56, 'Neroli', 'neroli', 'uploads/notes/neroli.png'),
(57, 'Cognac', 'cognac', 'uploads/notes/cognac.png'),
(58, 'Bitter Almond', 'bitter-almond', 'uploads/notes/bitter-almond.png'),
(59, 'Egyptian Jasmine', 'egyptian-jasmine', 'uploads/notes/egyptian-jasmine.png'),
(60, 'Woody Notes', 'woody-notes', 'uploads/notes/woody-notes.png'),
(61, 'Amberwood', 'amberwood', 'uploads/notes/amberwood.png'),
(62, 'Hedione', 'hedione', 'uploads/notes/hedione.png'),
(63, 'Fir Resin', 'fir-resin', 'uploads/notes/fir-resin.png'),
(64, 'Petitgrain', 'petitgrain', 'uploads/notes/petitgrain.png'),
(65, 'Mint', 'mint', 'uploads/notes/mint.png'),
(66, 'Aldehydes', 'aldehydes', 'uploads/notes/aldehydes.png'),
(67, 'Coriander', 'coriander', 'uploads/notes/coriander.png'),
(68, 'Nutmeg', 'nutmeg', 'uploads/notes/nutmeg.png'),
(69, 'Melon', 'melon', 'uploads/notes/melon.png'),
(70, 'Incense', 'incense', 'uploads/notes/incense.png'),
(71, 'Sichuan Pepper', 'sichuan-pepper', 'uploads/notes/sichuan-pepper.png'),
(72, 'Star Anise', 'star-anise', 'uploads/notes/star-anise.png'),
(73, 'Birch', 'birch', 'uploads/notes/birch.png'),
(74, 'Tobacco', 'tobacco', 'uploads/notes/tobacco.png'),
(75, 'White Musk', 'white-musk', 'uploads/notes/white-musk.png'),
(76, 'Madagascar Vanilla', 'madagascar-vanilla', 'uploads/notes/madagascar-vanilla.png'),
(77, 'Calabrian Bergamot', 'calabrian-bergamot', 'uploads/notes/calabrian-bergamot.png'),
(78, 'Sicilian Lemon', 'sicilian-lemon', 'uploads/notes/sicilian-lemon.png'),
(79, 'Jasmine Sambac', 'jasmine-sambac', 'uploads/notes/jasmine-sambac.png'),
(80, 'Virginia Cedar', 'virginia-cedar', 'uploads/notes/virginia-cedar.png'),
(81, 'Brazilian Rosewood', 'brazilian-rosewood', 'uploads/notes/brazilian-rosewood.png'),
(82, 'Rum', 'rum', 'uploads/notes/rum.png'),
(83, 'Cypriol Oil', 'cypriol-oil', 'uploads/notes/cypriol-oil.png'),
(84, 'Rose', 'rose', 'uploads/notes/rose.png'),
(85, 'Moroccan Jasmine', 'moroccan-jasmine', 'uploads/notes/moroccan-jasmine.png');
(86, 'Sugar Cane', 'sugar-cane', 'uploads/notes/sugar-cane.png'),

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
(1, 1, 2, 'Dior Sauvage', 'Sauvage', 'Eau de Parfum', 'dior-sauvage-edp', 'Fresh spicy fragrance with bergamot, pepper and ambroxan.', 'male'),
(2, 2, 2, 'Bleu de Chanel', 'Bleu de Chanel', 'Eau de Parfum', 'bleu-de-chanel-edp', 'Woody aromatic fragrance with citrus, incense and cedar.', 'male'),
(3, 3, 2, 'Libre', 'Libre', 'Eau de Parfum', 'ysl-libre-edp', 'Floral lavender perfume with vanilla and musk.', 'female'),
(4, 4, 3, 'Aventus', 'Aventus', 'Parfum', 'creed-aventus-parfum', 'Fruity smoky fragrance with pineapple, birch and musk.', 'male'),
(5, 5, 3, 'Oud Wood', 'Oud Wood', 'Parfum', 'tom-ford-oud-wood-parfum', 'Warm woody fragrance with oud, vanilla and spices.', 'unisex'),
(6, 6, 2, 'Baccarat Rouge 540', 'Baccarat Rouge 540', 'Eau de Parfum', 'baccarat-rouge-540-edp', 'Amber floral fragrance with jasmine, saffron and cedar.', 'unisex'),
(7, 6, 3, 'Baccarat Rouge 540', 'Baccarat Rouge 540', 'Extrait de Parfum', 'baccarat-rouge-540-extrait', 'A richer and denser Baccarat Rouge 540 interpretation with deeper amber and musk facets.', 'unisex'),
(8, 7, 3, 'Elysium', 'Elysium', 'Parfum Cologne', 'roja-elysium-parfum-cologne', 'Bright citrus aromatic fragrance with refined woods and musk.', 'male'),
(9, 7, 3, 'Enigma', 'Enigma', 'Parfum Cologne', 'roja-enigma-parfum-cologne', 'Warm amber-spicy composition with vanilla, cognac and woods.', 'male'),
(10, 8, 2, 'Naxos', 'Naxos', 'Eau de Parfum', 'xerjoff-naxos-edp', 'Honeyed tobacco fragrance with lavender, citrus and vanilla.', 'unisex'),
(11, 8, 2, 'Erba Pura', 'Erba Pura', 'Eau de Parfum', 'xerjoff-erba-pura-edp', 'Fruity-musky fragrance with radiant sweetness and soft warmth.', 'unisex'),
(12, 9, 2, 'Arabians Tonka', 'Arabians Tonka', 'Eau de Parfum', 'montale-arabians-tonka-edp', 'Dense amber-oud profile with rose, sugar and tonka facets.', 'unisex'),
(13, 10, 3, 'Mercurial Cashmere', 'Mercurial Cashmere', 'Parfum', 'electimuss-mercurial-cashmere-parfum', 'Smooth amber-woody fragrance with creamy vanilla and musk.', 'unisex'),
(14, 11, 2, 'Side Effect', 'Side Effect', 'Eau de Parfum', 'initio-side-effect-edp', 'Rich spicy blend with cinnamon, vanilla, tobacco and rum nuances.', 'unisex'),
(15, 12, 2, 'Layton', 'Layton', 'Eau de Parfum', 'parfums-de-marly-layton-edp', 'Elegant sweet-spicy profile with apple, vanilla and woods.', 'male');

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
(1, 1, 1, 'Sauvage', 'Dior', 'Eau de Parfum', 'uploads/products/dior-sauvage-eau-de-parfum-60ml.jpg', 60, 2, 85.00),
(2, 1, 4, 'Bleu de Chanel', 'Chanel', 'Eau de Parfum', 'uploads/products/chanel-bleu-de-chanel-eau-de-parfum-100ml.jpg', 100, 1, 130.00),

(3, 2, 11, 'Baccarat Rouge 540', 'Maison Francis Kurkdjian', 'Eau de Parfum', 'uploads/products/maison-francis-kurkdjian-baccarat-rouge-540-eau-de-parfum-70ml.jpg', 70, 1, 245.00),

(4, 3, 9, 'Oud Wood', 'Tom Ford', 'Parfum', 'uploads/products/tom-ford-oud-wood-parfum-50ml.jpg', 50, 1, 210.00),

(5, 4, 12, 'Baccarat Rouge 540', 'Maison Francis Kurkdjian', 'Eau de Parfum', 'uploads/products/maison-francis-kurkdjian-baccarat-rouge-540-eau-de-parfum-200ml.jpg', 200, 1, 430.00),

(6, 5, 5, 'Libre', 'Yves Saint Laurent', 'Eau de Parfum', 'uploads/products/ysl-libre-eau-de-parfum-50ml.jpg', 50, 1, 95.00),

(7, 6, 17, 'Naxos', 'Xerjoff', 'Eau de Parfum', 'uploads/products/xerjoff-naxos-eau-de-parfum-100ml.jpg', 100, 1, 205.00),

(8, 7, 24, 'Layton', 'Parfums de Marly', 'Eau de Parfum', 'uploads/products/parfums-de-marly-layton-eau-de-parfum-75ml.jpg', 75, 1, 180.00),

(9, 8, 22, 'Side Effect', 'Initio Parfums Privés', 'Eau de Parfum', 'uploads/products/initio-side-effect-eau-de-parfum-50ml.jpg', 50, 1, 185.00);

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
