-- ============================================================
--  PINOYBALLERS-STYLE WEBSITE — Database Schema
--  Engine : InnoDB | Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
--  Run this file in MySQL / MariaDB as your DB admin user
-- ============================================================

-- Create and select the database
CREATE DATABASE IF NOT EXISTS creativekit3a
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE creativekit3a;

-- ============================================================
--  TABLE: categories
--  Supports unlimited nesting via parent_id (self-referencing)
-- ============================================================
CREATE TABLE IF NOT EXISTS categories (
  id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  parent_id   INT UNSIGNED    DEFAULT NULL,
  name        VARCHAR(150)    NOT NULL,
  slug        VARCHAR(160)    NOT NULL UNIQUE,
  description TEXT            DEFAULT NULL,
  image_url   VARCHAR(512)    DEFAULT NULL,
  sort_order  SMALLINT        NOT NULL DEFAULT 0,
  is_active   TINYINT(1)      NOT NULL DEFAULT 1,
  created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                              ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_parent  (parent_id),
  INDEX idx_slug    (slug),
  INDEX idx_active  (is_active),

  CONSTRAINT fk_cat_parent
    FOREIGN KEY (parent_id) REFERENCES categories (id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  TABLE: products
-- ============================================================
CREATE TABLE IF NOT EXISTS products (
  id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  category_id     INT UNSIGNED    DEFAULT NULL,
  name            VARCHAR(255)    NOT NULL,
  slug            VARCHAR(270)    NOT NULL UNIQUE,
  sku             VARCHAR(100)    DEFAULT NULL UNIQUE,
  short_desc      VARCHAR(500)    DEFAULT NULL,
  description     LONGTEXT        DEFAULT NULL,
  thumbnail_url   VARCHAR(512)    DEFAULT NULL,
  base_price      DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  sale_price      DECIMAL(10,2)   DEFAULT NULL,
  stock_qty       INT             NOT NULL DEFAULT 0,
  is_customizable TINYINT(1)      NOT NULL DEFAULT 1,
  is_featured     TINYINT(1)      NOT NULL DEFAULT 0,
  is_active       TINYINT(1)      NOT NULL DEFAULT 1,
  sort_order      SMALLINT        NOT NULL DEFAULT 0,
  meta_title      VARCHAR(255)    DEFAULT NULL,
  meta_desc       VARCHAR(500)    DEFAULT NULL,
  created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                  ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_category    (category_id),
  INDEX idx_slug        (slug),
  INDEX idx_featured    (is_featured),
  INDEX idx_active      (is_active),
  INDEX idx_price       (base_price),

  CONSTRAINT fk_product_category
    FOREIGN KEY (category_id) REFERENCES categories (id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  TABLE: product_images
--  Multiple images per product
-- ============================================================
CREATE TABLE IF NOT EXISTS product_images (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  product_id  INT UNSIGNED  NOT NULL,
  image_url   VARCHAR(512)  NOT NULL,
  alt_text    VARCHAR(255)  DEFAULT NULL,
  sort_order  SMALLINT      NOT NULL DEFAULT 0,
  is_primary  TINYINT(1)    NOT NULL DEFAULT 0,
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_product (product_id),

  CONSTRAINT fk_img_product
    FOREIGN KEY (product_id) REFERENCES products (id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  TABLE: product_pricing_tiers
--  Volume-based price breaks (e.g. 100 pcs → ₱45.00)
-- ============================================================
CREATE TABLE IF NOT EXISTS product_pricing_tiers (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  product_id  INT UNSIGNED  NOT NULL,
  size_label  VARCHAR(50)   DEFAULT NULL   COMMENT 'e.g. 1/2 inch, 1 inch',
  min_qty     INT UNSIGNED  NOT NULL       COMMENT 'Minimum pieces for this price',
  max_qty     INT UNSIGNED  DEFAULT NULL   COMMENT 'NULL = no upper limit',
  unit_price  DECIMAL(10,2) NOT NULL,
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_pt_product (product_id),
  INDEX idx_pt_qty     (min_qty),

  CONSTRAINT fk_pt_product
    FOREIGN KEY (product_id) REFERENCES products (id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  TABLE: product_tags
-- ============================================================
CREATE TABLE IF NOT EXISTS product_tags (
  id    INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  name  VARCHAR(100)  NOT NULL UNIQUE,
  slug  VARCHAR(110)  NOT NULL UNIQUE,

  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  TABLE: product_tag_map  (many-to-many)
-- ============================================================
CREATE TABLE IF NOT EXISTS product_tag_map (
  product_id  INT UNSIGNED  NOT NULL,
  tag_id      INT UNSIGNED  NOT NULL,

  PRIMARY KEY (product_id, tag_id),

  CONSTRAINT fk_ptm_product
    FOREIGN KEY (product_id) REFERENCES products (id)
    ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT fk_ptm_tag
    FOREIGN KEY (tag_id) REFERENCES product_tags (id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  TABLE: clients  (logos on the "Our Clients" carousel)
-- ============================================================
CREATE TABLE IF NOT EXISTS clients (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  name        VARCHAR(150)  NOT NULL,
  logo_url    VARCHAR(512)  DEFAULT NULL,
  website_url VARCHAR(512)  DEFAULT NULL,
  sort_order  SMALLINT      NOT NULL DEFAULT 0,
  is_active   TINYINT(1)    NOT NULL DEFAULT 1,
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  TABLE: hero_banners  (carousel slides)
-- ============================================================
CREATE TABLE IF NOT EXISTS hero_banners (
  id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  title           VARCHAR(200)  DEFAULT NULL,
  subtitle        VARCHAR(200)  DEFAULT NULL,
  body_text       TEXT          DEFAULT NULL,
  eyebrow         VARCHAR(100)  DEFAULT NULL  COMMENT 'Small badge above headline',
  image_url       VARCHAR(512)  DEFAULT NULL,
  cta_label       VARCHAR(80)   DEFAULT NULL,
  cta_url         VARCHAR(512)  DEFAULT NULL,
  bg_class        VARCHAR(80)   DEFAULT NULL  COMMENT 'CSS class for slide background',
  sort_order      SMALLINT      NOT NULL DEFAULT 0,
  is_active       TINYINT(1)    NOT NULL DEFAULT 1,
  created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                                ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  TABLE: users
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
  id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  first_name      VARCHAR(100)  NOT NULL,
  last_name       VARCHAR(100)  NOT NULL,
  email           VARCHAR(255)  NOT NULL UNIQUE,
  phone           VARCHAR(30)   DEFAULT NULL,
  password_hash   VARCHAR(255)  NOT NULL           COMMENT 'bcrypt hash',
  role            ENUM('customer','admin','staff')  NOT NULL DEFAULT 'customer',
  is_verified     TINYINT(1)    NOT NULL DEFAULT 0,
  is_active       TINYINT(1)    NOT NULL DEFAULT 1,
  created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                                ON UPDATE CURRENT_TIMESTAMP,
  last_login_at   DATETIME      DEFAULT NULL,

  PRIMARY KEY (id),
  UNIQUE INDEX idx_email  (email),
  INDEX idx_role          (role),
  INDEX idx_active        (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  TABLE: addresses  (shipping / billing)
-- ============================================================
CREATE TABLE IF NOT EXISTS addresses (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  user_id     INT UNSIGNED  NOT NULL,
  label       VARCHAR(50)   DEFAULT 'Home'     COMMENT 'e.g. Home, Office',
  full_name   VARCHAR(200)  NOT NULL,
  phone       VARCHAR(30)   DEFAULT NULL,
  line1       VARCHAR(255)  NOT NULL,
  line2       VARCHAR(255)  DEFAULT NULL,
  city        VARCHAR(100)  NOT NULL,
  province    VARCHAR(100)  DEFAULT NULL,
  postal_code VARCHAR(20)   DEFAULT NULL,
  country     VARCHAR(80)   NOT NULL DEFAULT 'Philippines',
  is_default  TINYINT(1)    NOT NULL DEFAULT 0,
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_user (user_id),

  CONSTRAINT fk_addr_user
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  TABLE: orders
-- ============================================================
CREATE TABLE IF NOT EXISTS orders (
  id                  INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  user_id             INT UNSIGNED    DEFAULT NULL  COMMENT 'NULL = guest',
  order_number        VARCHAR(40)     NOT NULL UNIQUE,
  status              ENUM(
                        'pending',
                        'confirmed',
                        'in_production',
                        'shipped',
                        'delivered',
                        'cancelled',
                        'refunded'
                      ) NOT NULL DEFAULT 'pending',
  subtotal            DECIMAL(12,2)   NOT NULL DEFAULT 0.00,
  discount_amount     DECIMAL(12,2)   NOT NULL DEFAULT 0.00,
  shipping_fee        DECIMAL(12,2)   NOT NULL DEFAULT 0.00,
  tax_amount          DECIMAL(12,2)   NOT NULL DEFAULT 0.00,
  total_amount        DECIMAL(12,2)   NOT NULL DEFAULT 0.00,
  coupon_code         VARCHAR(50)     DEFAULT NULL,
  payment_method      VARCHAR(60)     DEFAULT NULL,
  payment_status      ENUM('unpaid','paid','refunded') NOT NULL DEFAULT 'unpaid',
  notes               TEXT            DEFAULT NULL,
  -- snapshot of shipping address at time of order
  ship_full_name      VARCHAR(200)    DEFAULT NULL,
  ship_phone          VARCHAR(30)     DEFAULT NULL,
  ship_line1          VARCHAR(255)    DEFAULT NULL,
  ship_line2          VARCHAR(255)    DEFAULT NULL,
  ship_city           VARCHAR(100)    DEFAULT NULL,
  ship_province       VARCHAR(100)    DEFAULT NULL,
  ship_postal_code    VARCHAR(20)     DEFAULT NULL,
  ship_country        VARCHAR(80)     DEFAULT 'Philippines',
  created_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                      ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE INDEX idx_order_number (order_number),
  INDEX idx_user          (user_id),
  INDEX idx_status        (status),
  INDEX idx_payment_stat  (payment_status),
  INDEX idx_created       (created_at),

  CONSTRAINT fk_order_user
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  TABLE: order_items
-- ============================================================
CREATE TABLE IF NOT EXISTS order_items (
  id                INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  order_id          INT UNSIGNED  NOT NULL,
  product_id        INT UNSIGNED  DEFAULT NULL,
  product_name      VARCHAR(255)  NOT NULL   COMMENT 'Snapshot at time of order',
  product_sku       VARCHAR(100)  DEFAULT NULL,
  quantity          INT UNSIGNED  NOT NULL DEFAULT 1,
  unit_price        DECIMAL(10,2) NOT NULL,
  line_total        DECIMAL(12,2) NOT NULL,
  -- Custom product options
  custom_text       VARCHAR(255)  DEFAULT NULL,
  custom_color      VARCHAR(80)   DEFAULT NULL,
  custom_size       VARCHAR(80)   DEFAULT NULL,
  custom_font       VARCHAR(80)   DEFAULT NULL,
  custom_notes      TEXT          DEFAULT NULL,
  artwork_url       VARCHAR(512)  DEFAULT NULL,

  PRIMARY KEY (id),
  INDEX idx_order   (order_id),
  INDEX idx_product (product_id),

  CONSTRAINT fk_oi_order
    FOREIGN KEY (order_id) REFERENCES orders (id)
    ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT fk_oi_product
    FOREIGN KEY (product_id) REFERENCES products (id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  TABLE: coupons
-- ============================================================
CREATE TABLE IF NOT EXISTS coupons (
  id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  code            VARCHAR(50)     NOT NULL UNIQUE,
  description     VARCHAR(255)    DEFAULT NULL,
  discount_type   ENUM('percent','fixed') NOT NULL DEFAULT 'percent',
  discount_value  DECIMAL(10,2)   NOT NULL,
  min_order_total DECIMAL(10,2)   DEFAULT NULL  COMMENT 'Minimum cart value to apply',
  max_uses        INT UNSIGNED    DEFAULT NULL  COMMENT 'NULL = unlimited',
  used_count      INT UNSIGNED    NOT NULL DEFAULT 0,
  expires_at      DATETIME        DEFAULT NULL,
  is_active       TINYINT(1)      NOT NULL DEFAULT 1,
  created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE INDEX idx_code   (code),
  INDEX idx_active        (is_active),
  INDEX idx_expires       (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  TABLE: email_subscribers
--  Captures signups from the 5% OFF popup
-- ============================================================
CREATE TABLE IF NOT EXISTS email_subscribers (
  id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  first_name    VARCHAR(100)  DEFAULT NULL,
  email         VARCHAR(255)  NOT NULL UNIQUE,
  coupon_sent   TINYINT(1)    NOT NULL DEFAULT 0,
  subscribed_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  unsubscribed_at DATETIME    DEFAULT NULL,

  PRIMARY KEY (id),
  UNIQUE INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  TABLE: contact_messages  (from the Contact Us form)
-- ============================================================
CREATE TABLE IF NOT EXISTS contact_messages (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  full_name   VARCHAR(200)  NOT NULL,
  email       VARCHAR(255)  NOT NULL,
  phone       VARCHAR(30)   DEFAULT NULL,
  subject     VARCHAR(255)  DEFAULT NULL,
  message     TEXT          NOT NULL,
  is_read     TINYINT(1)    NOT NULL DEFAULT 0,
  replied_at  DATETIME      DEFAULT NULL,
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_read    (is_read),
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  TABLE: site_settings  (key-value store for CMS options)
-- ============================================================
CREATE TABLE IF NOT EXISTS site_settings (
  setting_key   VARCHAR(100) NOT NULL,
  setting_value LONGTEXT     DEFAULT NULL,
  setting_group VARCHAR(60)  NOT NULL DEFAULT 'general',
  updated_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
                             ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (setting_key),
  INDEX idx_group (setting_group)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  TABLE: sessions  (server-side user sessions)
-- ============================================================
CREATE TABLE IF NOT EXISTS sessions (
  session_id    VARCHAR(128)  NOT NULL,
  user_id       INT UNSIGNED  DEFAULT NULL,
  ip_address    VARCHAR(45)   DEFAULT NULL,
  user_agent    VARCHAR(512)  DEFAULT NULL,
  payload       LONGTEXT      DEFAULT NULL  COMMENT 'Serialized session data',
  last_activity DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                              ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (session_id),
  INDEX idx_user      (user_id),
  INDEX idx_activity  (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  SEED DATA
-- ============================================================

-- ---- Categories ----
INSERT INTO categories (name, slug, description, sort_order) VALUES
  ('Wristbands',           'wristbands',            'All types of custom wristbands',     1),
  ('Lapel Pins',           'lapel-pins',             'Custom lapel pins and cuff links',   2),
  ('Lanyards',             'lanyards',               'Polyester, tubular, woven & more',   3),
  ('Bags',                 'bags',                   'Tote bags, drawstring, sports bags', 4),
  ('Drinkware',            'drinkware',              'Mugs, tumblers, flasks & coolers',   5),
  ('Clothing & Apparel',   'clothing-and-apparel',   'Jackets, polo, hats, jerseys',       6),
  ('Office Supplies',      'office-supplies',        'Notebooks, pens, notepads, clocks',  7),
  ('Tech Products',        'tech-products',          'Flash drives, power banks, mousepads',8),
  ('Household Items',      'household-items',        'Umbrellas, magnets, coasters & more',9),
  ('Corporate Giveaways',  'corporate-giveaways',    'Bulk promotional items',             10);

-- Sub-categories for Wristbands
INSERT INTO categories (parent_id, name, slug, sort_order) VALUES
  (1, 'Silicone Wristbands', 'silicone-wristbands', 1),
  (1, 'Tyvek Wristbands',   'tyvek-wristbands',     2),
  (1, 'Fabric Wristbands',  'fabric-wristbands',    3),
  (1, 'Plastic Wristbands', 'plastic-wristbands',   4);

-- ---- Product Tags ----
INSERT INTO product_tags (name, slug) VALUES
  ('Silicone Wristbands', 'silicone-wristbands'),
  ('Election Wristbands', 'election-wristbands'),
  ('Lanyards',            'lanyards'),
  ('Lapel Pins',          'lapel-pins'),
  ('Drinkware',           'drinkware'),
  ('Corporate Gifts',     'corporate-gifts'),
  ('Tote Bags',           'tote-bags'),
  ('Rush Orders',         'rush-orders');

-- ---- Products ----
INSERT INTO products
  (category_id, name, slug, sku, short_desc, base_price, is_customizable, is_featured, is_active, sort_order)
VALUES
  (11, 'Debossed Wristbands',             'debossed-wristbands',               'WB-DEB-001',  'Classic debossed silicone wristbands',                8.00,  1, 1, 1, 1),
  (11, 'Imprinted Wristbands',            'imprinted-wristbands',              'WB-IMP-001',  'Full-color imprinted silicone wristbands',            8.00,  1, 1, 1, 2),
  (11, 'Deboss-fill Wristbands',          'deboss-fill-wristbands',            'WB-DBF-001',  'Debossed with color-filled text',                    11.00, 1, 1, 1, 3),
  (11, 'Embossed Wristbands',             'embossed-wristbands',               'WB-EMB-001',  'Raised text/logo silicone bands',                    11.00, 1, 0, 1, 4),
  (11, 'Dual Layer Wristbands',           'dual-layer-wristbands',             'WB-DUL-001',  'Two-layer contrasting silicone',                     13.00, 1, 0, 1, 5),
  (13, 'Fabric Wristbands',              'fabric-wristbands',                 'WB-FAB-001',  'Woven cloth event wristbands',                       23.40, 1, 0, 1, 6),
  (12, 'Tyvek Wristbands',              'tyvek-wristbands',                  'WB-TVK-001',  'Disposable Tyvek event wristbands',                   2.40, 1, 0, 1, 7),
  (14, 'Plastic Wristbands',            'plastic-wristbands',                'WB-PLS-001',  'Snap-on plastic wristbands',                          5.45, 1, 0, 1, 8),
  (11, 'Election Wristbands',            'election-wristbands',               'WB-ELX-001',  'Campaign and election silicone bands',                11.00, 1, 1, 1, 9),
  (2,  'Soft Enamel Lapel Pins',         'soft-enamel-lapel-pins',            'LP-SFT-001',  'Colorful soft enamel lapel pins',                    39.90, 1, 1, 1, 1),
  (2,  'Hard Enamel Lapel Pins',         'hard-enamel-lapel-pins',            'LP-HRD-001',  'Premium hard enamel lapel pins',                     53.90, 1, 0, 1, 2),
  (2,  'Printed Lapel Pins',             'printed-lapel-pins',                'LP-PRT-001',  'Full-color epoxy-coated printed pins',               39.90, 1, 0, 1, 3),
  (3,  'Tubular Lanyards',               'tubular-lanyards',                  'LN-TUB-001',  'Classic tubular lanyards with hardware',             32.90, 1, 1, 1, 1),
  (3,  'Polyester Lanyards',             'polyester-lanyards',                'LN-PLY-001',  'Budget-friendly flat polyester lanyards',            28.00, 1, 1, 1, 2),
  (3,  'Woven Lanyards',                 'woven-lanyards',                    'LN-WVN-001',  'Premium woven logo lanyards',                        39.90, 1, 0, 1, 3),
  (3,  'Full Color Dye-Sublimation Lanyards', 'full-color-dye-sublimation-lanyards', 'LN-DSL-001', 'Vivid full-color sublimation lanyards',        20.00, 1, 0, 1, 4),
  (4,  'Tote Bags',                      'tote-bags',                         'BG-TOT-001',  'Eco-friendly custom canvas tote bags',               37.00, 1, 1, 1, 1),
  (4,  'Custom Drawstring Bag',          'custom-drawstring-bag',             'BG-DRW-001',  'Lightweight drawstring sports bags',                 48.25, 1, 0, 1, 2),
  (5,  'Neoprene Can Coolers',           'neoprene-can-coolers',              'DW-CAN-001',  'Insulated neoprene can sleeves',                      0.00, 1, 0, 1, 1),
  (5,  'Campfire Mug 15 Oz',            'campfire-mug-15oz',                 'DW-MUG-001',  'Enamel-coated campfire-style mug',                  105.00, 1, 0, 1, 2);

-- ---- Pricing Tiers for Debossed Wristbands (product id=1) ----
INSERT INTO product_pricing_tiers (product_id, size_label, min_qty, max_qty, unit_price) VALUES
  (1, '1/2 inch', 100,   199,   45.00),
  (1, '1/2 inch', 200,   299,   25.00),
  (1, '1/2 inch', 300,   499,   20.00),
  (1, '1/2 inch', 500,   999,   15.00),
  (1, '1/2 inch', 1000,  1999,  10.00),
  (1, '1/2 inch', 2000,  2999,   9.00),
  (1, '1/2 inch', 3000,  4999,   9.00),
  (1, '1/2 inch', 5000,  9999,   8.50),
  (1, '1/2 inch', 10000, 19999,  8.50),
  (1, '1/2 inch', 20000, NULL,   8.00),
  (1, '1 inch',   100,   199,   85.00),
  (1, '1 inch',   200,   299,   51.00),
  (1, '1 inch',   300,   499,   46.00),
  (1, '1 inch',   500,   999,   40.00),
  (1, '1 inch',   1000,  1999,  27.00),
  (1, '1 inch',   2000,  2999,  24.00),
  (1, '1 inch',   3000,  4999,  21.00),
  (1, '1 inch',   5000,  9999,  20.00),
  (1, '1 inch',   10000, 19999, 19.00),
  (1, '1 inch',   20000, NULL,  18.00);

-- ---- Hero Banners ----
INSERT INTO hero_banners (title, subtitle, body_text, eyebrow, cta_label, cta_url, bg_class, sort_order) VALUES
  ('Silicone Wristbands', 'In 10 Days', 'We specialize in rush orders as we understand the importance of getting them on time.', 'Get Your', 'View Pricing', '/wristband-prices', 'item-10-days', 1),
  ('Custom Lapel Pins',   NULL,         'Stylish and durable custom lapel pins for events, recognition, and giveaways.',          'Showcase Your Brand with', 'Customize Now', '/lapel-pins', 'item-lapel-pins', 2),
  ('Custom Logo Tote Bags', NULL,       'Eco-friendly tote bags customized with your logo for events and promotions.',            'Stand Out with', 'Customize Now', '/tote-bags', 'item-tote-bags', 3),
  ('Support School Spirit', NULL,       'Boost school pride with custom merch like wristbands and tote bags.',                   'Unite Your Team:', 'Customize Now', '/wristbands', 'item-wristbands', 4),
  ('Personalized Can Coolers', NULL,    'Custom can coolers to keep drinks chilled and promote your brand.',                     'Keep It Cool with', 'Customize Now', '/can-coolers', 'item-can-coolers', 5),
  ('Custom Lanyards', 'for Your Company', 'Perfect for staff IDs, conferences, and corporate events.',                           NULL, 'Customize Now', '/lanyards', 'item-custom-lanyards', 6);

-- ---- Clients ----
INSERT INTO clients (name, logo_url, sort_order) VALUES
  ('I Am Ninoy',           '/images/clients/iamninoy.png',                1),
  ('Ateneo',               '/images/clients/ateneo.png',                  2),
  ('Duterte Campaign',     '/images/clients/duterte.png',                 3),
  ('PHC',                  '/images/clients/phc.png',                     4),
  ('VURS',                 '/images/clients/vurs.png',                    5),
  ('LEGO',                 '/images/clients/lego.png',                    6),
  ('Xavier School',        '/images/clients/xavier-school.png',           7),
  ('Asian Development Bank','/images/clients/asian-development-bank.png', 8),
  ('International School Manila','/images/clients/ismanila.png',          9);

-- ---- Coupons ----
INSERT INTO coupons (code, description, discount_type, discount_value, is_active) VALUES
  ('PBALL5OFF', '5% off entire order — popup promotion', 'percent', 5.00, 1),
  ('WELCOME10', '10% off first order',                   'percent', 10.00, 1),
  ('RUSH100',   '₱100 off rush orders',                  'fixed',   100.00, 1);

-- ---- Site Settings ----
INSERT INTO site_settings (setting_key, setting_value, setting_group) VALUES
  ('site_name',          'Pinoyballers',                           'general'),
  ('site_tagline',       'Customized Silicone Wristbands & Baller IDs Philippines', 'general'),
  ('contact_email',      'sales@pinoyballers.com',                 'contact'),
  ('contact_phone_1',    '+63 927-877-5212',                       'contact'),
  ('contact_phone_2',    '+632 8929-9845',                         'contact'),
  ('address_line1',      'Suite 2B, 17 Bayanihan St',              'contact'),
  ('address_city',       'Quezon City',                            'contact'),
  ('address_postal',     '1104',                                   'contact'),
  ('address_country',    'Philippines',                            'contact'),
  ('facebook_url',       'https://www.facebook.com/PinoyBallerBands', 'social'),
  ('twitter_url',        'https://twitter.com/PinoyBallersCom',    'social'),
  ('pinterest_url',      'https://www.pinterest.com/pinoyballers', 'social'),
  ('instagram_url',      'https://www.instagram.com/pinoyballerscom/', 'social'),
  ('linkedin_url',       'https://www.linkedin.com/company/pinoy-ballers', 'social'),
  ('google_maps_embed',  'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3860.173504972463!2d121.03253497474016!3d14.64609038584566!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b7d608abae61%3A0x38d416cc154f4a00!2sPinoy%20Ballers!5e0!3m2!1sen!2sph!4v1690426232973!5m2!1sen!2sph', 'contact'),
  ('currency_symbol',    '₱',                                      'shop'),
  ('vat_rate_pct',       '12',                                     'shop'),
  ('popup_delay_sec',    '9',                                      'marketing'),
  ('popup_cookie_hours', '24',                                     'marketing');

-- ============================================================
--  USEFUL VIEWS
-- ============================================================

-- View: products with their category name and tag list
CREATE OR REPLACE VIEW v_products AS
SELECT
  p.id,
  p.name,
  p.slug,
  p.sku,
  p.base_price,
  p.sale_price,
  p.thumbnail_url,
  p.is_featured,
  p.is_active,
  c.name        AS category_name,
  c.slug        AS category_slug,
  GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ', ') AS tags
FROM products p
LEFT JOIN categories c      ON c.id = p.category_id
LEFT JOIN product_tag_map m ON m.product_id = p.id
LEFT JOIN product_tags t    ON t.id = m.tag_id
WHERE p.is_active = 1
GROUP BY p.id;

-- View: order summary per user
CREATE OR REPLACE VIEW v_user_orders AS
SELECT
  o.id            AS order_id,
  o.order_number,
  o.status,
  o.total_amount,
  o.payment_status,
  o.created_at,
  u.email,
  CONCAT(u.first_name,' ', u.last_name) AS customer_name
FROM orders o
LEFT JOIN users u ON u.id = o.user_id;

-- ============================================================
--  STORED PROCEDURES
-- ============================================================

DELIMITER $$

-- Get the correct unit price for a product given a quantity and size
CREATE PROCEDURE get_tier_price (
  IN  p_product_id INT UNSIGNED,
  IN  p_size_label VARCHAR(50),
  IN  p_quantity   INT UNSIGNED,
  OUT p_unit_price DECIMAL(10,2)
)
BEGIN
  SELECT unit_price INTO p_unit_price
  FROM product_pricing_tiers
  WHERE product_id  = p_product_id
    AND (size_label = p_size_label OR size_label IS NULL)
    AND min_qty    <= p_quantity
    AND (max_qty   >= p_quantity OR max_qty IS NULL)
  ORDER BY min_qty DESC
  LIMIT 1;

  IF p_unit_price IS NULL THEN
    SELECT base_price INTO p_unit_price
    FROM products WHERE id = p_product_id;
  END IF;
END$$

-- Validate and apply a coupon code; returns discount amount
CREATE PROCEDURE apply_coupon (
  IN  p_code          VARCHAR(50),
  IN  p_order_total   DECIMAL(12,2),
  OUT p_discount      DECIMAL(12,2),
  OUT p_message       VARCHAR(200)
)
BEGIN
  DECLARE v_type   VARCHAR(20);
  DECLARE v_value  DECIMAL(10,2);
  DECLARE v_min    DECIMAL(10,2);
  DECLARE v_max    INT UNSIGNED;
  DECLARE v_used   INT UNSIGNED;
  DECLARE v_exp    DATETIME;
  DECLARE v_active TINYINT;

  SET p_discount = 0.00;

  SELECT discount_type, discount_value, min_order_total,
         max_uses, used_count, expires_at, is_active
  INTO   v_type, v_value, v_min, v_max, v_used, v_exp, v_active
  FROM   coupons
  WHERE  code = p_code
  LIMIT  1;

  IF v_type IS NULL THEN
    SET p_message = 'Coupon code not found.';
  ELSEIF v_active = 0 THEN
    SET p_message = 'Coupon is no longer active.';
  ELSEIF v_exp IS NOT NULL AND NOW() > v_exp THEN
    SET p_message = 'Coupon has expired.';
  ELSEIF v_max IS NOT NULL AND v_used >= v_max THEN
    SET p_message = 'Coupon usage limit reached.';
  ELSEIF v_min IS NOT NULL AND p_order_total < v_min THEN
    SET p_message = CONCAT('Minimum order of ₱', v_min, ' required.');
  ELSE
    IF v_type = 'percent' THEN
      SET p_discount = ROUND(p_order_total * v_value / 100, 2);
    ELSE
      SET p_discount = LEAST(v_value, p_order_total);
    END IF;
    SET p_message = 'Coupon applied successfully.';
    UPDATE coupons SET used_count = used_count + 1 WHERE code = p_code;
  END IF;
END$$

DELIMITER ;

-- ============================================================
--  INDEXES for performance
-- ============================================================
-- Full-text search on products
ALTER TABLE products
  ADD FULLTEXT INDEX ft_product_search (name, short_desc, description);

-- ============================================================
--  END OF SCHEMA
-- ============================================================

-- ============================================================
-- ADDED: AUTH SYSTEM ADDITIONS
-- Supporting the Login & Sign Up modal forms.
-- The `users` table already exists above — these additions
-- extend it with password-reset tokens and login audit logs.
-- ============================================================

-- ── TABLE: password_reset_tokens ────────────────────────────
-- Stores short-lived tokens emailed to users for password reset.
-- Backend: INSERT here when user clicks "Forgot Password".
--          DELETE here after token is consumed or expired.
CREATE TABLE IF NOT EXISTS password_reset_tokens (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  user_id     INT UNSIGNED  NOT NULL,
  token       VARCHAR(128)  NOT NULL UNIQUE  COMMENT 'Secure random token (SHA-256 hex)',
  expires_at  DATETIME      NOT NULL         COMMENT 'Typically NOW() + 1 HOUR',
  used        TINYINT(1)    NOT NULL DEFAULT 0,
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_token   (token),
  INDEX idx_user    (user_id),
  INDEX idx_expires (expires_at),

  CONSTRAINT fk_prt_user
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── TABLE: login_audit_log ───────────────────────────────────
-- Logs every login attempt (success or failure) for security.
-- Backend: INSERT here on every POST /api/auth/login attempt.
CREATE TABLE IF NOT EXISTS login_audit_log (
  id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  user_id      INT UNSIGNED  DEFAULT NULL   COMMENT 'NULL if email not found',
  email_tried  VARCHAR(255)  NOT NULL       COMMENT 'Email entered at login',
  ip_address   VARCHAR(45)   DEFAULT NULL,
  user_agent   VARCHAR(512)  DEFAULT NULL,
  success      TINYINT(1)    NOT NULL DEFAULT 0,
  fail_reason  VARCHAR(100)  DEFAULT NULL   COMMENT 'e.g. wrong_password, not_found, inactive',
  created_at   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_user      (user_id),
  INDEX idx_email     (email_tried),
  INDEX idx_ip        (ip_address),
  INDEX idx_success   (success),
  INDEX idx_created   (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- ADDED: STORED PROCEDURES for Auth Modal
-- ============================================================

DELIMITER $$

-- ── PROCEDURE: register_user ─────────────────────────────────
-- Called by backend on POST /api/auth/register.
-- Backend passes the bcrypt hash — never the raw password.
-- Returns: 'ok' if created, 'duplicate_email' if taken.
CREATE PROCEDURE register_user (
  IN  p_first_name    VARCHAR(100),
  IN  p_last_name     VARCHAR(100),
  IN  p_email         VARCHAR(255),
  IN  p_phone         VARCHAR(30),
  IN  p_password_hash VARCHAR(255),
  OUT p_result        VARCHAR(50),
  OUT p_new_user_id   INT UNSIGNED
)
BEGIN
  -- Check for duplicate email
  IF EXISTS (SELECT 1 FROM users WHERE email = p_email) THEN
    SET p_result      = 'duplicate_email';
    SET p_new_user_id = NULL;
  ELSE
    INSERT INTO users (first_name, last_name, email, phone, password_hash, role)
    VALUES (p_first_name, p_last_name, p_email, p_phone, p_password_hash, 'customer');

    SET p_new_user_id = LAST_INSERT_ID();
    SET p_result      = 'ok';
  END IF;
END$$

-- ── PROCEDURE: login_user ────────────────────────────────────
-- Called by backend on POST /api/auth/login.
-- Returns the stored bcrypt hash so the backend can compare it.
-- Also updates last_login_at on success and logs the attempt.
CREATE PROCEDURE login_user (
  IN  p_email        VARCHAR(255),
  IN  p_ip_address   VARCHAR(45),
  IN  p_user_agent   VARCHAR(512),
  OUT p_user_id      INT UNSIGNED,
  OUT p_password_hash VARCHAR(255),
  OUT p_first_name   VARCHAR(100),
  OUT p_role         VARCHAR(20),
  OUT p_result       VARCHAR(50)
)
BEGIN
  DECLARE v_is_active TINYINT;

  SELECT id, password_hash, first_name, role, is_active
  INTO   p_user_id, p_password_hash, p_first_name, p_role, v_is_active
  FROM   users
  WHERE  email = p_email
  LIMIT  1;

  IF p_user_id IS NULL THEN
    -- Email not found
    SET p_result = 'not_found';
    -- Log failed attempt
    INSERT INTO login_audit_log (email_tried, ip_address, user_agent, success, fail_reason)
    VALUES (p_email, p_ip_address, p_user_agent, 0, 'not_found');
  ELSEIF v_is_active = 0 THEN
    -- Account disabled
    SET p_result = 'inactive';
    INSERT INTO login_audit_log (user_id, email_tried, ip_address, user_agent, success, fail_reason)
    VALUES (p_user_id, p_email, p_ip_address, p_user_agent, 0, 'inactive');
  ELSE
    -- Return hash so backend can verify with bcrypt
    -- Backend must call this procedure, verify hash, then log success/fail
    SET p_result = 'found';
  END IF;
END$$

-- ── PROCEDURE: record_login_success ─────────────────────────
-- Call this after backend confirms bcrypt hash matches.
CREATE PROCEDURE record_login_success (
  IN p_user_id    INT UNSIGNED,
  IN p_email      VARCHAR(255),
  IN p_ip_address VARCHAR(45),
  IN p_user_agent VARCHAR(512)
)
BEGIN
  -- Update last login timestamp
  UPDATE users SET last_login_at = NOW() WHERE id = p_user_id;

  -- Audit log
  INSERT INTO login_audit_log (user_id, email_tried, ip_address, user_agent, success)
  VALUES (p_user_id, p_email, p_ip_address, p_user_agent, 1);
END$$

-- ── PROCEDURE: cleanup_expired_tokens ───────────────────────
-- Schedule this with a cron job (e.g. every hour) to prune
-- expired password reset tokens and old sessions.
CREATE PROCEDURE cleanup_expired_tokens ()
BEGIN
  -- Remove expired/used password reset tokens
  DELETE FROM password_reset_tokens
  WHERE  expires_at < NOW() OR used = 1;

  -- Remove sessions idle for more than 7 days
  DELETE FROM sessions
  WHERE  last_activity < DATE_SUB(NOW(), INTERVAL 7 DAY);
END$$

DELIMITER ;

-- ============================================================
-- END ADDED: AUTH SYSTEM ADDITIONS
-- ============================================================