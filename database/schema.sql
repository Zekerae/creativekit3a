-- ============================================================
--  CREATIVEKIT 3A — Comprehensive Enterprise Database Schema
--  Engine : InnoDB | Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
--  Systems: Product Catalog, Quotation Engine, E-Commerce,
--           Purchasing System, Inventory Management,
--           and Management Information System (MIS)
-- ============================================================

CREATE DATABASE IF NOT EXISTS creativekit3a
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE creativekit3a;

-- ============================================================
--  1. CATALOG & PRODUCTS DOMAIN
-- ============================================================

-- Table: categories (supports hierarchy via parent_id)
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
  updated_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_parent  (parent_id),
  INDEX idx_slug    (slug),
  INDEX idx_active  (is_active),

  CONSTRAINT fk_cat_parent
    FOREIGN KEY (parent_id) REFERENCES categories (id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: products
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
  updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

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

-- Table: product_variants
CREATE TABLE IF NOT EXISTS product_variants (
  id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  product_id      INT UNSIGNED    NOT NULL,
  variant_type    VARCHAR(100)    DEFAULT NULL,
  name            VARCHAR(255)    NOT NULL,
  slug            VARCHAR(270)    NOT NULL UNIQUE,
  specs           VARCHAR(500)    DEFAULT NULL,
  thumbnail_url   VARCHAR(512)    DEFAULT NULL,
  base_price      DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  sale_price      DECIMAL(10,2)   DEFAULT NULL,
  badge_label     VARCHAR(80)     DEFAULT NULL,
  is_active       TINYINT(1)      NOT NULL DEFAULT 1,
  sort_order      SMALLINT        NOT NULL DEFAULT 0,
  created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

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

-- Table: product_variant_types (filter tabs)
CREATE TABLE IF NOT EXISTS product_variant_types (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  product_id  INT UNSIGNED  NOT NULL,
  type_key    VARCHAR(100)  NOT NULL,
  type_label  VARCHAR(150)  NOT NULL,
  sort_order  SMALLINT      NOT NULL DEFAULT 0,
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_pvt_product_key (product_id, type_key),
  INDEX idx_pvt_product (product_id),

  CONSTRAINT fk_pvt_product
    FOREIGN KEY (product_id) REFERENCES products (id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: product_images
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

-- Table: product_pricing_tiers
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

-- Table: product_tags
CREATE TABLE IF NOT EXISTS product_tags (
  id    INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  name  VARCHAR(100)  NOT NULL UNIQUE,
  slug  VARCHAR(110)  NOT NULL UNIQUE,

  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: product_tag_map
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
--  2. CUSTOMER LEADS & QUOTATION SYSTEM
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
  updated_at              DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE INDEX idx_qr_ref     (quote_ref),
  INDEX idx_qr_email   (email),
  INDEX idx_qr_status  (status),
  INDEX idx_qr_product (product_type),
  INDEX idx_qr_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
--  3. USERS, AUTHENTICATION & ACCESS CONTROL
-- ============================================================

CREATE TABLE IF NOT EXISTS users (
  id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  first_name      VARCHAR(100)  NOT NULL,
  last_name       VARCHAR(100)  NOT NULL,
  email           VARCHAR(255)  NOT NULL UNIQUE,
  phone           VARCHAR(30)   DEFAULT NULL,
  password_hash   VARCHAR(255)  NOT NULL,
  role            ENUM('customer','admin','staff','procurement_officer','inventory_manager') NOT NULL DEFAULT 'customer',
  is_verified     TINYINT(1)    NOT NULL DEFAULT 0,
  is_active       TINYINT(1)    NOT NULL DEFAULT 1,
  created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  last_login_at   DATETIME      DEFAULT NULL,

  PRIMARY KEY (id),
  UNIQUE INDEX idx_email  (email),
  INDEX idx_role          (role),
  INDEX idx_active        (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE IF NOT EXISTS sessions (
  session_id    VARCHAR(128)  NOT NULL,
  user_id       INT UNSIGNED  DEFAULT NULL,
  ip_address    VARCHAR(45)   DEFAULT NULL,
  user_agent    VARCHAR(512)  DEFAULT NULL,
  payload       LONGTEXT      DEFAULT NULL,
  last_activity DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (session_id),
  INDEX idx_user      (user_id),
  INDEX idx_activity  (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
--  4. PURCHASING SYSTEM (Procurement & Supplier Management)
-- ============================================================

-- Table: suppliers (vendors providing materials & blanks)
CREATE TABLE IF NOT EXISTS suppliers (
  id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  supplier_code   VARCHAR(40)   NOT NULL UNIQUE,
  company_name    VARCHAR(200)  NOT NULL,
  contact_person  VARCHAR(150)  DEFAULT NULL,
  email           VARCHAR(255)  NOT NULL,
  phone           VARCHAR(50)   NOT NULL,
  address         VARCHAR(500)  DEFAULT NULL,
  tax_id          VARCHAR(80)   DEFAULT NULL,
  payment_terms   VARCHAR(60)   NOT NULL DEFAULT 'Net 30',
  rating          DECIMAL(3,2)  DEFAULT 5.00,
  is_active       TINYINT(1)    NOT NULL DEFAULT 1,
  notes           TEXT          DEFAULT NULL,
  created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE INDEX idx_sup_code  (supplier_code),
  INDEX idx_sup_company (company_name),
  INDEX idx_sup_active  (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: purchase_orders
CREATE TABLE IF NOT EXISTS purchase_orders (
  id                  INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  po_number           VARCHAR(50)     NOT NULL UNIQUE,
  supplier_id         INT UNSIGNED    NOT NULL,
  created_by_user_id  INT UNSIGNED    DEFAULT NULL,
  order_date          DATE            NOT NULL,
  expected_date       DATE            DEFAULT NULL,
  received_date       DATE            DEFAULT NULL,
  status              ENUM('draft','sent','partially_received','received','cancelled') NOT NULL DEFAULT 'draft',
  subtotal            DECIMAL(12,2)   NOT NULL DEFAULT 0.00,
  shipping_cost       DECIMAL(12,2)   NOT NULL DEFAULT 0.00,
  tax_amount          DECIMAL(12,2)   NOT NULL DEFAULT 0.00,
  total_amount        DECIMAL(12,2)   NOT NULL DEFAULT 0.00,
  payment_status      ENUM('unpaid','partially_paid','paid') NOT NULL DEFAULT 'unpaid',
  notes               TEXT            DEFAULT NULL,
  created_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE INDEX idx_po_number   (po_number),
  INDEX idx_po_supplier (supplier_id),
  INDEX idx_po_status   (status),
  INDEX idx_po_date     (order_date),

  CONSTRAINT fk_po_supplier
    FOREIGN KEY (supplier_id) REFERENCES suppliers (id)
    ON DELETE RESTRICT ON UPDATE CASCADE,

  CONSTRAINT fk_po_creator
    FOREIGN KEY (created_by_user_id) REFERENCES users (id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: purchase_order_items (line items)
CREATE TABLE IF NOT EXISTS purchase_order_items (
  id                  INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  purchase_order_id   INT UNSIGNED    NOT NULL,
  product_id          INT UNSIGNED    DEFAULT NULL,
  variant_id          INT UNSIGNED    DEFAULT NULL,
  item_name           VARCHAR(255)    NOT NULL,
  sku                 VARCHAR(100)    DEFAULT NULL,
  quantity_ordered    INT UNSIGNED    NOT NULL DEFAULT 1,
  quantity_received   INT UNSIGNED    NOT NULL DEFAULT 0,
  unit_cost           DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  line_total          DECIMAL(12,2)   NOT NULL DEFAULT 0.00,
  status              ENUM('pending','received','cancelled') NOT NULL DEFAULT 'pending',

  PRIMARY KEY (id),
  INDEX idx_poi_po       (purchase_order_id),
  INDEX idx_poi_product  (product_id),
  INDEX idx_poi_variant  (variant_id),

  CONSTRAINT fk_poi_order
    FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders (id)
    ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT fk_poi_product
    FOREIGN KEY (product_id) REFERENCES products (id)
    ON DELETE SET NULL ON UPDATE CASCADE,

  CONSTRAINT fk_poi_variant
    FOREIGN KEY (variant_id) REFERENCES product_variants (id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
--  5. INVENTORY SYSTEM (Warehouses, Stock & Movements)
-- ============================================================

-- Table: warehouses (stock storage locations)
CREATE TABLE IF NOT EXISTS warehouses (
  id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  warehouse_code  VARCHAR(40)   NOT NULL UNIQUE,
  name            VARCHAR(150)  NOT NULL,
  address         VARCHAR(500)  DEFAULT NULL,
  contact_person  VARCHAR(150)  DEFAULT NULL,
  phone           VARCHAR(50)   DEFAULT NULL,
  is_active       TINYINT(1)    NOT NULL DEFAULT 1,
  created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE INDEX idx_wh_code   (warehouse_code),
  INDEX idx_wh_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: inventory_items (multi-warehouse stock levels per product/variant)
CREATE TABLE IF NOT EXISTS inventory_items (
  id                  INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  warehouse_id        INT UNSIGNED  NOT NULL,
  product_id          INT UNSIGNED  NOT NULL,
  variant_id          INT UNSIGNED  DEFAULT NULL,
  quantity_on_hand    INT           NOT NULL DEFAULT 0,
  quantity_reserved   INT           NOT NULL DEFAULT 0,
  reorder_threshold   INT           NOT NULL DEFAULT 20,
  reorder_quantity    INT           NOT NULL DEFAULT 100,
  last_counted_at     DATETIME      DEFAULT NULL,
  updated_at          DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_wh_product_variant (warehouse_id, product_id, variant_id),
  INDEX idx_inv_wh       (warehouse_id),
  INDEX idx_inv_product  (product_id),
  INDEX idx_inv_variant  (variant_id),
  INDEX idx_inv_qty      (quantity_on_hand),

  CONSTRAINT fk_inv_wh
    FOREIGN KEY (warehouse_id) REFERENCES warehouses (id)
    ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT fk_inv_product
    FOREIGN KEY (product_id) REFERENCES products (id)
    ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT fk_inv_variant
    FOREIGN KEY (variant_id) REFERENCES product_variants (id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: inventory_movements (audit log of every stock transaction)
CREATE TABLE IF NOT EXISTS inventory_movements (
  id                  INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  movement_ref        VARCHAR(60)   NOT NULL UNIQUE,
  reference_type      ENUM('purchase_order','sales_order','manual_adjustment','return','production') NOT NULL,
  reference_id        INT UNSIGNED  DEFAULT NULL,
  warehouse_id        INT UNSIGNED  NOT NULL,
  product_id          INT UNSIGNED  NOT NULL,
  variant_id          INT UNSIGNED  DEFAULT NULL,
  movement_type       ENUM('stock_in','stock_out','transfer','adjustment') NOT NULL,
  quantity            INT           NOT NULL,
  unit_cost           DECIMAL(10,2) DEFAULT NULL,
  performed_by_user_id INT UNSIGNED DEFAULT NULL,
  remarks             VARCHAR(500)  DEFAULT NULL,
  created_at          DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_mov_ref     (movement_ref),
  INDEX idx_mov_type    (movement_type),
  INDEX idx_mov_wh      (warehouse_id),
  INDEX idx_mov_product (product_id),
  INDEX idx_mov_created (created_at),

  CONSTRAINT fk_mov_wh
    FOREIGN KEY (warehouse_id) REFERENCES warehouses (id)
    ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT fk_mov_product
    FOREIGN KEY (product_id) REFERENCES products (id)
    ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT fk_mov_variant
    FOREIGN KEY (variant_id) REFERENCES product_variants (id)
    ON DELETE SET NULL ON UPDATE CASCADE,

  CONSTRAINT fk_mov_user
    FOREIGN KEY (performed_by_user_id) REFERENCES users (id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
--  6. INFORMATION SYSTEM & AUDIT LOGS (Executive MIS)
-- ============================================================

-- Table: system_metrics_daily (pre-computed analytics snapshots)
CREATE TABLE IF NOT EXISTS system_metrics_daily (
  id                      INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  metric_date             DATE          NOT NULL UNIQUE,
  total_quote_requests    INT           NOT NULL DEFAULT 0,
  total_orders_placed     INT           NOT NULL DEFAULT 0,
  gross_revenue           DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  procurement_spend       DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  active_inventory_value  DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  low_stock_item_count    INT           NOT NULL DEFAULT 0,
  created_at              DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE INDEX idx_metric_date (metric_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: audit_logs (Enterprise activity log for all operations)
CREATE TABLE IF NOT EXISTS audit_logs (
  id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  user_id         INT UNSIGNED  DEFAULT NULL,
  module          ENUM('purchasing','inventory','quotes','orders','catalog','auth','system') NOT NULL,
  action          ENUM('create','update','delete','receive','adjust','login','export') NOT NULL,
  entity_id       VARCHAR(100)  DEFAULT NULL,
  description     VARCHAR(500)  NOT NULL,
  ip_address      VARCHAR(45)   DEFAULT NULL,
  user_agent      VARCHAR(512)  DEFAULT NULL,
  created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_audit_module  (module),
  INDEX idx_audit_action  (action),
  INDEX idx_audit_user    (user_id),
  INDEX idx_audit_created (created_at),

  CONSTRAINT fk_audit_user
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: login_audit_log
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
  INDEX idx_l_user    (user_id),
  INDEX idx_l_email   (email_tried),
  INDEX idx_l_success (success),
  INDEX idx_l_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
--  7. ORDERS & CHECKOUT DOMAIN
-- ============================================================

CREATE TABLE IF NOT EXISTS orders (
  id                  INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  user_id             INT UNSIGNED    DEFAULT NULL,
  order_number        VARCHAR(40)     NOT NULL UNIQUE,
  status              ENUM('pending','confirmed','in_production','shipped','delivered','cancelled','refunded') NOT NULL DEFAULT 'pending',
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
  updated_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE INDEX idx_order_number (order_number),
  INDEX idx_user          (user_id),
  INDEX idx_status        (status),
  INDEX idx_created       (created_at),

  CONSTRAINT fk_order_user
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
--  8. CMS & SITE CONFIGURATION
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
  updated_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE IF NOT EXISTS site_settings (
  setting_key   VARCHAR(100) NOT NULL,
  setting_value LONGTEXT     DEFAULT NULL,
  setting_group VARCHAR(60)  NOT NULL DEFAULT 'general',
  updated_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (setting_key),
  INDEX idx_group (setting_group)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
--  9. SEED DATA
-- ============================================================

-- ---- Default Users & Administrators ----
INSERT INTO users (id, first_name, last_name, email, phone, password_hash, role, is_active) VALUES
  (1, 'Admin', 'Director', 'admin@creativekit3a.com', '+63 917 714 2774', '$2y$10$w6z/pA8vXq3JgA3yPqHZeOq9O.7eB9uM6pW1k5jX3ZzOQ3j6Uq8Wa', 'admin', 1),
  (2, 'Maria', 'Santos',   'maria.customer@gmail.com', '+63 918 123 4567', '$2y$10$w6z/pA8vXq3JgA3yPqHZeOq9O.7eB9uM6pW1k5jX3ZzOQ3j6Uq8Wa', 'customer', 1)
ON DUPLICATE KEY UPDATE role = VALUES(role), is_active = VALUES(is_active);

-- ---- Core Categories ----
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
  (101, 1, 'Car Accessories',       'car',       1),
  (102, 1, 'Computer Accessories',  'computer',  2),
  (103, 1, 'Desk Accessories',      'desk',      3),
  (104, 1, 'Mobile Accessories',    'mobile',    4),
  (105, 1, 'Gadgets',               'gadgets',   5),
  (201, 2, 'Caps',                  'caps',      1),
  (202, 2, 'Jackets',               'jackets',   2),
  (203, 2, 'Shirts',                'shirts',    3),
  (301, 3, 'Backpacks',             'backpacks', 1),
  (302, 3, 'Eco Bags',              'ecobags',   2),
  (303, 3, 'Laptop Bags',           'laptop',    3),
  (304, 3, 'Tote Bags',             'tote',      4),
  (305, 3, 'Travel Bags',           'travel',    5),
  (306, 3, 'Lunch Bags',            'lunch',     6),
  (307, 3, 'Organizer Pouches',     'organizer', 7),
  (401, 4, 'Tumblers & Flasks',     'tumbler',   1),
  (402, 4, 'Mugs & Cups',           'mugs',      2),
  (403, 4, 'Water Bottles',         'bottles',   3),
  (404, 4, 'Eco-Friendly',          'eco',       4),
  (501, 5, 'Corporate',             'corporate', 1),
  (502, 5, 'Lifestyle',             'lifestyle', 2),
  (503, 5, 'Health & Wellness',     'wellness',  3),
  (601, 6, 'Kitchen & Dining',      'kitchen',   1),
  (602, 6, 'Personal Care',         'personal',  2),
  (603, 6, 'Outdoor & Lifestyle',   'outdoor',   3),
  (701, 7, 'Pens',                  'pen',       1),
  (702, 7, 'Paper & Notebooks',     'paper',     2),
  (801, 8, 'Wallets & Cases',       'wallets',   1),
  (802, 8, 'Organizers & Bags',     'organizers',2),
  (803, 8, 'Keychains & Tags',      'keychains', 3)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- ---- Core Products ----
INSERT INTO products
  (id, category_id, name, slug, sku, short_desc, thumbnail_url, base_price, is_customizable, is_featured, is_active, sort_order)
VALUES
  (1,  101, 'Car Charger Adapters',          'car-charger-adapter',         'ACC-CAR-001', 'Custom-branded single, dual, and fast car charger adapters', 'assets/images/car-charger-adapters.png', 85.00,  1, 1, 1, 1),
  (2,  101, 'Car Organizers & Holders',      'car-organizers',              'ACC-CAR-002', 'Seat-back and console organizers for brand visibility on the go', 'assets/images/car-organizer.png', 120.00, 1, 1, 1, 2),
  (3,  101, 'Dash Cams',                     'car-dashcams',                'ACC-CAR-003', 'HD dash cameras with customizable logo placement', 'assets/images/dash-cam.png', 450.00, 1, 1, 1, 3),
  (4,  102, 'Audio Accessories',             'computer-audio',              'ACC-AUD-001', 'Noise-canceling earphones and portable speaker gifts', 'assets/images/audio-accessories.png', 95.00,  1, 0, 1, 4),
  (5,  102, 'Mouse & Mouse Pads',            'computer-mouse',              'ACC-MSE-001', 'Non-slip speed mouse pads and ergonomic wireless mice', 'assets/images/mouse-pad.png', 75.00,  1, 0, 1, 5),
  (6,  104, 'Mobile Power Banks',            'mobile-powerbanks',           'ACC-PBK-001', 'High-capacity power banks with LED battery display', 'assets/images/mobile-powerbank.png', 350.00, 1, 1, 1, 6),
  (7,  202, 'Corporate Jackets',             'jackets-corporate',           'APP-JKT-001', 'Tailored corporate windbreakers and executive weather-resistant jackets', 'assets/images/corporate-jacket.png', 720.00, 1, 1, 1, 7),
  (8,  202, 'Bomber Jackets',                'jacket-bomber',               'APP-JKT-002', 'Modern streetwear and company bomber jackets with custom embroidery', 'assets/images/bomber-jacket.png', 650.00, 1, 0, 1, 8),
  (9,  201, 'Baseball Cap',                  'cap-baseball',                'APP-CAP-001', 'Structured 6-panel twill baseball caps with adjustable strap', 'assets/images/baseball-cap.png', 180.00, 1, 0, 1, 9),
  (10, 203, 'Collared Shirt',                'shirt-collared',              'APP-SHT-001', 'Breathable pique cotton polo shirts for company uniforms', 'assets/images/collared-shirt.png', 280.00, 1, 0, 1, 10),
  (11, 303, 'Laptop Backpacks',              'laptop-backpack',             'BAG-LPT-001', 'Padded compartments for 15.6 inch laptops with USB charging pass-through', 'assets/images/laptop-backpack.png', 520.00, 1, 1, 1, 11),
  (12, 304, 'Classic Zip Totes',             'tote-bag-classic-zip',        'BAG-TOT-001', 'Heavy-duty eco canvas tote bags with reinforced zipper closure', 'assets/images/tote-bag.png', 120.00, 1, 1, 1, 12),
  (13, 302, 'Foldable Shopping Totes',       'ecobags-shopping-tote',       'BAG-ECO-001', 'Compact reusable fold-away bags for sustainable corporate gifting', 'assets/images/eco-bag.png', 95.00,  1, 0, 1, 13),
  (14, 401, 'Bamboo Tumblers',               'tumbler-bamboo',              'DRK-TMB-001', 'Eco-friendly natural bamboo exterior with double-wall stainless steel interior', 'assets/images/bamboo-tumbler.png', 220.00, 1, 1, 1, 14),
  (15, 401, 'Stainless Steel Tumblers',      'tumbler-stainless',           'DRK-TMB-002', 'Vacuum insulated travel tumblers keeping drinks cold 24hrs / hot 12hrs', 'assets/images/stainless-tumbler.png', 310.00, 1, 1, 1, 15),
  (16, 402, 'Ceramic Mugs',                  'mugs-ceramic',                'DRK-MUG-001', 'Glossy ceramic mugs with full-color wrap dye-sublimation imprint', 'assets/images/ceramic-mug.png', 95.00,  1, 0, 1, 16),
  (17, 501, 'Employee Onboarding Sets',      'gift-set-employee-onboarding','GFT-SET-001', 'Welcome box complete with notebook, tumbler, pen, and USB flash drive', 'assets/images/onboarding-set.png', 890.00, 1, 1, 1, 17),
  (18, 501, 'Desk Essentials Gift Sets',      'gift-set-desk-essentials',    'GFT-SET-002', 'Executive wooden desk set with wireless charger and pen stand', 'assets/images/desk-gift-set.png', 650.00, 1, 0, 1, 18),
  (19, 701, 'Bamboo Pens',                   'pen-bamboo',                  'PEN-BAM-001', 'Laser-engraved natural bamboo ballpoint pens with black German ink', 'assets/images/bamboo-pen.png', 35.00,  1, 1, 1, 19),
  (20, 702, 'Custom Notebooks & Journals',   'paper-notebooks-journals',    'PPR-NBK-001', 'Hardbound PU and craft paper journals with ribbon bookmark and elastic band', 'assets/images/custom-notebook.png', 95.00, 1, 1, 1, 20),
  (21, 801, 'Wallets',                       'pu-leather-wallets',          'LEA-WLT-001', 'Slim bifold PU leather wallets with debossed corporate logo', 'assets/images/leather-wallet.png', 195.00, 1, 0, 1, 21),
  (22, 803, 'Keychains',                     'pu-leather-keychains',        'LEA-KEY-001', 'Stitched leather key fobs with polished metallic hardware', 'assets/images/leather-keychain.png', 65.00,  1, 0, 1, 22)
ON DUPLICATE KEY UPDATE name = VALUES(name), base_price = VALUES(base_price);

-- ---- Suppliers Seed (Purchasing System) ----
INSERT INTO suppliers (id, supplier_code, company_name, contact_person, email, phone, address, payment_terms, rating) VALUES
  (1, 'SUP-PH-001', 'Luzon Garments & Apparel Mfg', 'Maricel Bautista', 'orders@luzongarments.ph', '+63 2 8712 3456', 'Taytay, Rizal, Philippines', 'Net 30', 4.90),
  (2, 'SUP-PH-002', 'Pacific Tech & Gadgets Import Corp', 'Kenji Tanaka', 'supply@pacifictech.com.ph', '+63 2 8923 8812', 'Binondo, Manila, Philippines', 'Net 15', 4.85),
  (3, 'SUP-PH-003', 'EcoDrinkware Phils Corp', 'Eduardo Ramos', 'sales@ecodrinkware.ph', '+63 917 555 1290', 'Sta. Rosa, Laguna, Philippines', 'Net 30', 4.95),
  (4, 'SUP-PH-004', 'Premium Leather & Packaging Solutions', 'Sofia Tan', 'inquiry@premiumleather.ph', '+63 2 8540 2211', 'Marikina City, Metro Manila', 'Net 30', 4.75)
ON DUPLICATE KEY UPDATE company_name = VALUES(company_name);

-- ---- Warehouses Seed (Inventory System) ----
INSERT INTO warehouses (id, warehouse_code, name, address, contact_person, phone) VALUES
  (1, 'WH-QC',  'Main Central Warehouse - Quezon City', '12 Innovation Way, Diliman, Quezon City', 'Ramon Valdez (Warehouse Head)', '+63 917 111 8899'),
  (2, 'WH-MM',  'Metro Manila Distribution Hub - Taguig', 'Bldg 4, FTI Complex, Taguig City', 'Analyn Santos (Logistics Supervisor)', '+63 920 222 7766'),
  (3, 'WH-CEB', 'Visayas Regional Depot - Cebu City', 'Subangdaku, Mandaue City, Cebu', 'Gary Alcantara (Depot Lead)', '+63 32 414 5500')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- ---- Inventory Items Stock Seed ----
INSERT INTO inventory_items (warehouse_id, product_id, variant_id, quantity_on_hand, quantity_reserved, reorder_threshold, reorder_quantity) VALUES
  -- Car Charger Adapters (Product 1)
  (1, 1, NULL, 480, 50, 60, 200),
  (2, 1, NULL, 180, 20, 40, 100),
  -- Car Organizers (Product 2)
  (1, 2, NULL, 310, 30, 50, 150),
  -- Dash Cams (Product 3) - LOW STOCK ALERT
  (1, 3, NULL, 18, 5, 25, 50),
  -- Corporate Jackets (Product 7) - LOW STOCK ALERT
  (1, 7, NULL, 14, 10, 30, 80),
  (2, 7, NULL, 22, 5, 20, 50),
  -- Bomber Jackets (Product 8)
  (1, 8, NULL, 95, 15, 25, 60),
  -- Laptop Backpacks (Product 11)
  (1, 11, NULL, 140, 25, 30, 100),
  -- Canvas Totes (Product 12)
  (1, 12, NULL, 1250, 200, 150, 500),
  (2, 12, NULL, 450, 50, 100, 300),
  -- Bamboo Tumblers (Product 14)
  (1, 14, NULL, 850, 120, 100, 300),
  (2, 14, NULL, 280, 40, 60, 150),
  -- Stainless Steel Tumblers (Product 15)
  (1, 15, NULL, 620, 90, 80, 250),
  -- Ceramic Mugs (Product 16)
  (1, 16, NULL, 940, 100, 150, 400),
  -- Bamboo Pens (Product 19)
  (1, 19, NULL, 3400, 500, 300, 1000),
  -- Custom Notebooks (Product 20)
  (1, 20, NULL, 780, 150, 100, 400),
  -- Leather Wallets (Product 21) - LOW STOCK ALERT
  (1, 21, NULL, 19, 4, 30, 100)
ON DUPLICATE KEY UPDATE quantity_on_hand = VALUES(quantity_on_hand);

-- ---- Initial Purchase Orders Seed (Purchasing System) ----
INSERT INTO purchase_orders (id, po_number, supplier_id, order_date, expected_date, received_date, status, subtotal, shipping_cost, tax_amount, total_amount, payment_status, notes) VALUES
  (1, 'PO-2026-001', 3, '2026-10-15', '2026-10-25', '2026-10-24', 'received', 88000.00, 2500.00, 10560.00, 101060.00, 'paid', 'Restock of 450ml raw bamboo tumbler shells with silicone gaskets.'),
  (2, 'PO-2026-002', 1, '2026-11-05', '2026-11-18', '2026-11-17', 'received', 72000.00, 1800.00, 8640.00, 82440.00, 'paid', 'Wholesale procurement of blank corporate microfiber windbreakers.'),
  (3, 'PO-2026-003', 2, '2026-11-18', '2026-11-28', NULL, 'sent', 45000.00, 1500.00, 5400.00, 51900.00, 'unpaid', 'Emergency replenishment for low stock 1080p wide-angle dash cams.'),
  (4, 'PO-2026-004', 4, '2026-11-20', '2026-12-02', NULL, 'draft', 32000.00, 1200.00, 3840.00, 37040.00, 'unpaid', 'Quarterly batch order for RFID bifold leather blanks.')
ON DUPLICATE KEY UPDATE total_amount = VALUES(total_amount);

-- ---- Purchase Order Items Seed ----
INSERT INTO purchase_order_items (purchase_order_id, product_id, item_name, sku, quantity_ordered, quantity_received, unit_cost, line_total, status) VALUES
  (1, 14, 'Natural Bamboo Tumbler Blank 450ml', 'DRK-TMB-001', 500, 500, 176.00, 88000.00, 'received'),
  (2, 7,  'Executive Corporate Windbreaker Blank', 'APP-JKT-001', 150, 150, 480.00, 72000.00, 'received'),
  (3, 3,  '1080p HD Wide-Angle Dash Cam Unit', 'ACC-CAR-003', 150, 0,   300.00, 45000.00, 'pending'),
  (4, 21, 'RFID Bifold PU Leather Wallet Blank', 'LEA-WLT-001', 250, 0,   128.00, 32000.00, 'pending')
ON DUPLICATE KEY UPDATE line_total = VALUES(line_total);

-- ---- System Metrics Daily Seed (Information System) ----
INSERT INTO system_metrics_daily (metric_date, total_quote_requests, total_orders_placed, gross_revenue, procurement_spend, active_inventory_value, low_stock_item_count) VALUES
  ('2026-11-18', 14, 6, 84200.00, 51900.00, 940000.00, 4),
  ('2026-11-19', 19, 8, 112500.00, 0.00, 935000.00, 3),
  ('2026-11-20', 23, 11, 148900.00, 37040.00, 968000.00, 3)
ON DUPLICATE KEY UPDATE gross_revenue = VALUES(gross_revenue);

-- ---- Audit Logs Seed (Information System) ----
INSERT INTO audit_logs (module, action, entity_id, description, ip_address) VALUES
  ('inventory', 'adjust', 'INV-101', 'Initial baseline physical inventory count completed for Main QC Warehouse.', '127.0.0.1'),
  ('purchasing', 'receive', 'PO-2026-001', 'Received 500 units of Bamboo Tumbler blanks from EcoDrinkware Phils Corp.', '127.0.0.1'),
  ('purchasing', 'receive', 'PO-2026-002', 'Received 150 units of Corporate Windbreakers from Luzon Garments.', '127.0.0.1'),
  ('purchasing', 'create', 'PO-2026-003', 'Dispatched Purchase Order PO-2026-003 to Pacific Tech & Gadgets Import Corp.', '127.0.0.1'),
  ('quotes', 'update', 'CK3A-849201', 'Quotation marked as Approved & In Production.', '127.0.0.1')
ON DUPLICATE KEY UPDATE description = VALUES(description);

-- ---- Site Settings ----
INSERT INTO site_settings (setting_key, setting_value, setting_group) VALUES
  ('site_name',          'CreativeKit 3A',                                              'general'),
  ('site_tagline',       'Custom Corporate Giveaways & Promotional Merchandise Philippines', 'general'),
  ('contact_email',      'inquiry@creativekit3a.ph',                                    'contact'),
  ('contact_phone_1',    '+63 917 123 4567',                                            'contact'),
  ('currency_symbol',    '₱',                                                           'shop'),
  ('vat_rate_pct',       '12',                                                          'shop')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);


-- ============================================================
--  10. ANALYTICAL VIEWS FOR INFORMATION SYSTEM
-- ============================================================

-- View 1: Real-time Inventory Summary with stock status & valuation
CREATE OR REPLACE VIEW v_inventory_summary AS
SELECT
  ii.id AS inventory_id,
  w.warehouse_code,
  w.name AS warehouse_name,
  p.id AS product_id,
  p.name AS product_name,
  p.sku,
  p.base_price,
  c.name AS category_name,
  ii.quantity_on_hand,
  ii.quantity_reserved,
  (ii.quantity_on_hand - ii.quantity_reserved) AS available_quantity,
  ii.reorder_threshold,
  ii.reorder_quantity,
  ROUND(ii.quantity_on_hand * p.base_price, 2) AS total_valuation,
  CASE
    WHEN ii.quantity_on_hand <= 0 THEN 'Out of Stock'
    WHEN ii.quantity_on_hand <= ii.reorder_threshold THEN 'Low Stock'
    ELSE 'In Stock'
  END AS stock_status,
  ii.updated_at
FROM inventory_items ii
JOIN warehouses w ON w.id = ii.warehouse_id
JOIN products p   ON p.id = ii.product_id
LEFT JOIN categories c ON c.id = p.category_id;

-- View 2: Purchasing & Procurement Summary
CREATE OR REPLACE VIEW v_purchasing_summary AS
SELECT
  po.id AS po_id,
  po.po_number,
  s.company_name AS supplier_name,
  s.supplier_code,
  po.order_date,
  po.expected_date,
  po.received_date,
  po.status,
  po.payment_status,
  po.total_amount,
  COUNT(poi.id) AS total_line_items,
  COALESCE(SUM(poi.quantity_ordered), 0) AS total_units_ordered,
  COALESCE(SUM(poi.quantity_received), 0) AS total_units_received
FROM purchase_orders po
JOIN suppliers s ON s.id = po.supplier_id
LEFT JOIN purchase_order_items poi ON poi.purchase_order_id = po.id
GROUP BY po.id;

-- View 3: Executive MIS KPIs Snapshot
CREATE OR REPLACE VIEW v_mis_executive_kpi AS
SELECT
  (SELECT COUNT(*) FROM products WHERE is_active = 1) AS total_active_products,
  (SELECT COALESCE(SUM(quantity_on_hand), 0) FROM inventory_items) AS total_inventory_units,
  (SELECT COALESCE(SUM(quantity_on_hand * p.base_price), 0) FROM inventory_items ii JOIN products p ON p.id = ii.product_id) AS total_inventory_valuation,
  (SELECT COUNT(*) FROM inventory_items WHERE quantity_on_hand <= reorder_threshold) AS low_stock_alerts_count,
  (SELECT COUNT(*) FROM purchase_orders WHERE status IN ('draft', 'sent', 'partially_received')) AS active_purchase_orders,
  (SELECT COALESCE(SUM(total_amount), 0) FROM purchase_orders WHERE status = 'received') AS total_procurement_expenditure,
  (SELECT COUNT(*) FROM quote_requests WHERE status = 'new') AS pending_quote_requests,
  (SELECT COUNT(*) FROM quote_requests) AS total_quote_requests,
  (SELECT COUNT(*) FROM users WHERE role = 'customer') AS total_customers;


-- ============================================================
--  11. STORED PROCEDURES & BUSINESS LOGIC
-- ============================================================

DELIMITER $$

-- ── PROCEDURE: record_stock_movement ────────────────────────
-- Atomically writes stock ledger entry and updates warehouse inventory
CREATE PROCEDURE record_stock_movement (
  IN p_ref_type      VARCHAR(40),
  IN p_ref_id        INT UNSIGNED,
  IN p_warehouse_id  INT UNSIGNED,
  IN p_product_id    INT UNSIGNED,
  IN p_variant_id    INT UNSIGNED,
  IN p_movement_type VARCHAR(20),
  IN p_quantity      INT,
  IN p_unit_cost     DECIMAL(10,2),
  IN p_user_id       INT UNSIGNED,
  IN p_remarks       VARCHAR(500),
  OUT p_movement_ref VARCHAR(60)
)
BEGIN
  SET p_movement_ref = CONCAT('MOV-', DATE_FORMAT(NOW(), '%y%m%d'), '-', FLOOR(1000 + (RAND() * 9000)));

  -- 1. Insert Movement Record
  INSERT INTO inventory_movements (
    movement_ref, reference_type, reference_id, warehouse_id, product_id,
    variant_id, movement_type, quantity, unit_cost, performed_by_user_id, remarks
  ) VALUES (
    p_movement_ref, p_ref_type, p_ref_id, p_warehouse_id, p_product_id,
    p_variant_id, p_movement_type, p_quantity, p_unit_cost, p_user_id, p_remarks
  );

  -- 2. Update Inventory Quantity on Hand
  IF p_movement_type = 'stock_in' THEN
    UPDATE inventory_items
    SET quantity_on_hand = quantity_on_hand + p_quantity
    WHERE warehouse_id = p_warehouse_id AND product_id = p_product_id
      AND (variant_id = p_variant_id OR (variant_id IS NULL AND p_variant_id IS NULL));
  ELSEIF p_movement_type = 'stock_out' THEN
    UPDATE inventory_items
    SET quantity_on_hand = GREATEST(0, quantity_on_hand - p_quantity)
    WHERE warehouse_id = p_warehouse_id AND product_id = p_product_id
      AND (variant_id = p_variant_id OR (variant_id IS NULL AND p_variant_id IS NULL));
  ELSEIF p_movement_type = 'adjustment' THEN
    UPDATE inventory_items
    SET quantity_on_hand = p_quantity
    WHERE warehouse_id = p_warehouse_id AND product_id = p_product_id
      AND (variant_id = p_variant_id OR (variant_id IS NULL AND p_variant_id IS NULL));
  END IF;

  -- 3. Update main products table total stock sum
  UPDATE products p
  SET stock_qty = (SELECT COALESCE(SUM(quantity_on_hand), 0) FROM inventory_items WHERE product_id = p.id)
  WHERE p.id = p_product_id;
END$$

-- ── PROCEDURE: receive_purchase_order ───────────────────────
-- Marks PO received and automatically posts stock_in to inventory
CREATE PROCEDURE receive_purchase_order (
  IN p_po_id         INT UNSIGNED,
  IN p_warehouse_id  INT UNSIGNED,
  IN p_user_id       INT UNSIGNED,
  OUT p_result       VARCHAR(50)
)
BEGIN
  DECLARE done INT DEFAULT FALSE;
  DECLARE v_prod_id INT UNSIGNED;
  DECLARE v_var_id INT UNSIGNED;
  DECLARE v_qty INT UNSIGNED;
  DECLARE v_cost DECIMAL(10,2);
  DECLARE v_dummy_ref VARCHAR(60);

  DECLARE cur CURSOR FOR
    SELECT product_id, variant_id, quantity_ordered, unit_cost
    FROM purchase_order_items
    WHERE purchase_order_id = p_po_id AND status != 'received';

  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

  OPEN cur;
  read_loop: LOOP
    FETCH cur INTO v_prod_id, v_var_id, v_qty, v_cost;
    IF done THEN
      LEAVE read_loop;
    END IF;

    -- Record stock movement for this line item
    CALL record_stock_movement(
      'purchase_order', p_po_id, p_warehouse_id, v_prod_id, v_var_id,
      'stock_in', v_qty, v_cost, p_user_id, 'Automatic goods receipt from PO', v_dummy_ref
    );
  END LOOP;
  CLOSE cur;

  -- Update PO item statuses
  UPDATE purchase_order_items
  SET status = 'received', quantity_received = quantity_ordered
  WHERE purchase_order_id = p_po_id;

  -- Update PO status
  UPDATE purchase_orders
  SET status = 'received', received_date = CURDATE()
  WHERE id = p_po_id;

  -- Log Audit Trail
  INSERT INTO audit_logs (user_id, module, action, entity_id, description)
  VALUES (p_user_id, 'purchasing', 'receive', CONCAT('PO-', p_po_id), 'Received all items for purchase order.');

  SET p_result = 'ok';
END$$

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

DELIMITER ;

-- ============================================================
--  END OF CREATIVEKIT 3A ENTERPRISE SCHEMA
-- ============================================================
