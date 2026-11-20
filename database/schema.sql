-- ============================================================
--  CREATIVEKIT 3A — Comprehensive Database Schema
--  Engine : InnoDB | Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
--  Supports: Full Product Catalog, Variants, 3-Step Quote System,
--            Authentication, Orders, Cart, CMS Settings
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
--  TABLE: product_variants
--  Stores individual variant items under a product line
-- ============================================================
CREATE TABLE IF NOT EXISTS product_variants (
  id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  product_id      INT UNSIGNED    NOT NULL              COMMENT 'Parent product',
  variant_type    VARCHAR(100)    DEFAULT NULL          COMMENT 'Filter tab key (e.g. single, dual, eco)',
  name            VARCHAR(255)    NOT NULL              COMMENT 'Variant display name',
  slug            VARCHAR(270)    NOT NULL UNIQUE       COMMENT 'Slug for deep linking',
  specs           VARCHAR(500)    DEFAULT NULL          COMMENT 'Short spec line shown on card',
  thumbnail_url   VARCHAR(512)    DEFAULT NULL,
  base_price      DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  sale_price      DECIMAL(10,2)   DEFAULT NULL,
  badge_label     VARCHAR(80)     DEFAULT NULL          COMMENT 'e.g. Best Seller, New, Premium',
  is_active       TINYINT(1)      NOT NULL DEFAULT 1,
  sort_order      SMALLINT        NOT NULL DEFAULT 0,
  created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                  ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_pv_product     (product_id),
  INDEX idx_pv_slug        (slug),
  INDEX idx_pv_active      (is_active),
  INDEX idx_pv_type        (variant_type),
  INDEX idx_pv_sort        (sort_order),

  CONSTRAINT fk_pv_product
    FOREIGN KEY (product_id) REFERENCES products (id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  TABLE: product_variant_types
--  Filter tab definitions per product
-- ============================================================
CREATE TABLE IF NOT EXISTS product_variant_types (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  product_id  INT UNSIGNED  NOT NULL,
  type_key    VARCHAR(100)  NOT NULL  COMMENT 'Matches product_variants.variant_type',
  type_label  VARCHAR(150)  NOT NULL  COMMENT 'Display label on the tab',
  sort_order  SMALLINT      NOT NULL DEFAULT 0,
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_pvt_product_key (product_id, type_key),
  INDEX idx_pvt_product (product_id),

  CONSTRAINT fk_pvt_product
    FOREIGN KEY (product_id) REFERENCES products (id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  TABLE: product_images
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
-- ============================================================
CREATE TABLE IF NOT EXISTS product_pricing_tiers (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  product_id  INT UNSIGNED  NOT NULL,
  size_label  VARCHAR(50)   DEFAULT NULL,
  min_qty     INT UNSIGNED  NOT NULL,
  max_qty     INT UNSIGNED  DEFAULT NULL,
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
--  TABLE: product_tag_map
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
--  TABLE: quote_requests
--  Captures multi-step quotation requests from clients
-- ============================================================
CREATE TABLE IF NOT EXISTS quote_requests (
  id                      INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  quote_ref               VARCHAR(40)   NOT NULL UNIQUE,
  full_name               VARCHAR(200)  NOT NULL,
  company_name            VARCHAR(200)  DEFAULT NULL,
  email                   VARCHAR(255)  NOT NULL,
  phone                   VARCHAR(50)   NOT NULL,
  product_type            VARCHAR(100)  NOT NULL,
  quantity_range          VARCHAR(50)   NOT NULL,
  target_budget           VARCHAR(50)   DEFAULT NULL,
  target_deadline         DATE          DEFAULT NULL,
  project_details         TEXT          DEFAULT NULL,
  customization_options   JSON          DEFAULT NULL,
  source_url              VARCHAR(512)  DEFAULT NULL,
  status                  ENUM('new', 'contacted', 'quoted', 'in_production', 'completed', 'cancelled') NOT NULL DEFAULT 'new',
  created_at              DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at              DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE INDEX idx_qr_ref     (quote_ref),
  INDEX idx_qr_email   (email),
  INDEX idx_qr_status  (status),
  INDEX idx_qr_product (product_type),
  INDEX idx_qr_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  TABLE: clients (Partner & client logos)
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
--  TABLE: hero_banners
-- ============================================================
CREATE TABLE IF NOT EXISTS hero_banners (
  id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  title           VARCHAR(200)  DEFAULT NULL,
  subtitle        VARCHAR(200)  DEFAULT NULL,
  body_text       TEXT          DEFAULT NULL,
  eyebrow         VARCHAR(100)  DEFAULT NULL,
  image_url       VARCHAR(512)  DEFAULT NULL,
  cta_label       VARCHAR(80)   DEFAULT NULL,
  cta_url         VARCHAR(512)  DEFAULT NULL,
  bg_class        VARCHAR(80)   DEFAULT NULL,
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
  password_hash   VARCHAR(255)  NOT NULL,
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
--  TABLE: addresses
-- ============================================================
CREATE TABLE IF NOT EXISTS addresses (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  user_id     INT UNSIGNED  NOT NULL,
  label       VARCHAR(50)   DEFAULT 'Office',
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
  user_id             INT UNSIGNED    DEFAULT NULL,
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
  product_name      VARCHAR(255)  NOT NULL,
  product_sku       VARCHAR(100)  DEFAULT NULL,
  quantity          INT UNSIGNED  NOT NULL DEFAULT 1,
  unit_price        DECIMAL(10,2) NOT NULL,
  line_total        DECIMAL(12,2) NOT NULL,
  custom_text       VARCHAR(255)  DEFAULT NULL,
  custom_color      VARCHAR(80)   DEFAULT NULL,
  custom_size       VARCHAR(80)   DEFAULT NULL,
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
  min_order_total DECIMAL(10,2)   DEFAULT NULL,
  max_uses        INT UNSIGNED    DEFAULT NULL,
  used_count      INT UNSIGNED    NOT NULL DEFAULT 0,
  expires_at      DATETIME        DEFAULT NULL,
  is_active       TINYINT(1)      NOT NULL DEFAULT 1,
  created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE INDEX idx_code   (code),
  INDEX idx_active        (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  TABLE: email_subscribers
-- ============================================================
CREATE TABLE IF NOT EXISTS email_subscribers (
  id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  first_name      VARCHAR(100)  DEFAULT NULL,
  email           VARCHAR(255)  NOT NULL UNIQUE,
  coupon_sent     TINYINT(1)    NOT NULL DEFAULT 0,
  subscribed_at   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  unsubscribed_at DATETIME      DEFAULT NULL,

  PRIMARY KEY (id),
  UNIQUE INDEX idx_sub_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  TABLE: contact_messages
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
--  TABLE: site_settings
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
--  TABLE: sessions
-- ============================================================
CREATE TABLE IF NOT EXISTS sessions (
  session_id    VARCHAR(128)  NOT NULL,
  user_id       INT UNSIGNED  DEFAULT NULL,
  ip_address    VARCHAR(45)   DEFAULT NULL,
  user_agent    VARCHAR(512)  DEFAULT NULL,
  payload       LONGTEXT      DEFAULT NULL,
  last_activity DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                              ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (session_id),
  INDEX idx_user      (user_id),
  INDEX idx_activity  (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  TABLE: password_reset_tokens
-- ============================================================
CREATE TABLE IF NOT EXISTS password_reset_tokens (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  user_id     INT UNSIGNED  NOT NULL,
  token       VARCHAR(128)  NOT NULL UNIQUE,
  expires_at  DATETIME      NOT NULL,
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

-- ============================================================
--  TABLE: login_audit_log
-- ============================================================
CREATE TABLE IF NOT EXISTS login_audit_log (
  id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  user_id      INT UNSIGNED  DEFAULT NULL,
  email_tried  VARCHAR(255)  NOT NULL,
  ip_address   VARCHAR(45)   DEFAULT NULL,
  user_agent   VARCHAR(512)  DEFAULT NULL,
  success      TINYINT(1)    NOT NULL DEFAULT 0,
  fail_reason  VARCHAR(100)  DEFAULT NULL,
  created_at   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_audit_user    (user_id),
  INDEX idx_audit_email   (email_tried),
  INDEX idx_audit_success (success),
  INDEX idx_audit_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
--  SEED DATA: CREATIVEKIT 3A
-- ============================================================

-- ---- Core Categories (Main Level) ----
INSERT INTO categories (id, parent_id, name, slug, description, sort_order) VALUES
  (1, NULL, 'Accessories & Gadgets', 'accessories-gadgets', 'Car accessories, computer essentials, desk items, and tech gadgets', 1),
  (2, NULL, 'Apparel',               'apparel',             'Corporate jackets, varsity jackets, hoodies, caps, and shirts',         2),
  (3, NULL, 'Bags & Pouches',        'bags-pouches',        'Backpacks, eco shopping totes, laptop sleeves, and duffels',            3),
  (4, NULL, 'Drinkware',             'drinkware',           'Bamboo tumblers, stainless flasks, ceramic mugs, and sports bottles',   4),
  (5, NULL, 'Gift Set',              'giftset',             'Executive gift boxes, onboarding kits, and sustainable care packages',   5),
  (6, NULL, 'Home Living',           'home-living',         'Cutlery sets, personal care, outdoor chairs, and lifestyle goods',      6),
  (7, NULL, 'Pen & Paper',           'pen-paper',           'Eco bamboo pens, premium metal pens, notebooks, and desk calendars',    7),
  (8, NULL, 'PU Leather',            'pu-leather',          'PU leather wallets, card cases, desk mats, organizers, and keychains',  8)
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description);

-- ---- Sub-Categories ----
INSERT INTO categories (id, parent_id, name, slug, sort_order) VALUES
  -- Subcats for Accessories & Gadgets (1)
  (101, 1, 'Car Accessories',       'car',       1),
  (102, 1, 'Computer Accessories',  'computer',  2),
  (103, 1, 'Desk Accessories',      'desk',      3),
  (104, 1, 'Mobile Accessories',    'mobile',    4),
  (105, 1, 'Gadgets',               'gadgets',   5),

  -- Subcats for Apparel (2)
  (201, 2, 'Caps',                  'caps',      1),
  (202, 2, 'Jackets',               'jackets',   2),
  (203, 2, 'Shirts',                'shirts',    3),

  -- Subcats for Bags & Pouches (3)
  (301, 3, 'Backpacks',             'backpacks', 1),
  (302, 3, 'Eco Bags',              'ecobags',   2),
  (303, 3, 'Laptop Bags',           'laptop',    3),
  (304, 3, 'Tote Bags',             'tote',      4),
  (305, 3, 'Travel Bags',           'travel',    5),
  (306, 3, 'Lunch Bags',            'lunch',     6),
  (307, 3, 'Organizer Pouches',     'organizer', 7),

  -- Subcats for Drinkware (4)
  (401, 4, 'Tumblers & Flasks',     'tumbler',   1),
  (402, 4, 'Mugs & Cups',           'mugs',      2),
  (403, 4, 'Water Bottles',         'bottles',   3),
  (404, 4, 'Eco-Friendly',          'eco',       4),

  -- Subcats for Gift Set (5)
  (501, 5, 'Corporate',             'corporate', 1),
  (502, 5, 'Lifestyle',             'lifestyle', 2),
  (503, 5, 'Health & Wellness',     'wellness',  3),

  -- Subcats for Home Living (6)
  (601, 6, 'Kitchen & Dining',      'kitchen',   1),
  (602, 6, 'Personal Care',         'personal',  2),
  (603, 6, 'Outdoor & Lifestyle',   'outdoor',   3),

  -- Subcats for Pen & Paper (7)
  (701, 7, 'Pens',                  'pen',       1),
  (702, 7, 'Paper & Notebooks',     'paper',     2),

  -- Subcats for PU Leather (8)
  (801, 8, 'Wallets & Cases',       'wallets',   1),
  (802, 8, 'Organizers & Bags',     'organizers',2),
  (803, 8, 'Keychains & Tags',      'keychains', 3)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- ---- Product Tags ----
INSERT INTO product_tags (name, slug) VALUES
  ('Best Seller',          'best-seller'),
  ('New Arrival',          'new-arrival'),
  ('Eco-Friendly',         'eco-friendly'),
  ('Executive Corporate',  'executive-corporate'),
  ('Fast Delivery',        'fast-delivery'),
  ('Laser Engraving',      'laser-engraving'),
  ('Silk Screen Imprint',  'silk-screen-imprint')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- ---- Core Products ----
INSERT INTO products
  (id, category_id, name, slug, sku, short_desc, thumbnail_url, base_price, is_customizable, is_featured, is_active, sort_order)
VALUES
  -- Accessories & Gadgets
  (1,  101, 'Car Charger Adapters',          'car-charger-adapter',         'ACC-CAR-001', 'Custom-branded single, dual, and fast car charger adapters', 'assets/images/car-charger-adapters.png', 85.00,  1, 1, 1, 1),
  (2,  101, 'Car Organizers & Holders',      'car-organizers',              'ACC-CAR-002', 'Seat-back and console organizers for brand visibility on the go', 'assets/images/car-organizer.png', 120.00, 1, 1, 1, 2),
  (3,  101, 'Dash Cams',                     'car-dashcams',                'ACC-CAR-003', 'HD dash cameras with customizable logo placement', 'assets/images/dash-cam.png', 450.00, 1, 1, 1, 3),
  (4,  102, 'Audio Accessories',             'computer-audio',              'ACC-AUD-001', 'Noise-canceling earphones and portable speaker gifts', 'assets/images/audio-accessories.png', 95.00,  1, 0, 1, 4),
  (5,  102, 'Mouse & Mouse Pads',            'computer-mouse',              'ACC-MSE-001', 'Non-slip speed mouse pads and ergonomic wireless mice', 'assets/images/mouse-pad.png', 75.00,  1, 0, 1, 5),
  (6,  104, 'Mobile Power Banks',            'mobile-powerbanks',           'ACC-PBK-001', 'High-capacity power banks with LED battery display', 'assets/images/mobile-powerbank.png', 350.00, 1, 1, 1, 6),

  -- Apparel
  (7,  202, 'Corporate Jackets',             'jackets-corporate',           'APP-JKT-001', 'Tailored corporate windbreakers and executive weather-resistant jackets', 'assets/images/corporate-jacket.png', 720.00, 1, 1, 1, 7),
  (8,  202, 'Bomber Jackets',                'jacket-bomber',               'APP-JKT-002', 'Modern streetwear and company bomber jackets with custom embroidery', 'assets/images/bomber-jacket.png', 650.00, 1, 0, 1, 8),
  (9,  201, 'Baseball Cap',                  'cap-baseball',                'APP-CAP-001', 'Structured 6-panel twill baseball caps with adjustable strap', 'assets/images/baseball-cap.png', 180.00, 1, 0, 1, 9),
  (10, 203, 'Collared Shirt',                'shirt-collared',              'APP-SHT-001', 'Breathable pique cotton polo shirts for company uniforms', 'assets/images/collared-shirt.png', 280.00, 1, 0, 1, 10),

  -- Bags & Pouches
  (11, 303, 'Laptop Backpacks',              'laptop-backpack',             'BAG-LPT-001', 'Padded compartments for 15.6 inch laptops with USB charging pass-through', 'assets/images/laptop-backpack.png', 520.00, 1, 1, 1, 11),
  (12, 304, 'Classic Zip Totes',             'tote-bag-classic-zip',        'BAG-TOT-001', 'Heavy-duty eco canvas tote bags with reinforced zipper closure', 'assets/images/tote-bag.png', 120.00, 1, 1, 1, 12),
  (13, 302, 'Foldable Shopping Totes',       'ecobags-shopping-tote',       'BAG-ECO-001', 'Compact reusable fold-away bags for sustainable corporate gifting', 'assets/images/eco-bag.png', 95.00,  1, 0, 1, 13),

  -- Drinkware
  (14, 401, 'Bamboo Tumblers',               'tumbler-bamboo',              'DRK-TMB-001', 'Eco-friendly natural bamboo exterior with double-wall stainless steel interior', 'assets/images/bamboo-tumbler.png', 220.00, 1, 1, 1, 14),
  (15, 401, 'Stainless Steel Tumblers',      'tumbler-stainless',           'DRK-TMB-002', 'Vacuum insulated travel tumblers keeping drinks cold 24hrs / hot 12hrs', 'assets/images/stainless-tumbler.png', 310.00, 1, 1, 1, 15),
  (16, 402, 'Ceramic Mugs',                  'mugs-ceramic',                'DRK-MUG-001', 'Glossy ceramic mugs with full-color wrap dye-sublimation imprint', 'assets/images/ceramic-mug.png', 95.00,  1, 0, 1, 16),

  -- Gift Sets
  (17, 501, 'Employee Onboarding Sets',      'gift-set-employee-onboarding','GFT-SET-001', 'Welcome box complete with notebook, tumbler, pen, and USB flash drive', 'assets/images/onboarding-set.png', 890.00, 1, 1, 1, 17),
  (18, 501, 'Desk Essentials Gift Sets',      'gift-set-desk-essentials',    'GFT-SET-002', 'Executive wooden desk set with wireless charger and pen stand', 'assets/images/desk-gift-set.png', 650.00, 1, 0, 1, 18),

  -- Pen & Paper
  (19, 701, 'Bamboo Pens',                   'pen-bamboo',                  'PEN-BAM-001', 'Laser-engraved natural bamboo ballpoint pens with black German ink', 'assets/images/bamboo-pen.png', 35.00,  1, 1, 1, 19),
  (20, 702, 'Custom Notebooks & Journals',   'paper-notebooks-journals',    'PPR-NBK-001', 'Hardbound PU and craft paper journals with ribbon bookmark and elastic band', 'assets/images/custom-notebook.png', 95.00, 1, 1, 1, 20),

  -- PU Leather
  (21, 801, 'Wallets',                       'pu-leather-wallets',          'LEA-WLT-001', 'Slim bifold PU leather wallets with debossed corporate logo', 'assets/images/leather-wallet.png', 195.00, 1, 0, 1, 21),
  (22, 803, 'Keychains',                     'pu-leather-keychains',        'LEA-KEY-001', 'Stitched leather key fobs with polished metallic hardware', 'assets/images/leather-keychain.png', 65.00,  1, 0, 1, 22)
ON DUPLICATE KEY UPDATE name = VALUES(name), base_price = VALUES(base_price);

-- ---- Sample Variants for Core Showcase Items ----
-- Variants for Car Charger Adapters (Product ID 1)
INSERT INTO product_variant_types (product_id, type_key, type_label, sort_order) VALUES
  (1, 'single',   'Single Port',  1),
  (1, 'dual',     'Dual Port',    2),
  (1, 'fast',     'Fast Charge',  3)
ON DUPLICATE KEY UPDATE type_label = VALUES(type_label);

INSERT INTO product_variants (product_id, variant_type, name, slug, specs, thumbnail_url, base_price, badge_label, sort_order) VALUES
  (1, 'single', 'Single USB-A Car Charger',   'car-charger-single-usba', '5V / 2.1A • USB-A • Compact Body',       'assets/images/1.png',               85.00,  NULL,          1),
  (1, 'single', 'Single USB-C Car Charger',   'car-charger-single-usbc', '5V / 3A • USB-C • Compact Body',         'assets/images/usb-c-adapter.png',   95.00,  'New',         2),
  (1, 'dual',   'Dual Port USB-A Charger',    'car-charger-dual-usba',   '2x USB-A • 3.1A Total • Logo Printing',  'assets/images/dual-usb-adapter.png',110.00, 'Best Seller', 3),
  (1, 'fast',   '20W USB-C PD Fast Charger',  'car-charger-pd20',        'USB-C PD • 20W • Fast Charge Chip',      'assets/images/car-charger-adapters.png', 165.00, 'Premium', 4)
ON DUPLICATE KEY UPDATE name = VALUES(name), specs = VALUES(specs);

-- Variants for Bamboo Tumblers (Product ID 14)
INSERT INTO product_variant_types (product_id, type_key, type_label, sort_order) VALUES
  (14, 'standard', 'Standard 450ml', 1),
  (14, 'tea',      'With Tea Infuser', 2),
  (14, 'handle',   'With Steel Handle', 3)
ON DUPLICATE KEY UPDATE type_label = VALUES(type_label);

INSERT INTO product_variants (product_id, variant_type, name, slug, specs, thumbnail_url, base_price, badge_label, sort_order) VALUES
  (14, 'standard', 'Bamboo Tumbler 450ml',        'bamboo-tumbler-450ml', '450ml • 304 Stainless Steel • Laser Engraved', 'assets/images/bamboo-tumbler.png', 220.00, 'Best Seller', 1),
  (14, 'tea',      'Bamboo Tumbler + Tea Strainer', 'bamboo-tumbler-strainer', '500ml • Removable Infuser Basket', 'assets/images/bamboo-tumbler.png', 250.00, 'New', 2),
  (14, 'handle',   'Bamboo Flask with Lid Handle', 'bamboo-tumbler-handle', '530ml • Ergonomic Carry Loop Lid', 'assets/images/bamboo-tumbler.png', 280.00, 'Premium', 3)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- ---- Hero Banners ----
INSERT INTO hero_banners (title, subtitle, body_text, eyebrow, image_url, cta_label, cta_url, bg_class, sort_order) VALUES
  ('Bamboo Tumbler', 'Eco-Friendly Drinkware', 'Keep your beverages hot or cold while showcasing your eco-conscious brand with custom laser-engraved bamboo tumblers.', 'Sustainable Sips:', 'assets/images/bamboo-tumbler.png', 'Explore Drinkware', 'category-page.html?cat=drinkware', 'item-bamboo-tumbler', 1),
  ('Custom Corporate Jackets', 'Premium Outerwear', 'Elevate your team image with weather-resistant corporate windbreakers and varsity jackets tailored with embroidery.', 'Wear Your Brand with Pride', 'assets/images/corporate-jacket.png', 'View Apparel', 'category-page.html?cat=apparel', 'item-corporate-jackets', 2),
  ('Custom Logo Bags & Pouches', 'Versatile Carryalls', 'From padded laptop backpacks to foldable canvas totes, promote your business on the move with custom-printed bags.', 'Carry Your Brand Everywhere', 'assets/images/tote-bag.png', 'Browse Bags', 'category-page.html?cat=bags-pouches', 'item-tote-bags', 3)
ON DUPLICATE KEY UPDATE title = VALUES(title), body_text = VALUES(body_text);

-- ---- Coupons ----
INSERT INTO coupons (code, description, discount_type, discount_value, is_active) VALUES
  ('CK3A5OFF',   '5% off your first quotation inquiry', 'percent', 5.00, 1),
  ('WELCOME10',  '10% off for verified bulk corporate orders', 'percent', 10.00, 1),
  ('BULK500',    '₱500 off orders over ₱10,000', 'fixed', 500.00, 1)
ON DUPLICATE KEY UPDATE description = VALUES(description);

-- ---- Site Settings ----
INSERT INTO site_settings (setting_key, setting_value, setting_group) VALUES
  ('site_name',          'CreativeKit 3A',                                              'general'),
  ('site_tagline',       'Custom Corporate Giveaways & Promotional Merchandise Philippines', 'general'),
  ('contact_email',      'inquiry@creativekit3a.ph',                                    'contact'),
  ('contact_phone_1',    '+63 917 123 4567',                                            'contact'),
  ('contact_phone_2',    '+632 8888 1234',                                              'contact'),
  ('address_line1',      'Unit 3A, Creative Corporate Center',                          'contact'),
  ('address_city',       'Quezon City, Metro Manila',                                   'contact'),
  ('address_postal',     '1100',                                                        'contact'),
  ('address_country',    'Philippines',                                                 'contact'),
  ('currency_symbol',    '₱',                                                           'shop'),
  ('vat_rate_pct',       '12',                                                          'shop'),
  ('popup_delay_sec',    '9',                                                           'marketing'),
  ('popup_cookie_hours', '24',                                                          'marketing')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);


-- ============================================================
--  VIEWS
-- ============================================================

-- View: products with category and subcategory info
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
  c.name AS subcategory_name,
  c.slug AS subcategory_slug,
  parent.name AS main_category_name,
  parent.slug AS main_category_slug
FROM products p
LEFT JOIN categories c ON c.id = p.category_id
LEFT JOIN categories parent ON parent.id = c.parent_id
WHERE p.is_active = 1;

-- View: quotation request summary
CREATE OR REPLACE VIEW v_quote_summary AS
SELECT
  qr.id,
  qr.quote_ref,
  qr.full_name,
  qr.company_name,
  qr.email,
  qr.phone,
  qr.product_type,
  qr.quantity_range,
  qr.status,
  qr.created_at
FROM quote_requests qr;


-- ============================================================
--  STORED PROCEDURES
-- ============================================================

DELIMITER $$

-- ── PROCEDURE: submit_quote_request ─────────────────────────
CREATE PROCEDURE submit_quote_request (
  IN  p_full_name             VARCHAR(200),
  IN  p_company_name          VARCHAR(200),
  IN  p_email                 VARCHAR(255),
  IN  p_phone                 VARCHAR(50),
  IN  p_product_type          VARCHAR(100),
  IN  p_quantity_range        VARCHAR(50),
  IN  p_target_budget         VARCHAR(50),
  IN  p_target_deadline       DATE,
  IN  p_project_details       TEXT,
  IN  p_customization_options JSON,
  IN  p_source_url            VARCHAR(512),
  OUT p_quote_ref             VARCHAR(40),
  OUT p_request_id            INT UNSIGNED
)
BEGIN
  -- Generate reference format CK3A-XXXXXX
  SET p_quote_ref = CONCAT('CK3A-', FLOOR(100000 + (RAND() * 900000)));

  INSERT INTO quote_requests (
    quote_ref, full_name, company_name, email, phone,
    product_type, quantity_range, target_budget, target_deadline,
    project_details, customization_options, source_url, status
  ) VALUES (
    p_quote_ref, p_full_name, p_company_name, p_email, p_phone,
    p_product_type, p_quantity_range, p_target_budget, p_target_deadline,
    p_project_details, p_customization_options, p_source_url, 'new'
  );

  SET p_request_id = LAST_INSERT_ID();
END$$

-- ── PROCEDURE: register_user ─────────────────────────────────
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
CREATE PROCEDURE login_user (
  IN  p_email         VARCHAR(255),
  IN  p_ip_address    VARCHAR(45),
  IN  p_user_agent    VARCHAR(512),
  OUT p_user_id       INT UNSIGNED,
  OUT p_password_hash VARCHAR(255),
  OUT p_first_name    VARCHAR(100),
  OUT p_role          VARCHAR(20),
  OUT p_result        VARCHAR(50)
)
BEGIN
  DECLARE v_is_active TINYINT;

  SELECT id, password_hash, first_name, role, is_active
  INTO   p_user_id, p_password_hash, p_first_name, p_role, v_is_active
  FROM   users
  WHERE  email = p_email
  LIMIT  1;

  IF p_user_id IS NULL THEN
    SET p_result = 'not_found';
    INSERT INTO login_audit_log (email_tried, ip_address, user_agent, success, fail_reason)
    VALUES (p_email, p_ip_address, p_user_agent, 0, 'not_found');
  ELSEIF v_is_active = 0 THEN
    SET p_result = 'inactive';
    INSERT INTO login_audit_log (user_id, email_tried, ip_address, user_agent, success, fail_reason)
    VALUES (p_user_id, p_email, p_ip_address, p_user_agent, 0, 'inactive');
  ELSE
    SET p_result = 'found';
  END IF;
END$$

-- ── PROCEDURE: record_login_success ─────────────────────────
CREATE PROCEDURE record_login_success (
  IN p_user_id    INT UNSIGNED,
  IN p_email      VARCHAR(255),
  IN p_ip_address VARCHAR(45),
  IN p_user_agent VARCHAR(512)
)
BEGIN
  UPDATE users SET last_login_at = NOW() WHERE id = p_user_id;

  INSERT INTO login_audit_log (user_id, email_tried, ip_address, user_agent, success)
  VALUES (p_user_id, p_email, p_ip_address, p_user_agent, 1);
END$$

-- ── PROCEDURE: cleanup_expired_tokens ───────────────────────
CREATE PROCEDURE cleanup_expired_tokens ()
BEGIN
  DELETE FROM password_reset_tokens
  WHERE  expires_at < NOW() OR used = 1;

  DELETE FROM sessions
  WHERE  last_activity < DATE_SUB(NOW(), INTERVAL 7 DAY);
END$$

DELIMITER ;

-- ============================================================
--  END OF CREATIVEKIT 3A SCHEMA
-- ============================================================
