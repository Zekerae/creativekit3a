-- ============================================================
--  ADDED: items-page SQL additions
--  File:  items-page.sql
--
--  These additions extend schema.sql to support the new
--  items-page.html — a page that shows individual product
--  variants (items) within a sub-category.
--
--  Example:
--    category: Accessories & Gadgets
--    subcategory: Car Accessories
--    product (sub-cat card): Car Charger Adapters
--    items (this page): Single USB-A, Dual Port, Fast Charge…
--
--  Run this AFTER schema.sql (it depends on the products table).
--  No existing tables were modified. Only new tables are added.
-- ============================================================

USE pinoyballers_db;

-- ============================================================
--  TABLE: product_variants
--  Stores individual item variants that belong to a product.
--  A "product" on category-page is like "Car Charger Adapters";
--  a "variant" on items-page is like "18W Quick Charge 3.0".
--
--  Relationships:
--    products (1) ──< product_variants (many)
-- ============================================================
CREATE TABLE IF NOT EXISTS product_variants (
  id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  product_id      INT UNSIGNED    NOT NULL              COMMENT 'Parent product (sub-category card)',
  variant_type    VARCHAR(100)    DEFAULT NULL          COMMENT 'Filter tab key, e.g. single | dual | fast',
  name            VARCHAR(255)    NOT NULL              COMMENT 'Variant display name',
  slug            VARCHAR(270)    NOT NULL UNIQUE       COMMENT 'URL-safe slug for deep linking',
  specs           VARCHAR(500)    DEFAULT NULL          COMMENT 'Short spec line shown on item card',
  thumbnail_url   VARCHAR(512)    DEFAULT NULL,
  base_price      DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  sale_price      DECIMAL(10,2)   DEFAULT NULL,
  badge_label     VARCHAR(80)     DEFAULT NULL          COMMENT 'e.g. Best Seller | New | Premium | Eco',
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
--  TABLE: product_variant_images
--  Multiple images per variant (gallery on items-page)
-- ============================================================
CREATE TABLE IF NOT EXISTS product_variant_images (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  variant_id  INT UNSIGNED  NOT NULL,
  image_url   VARCHAR(512)  NOT NULL,
  alt_text    VARCHAR(255)  DEFAULT NULL,
  sort_order  SMALLINT      NOT NULL DEFAULT 0,
  is_primary  TINYINT(1)    NOT NULL DEFAULT 0,
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_pvi_variant (variant_id),

  CONSTRAINT fk_pvi_variant
    FOREIGN KEY (variant_id) REFERENCES product_variants (id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
--  TABLE: product_variant_types
--  Stores the filter tab definitions per product.
--  e.g.  product "Car Charger Adapters" → tabs: single | dual | fast | wireless
-- ============================================================
CREATE TABLE IF NOT EXISTS product_variant_types (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  product_id  INT UNSIGNED  NOT NULL,
  type_key    VARCHAR(100)  NOT NULL  COMMENT 'Matches product_variants.variant_type',
  type_label  VARCHAR(150)  NOT NULL  COMMENT 'Display label on the tab, e.g. Single Port',
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
--  SAMPLE DATA: Car Charger Adapters (for testing)
--  NOTE: Replace product_id values with the real IDs from
--  your products table after inserting the parent product.
-- ============================================================

-- Step 1: Insert the parent product into `products`
--         (only if it doesn't already exist)
INSERT IGNORE INTO products
  (id, name, slug, short_desc, base_price, is_customizable, is_active)
VALUES
  -- ADDED: sample parent product for Car Charger Adapters
  -- Update the id if it conflicts with your existing rows
  (1001, 'Car Charger Adapters', 'car-charger-adapter',
   'Custom-branded car charger adapters — single, dual, fast-charge, and wireless variants.',
   85.00, 1, 1);

-- Step 2: Insert the variant type tabs for this product
INSERT IGNORE INTO product_variant_types
  (product_id, type_key, type_label, sort_order)
VALUES
  (1001, 'single',   'Single Port',  1),
  (1001, 'dual',     'Dual Port',    2),
  (1001, 'fast',     'Fast Charge',  3),
  (1001, 'wireless', 'Wireless',     4);

-- Step 3: Insert the individual variants (items shown on items-page)
INSERT IGNORE INTO product_variants
  (product_id, variant_type, name, slug, specs, base_price, badge_label, sort_order)
VALUES
  (1001, 'single',   'Single USB-A Car Charger',       'car-charger-single-usba',  '5V / 2.1A • USB-A • Compact',            85.00,  NULL,          1),
  (1001, 'single',   'Single USB-C Car Charger',       'car-charger-single-usbc',  '5V / 3A • USB-C • Compact',              95.00,  'New',         2),
  (1001, 'dual',     'Dual Port USB-A Charger',        'car-charger-dual-usba',    '2x USB-A • 3.1A Total • Logo Printing',  110.00, 'Best Seller', 3),
  (1001, 'dual',     'USB-A + USB-C Dual Charger',     'car-charger-dual-mixed',   'USB-A + USB-C • 4A Total',               125.00, NULL,          4),
  (1001, 'fast',     '18W Quick Charge 3.0',           'car-charger-qc3',          'QC 3.0 • USB-A • 18W',                   145.00, NULL,          5),
  (1001, 'fast',     '20W USB-C PD Fast Charger',      'car-charger-pd20',         'USB-C PD • 20W • Compact Body',           165.00, 'New',         6),
  (1001, 'fast',     '45W GaN Dual Port Fast Charger', 'car-charger-gan45',        'GaN • USB-C + USB-A • 45W',              220.00, 'Premium',     7),
  (1001, 'wireless', 'Wireless Car Charging Mount',    'car-charger-wireless-mount','15W Qi • Gravity Holder • Auto-Clamp',  280.00, NULL,          8),
  (1001, 'wireless', 'Wireless Charging Pad (Dash)',   'car-charger-wireless-pad', '10W Qi • Non-Slip Base • Logo Imprint',  260.00, NULL,          9);


-- ============================================================
--  USEFUL QUERIES for the backend API serving items-page.html
-- ============================================================

-- Get all variant types (filter tabs) for a product slug:
-- SELECT pvt.type_key, pvt.type_label, COUNT(pv.id) AS item_count
-- FROM   product_variant_types pvt
-- JOIN   products p ON p.id = pvt.product_id
-- LEFT JOIN product_variants pv
--   ON pv.product_id = pvt.product_id AND pv.variant_type = pvt.type_key AND pv.is_active = 1
-- WHERE  p.slug = 'car-charger-adapter'
-- GROUP  BY pvt.type_key, pvt.type_label
-- ORDER  BY pvt.sort_order;

-- Get all active variants for a product (optionally filtered by type):
-- SELECT pv.id, pv.name, pv.slug, pv.specs, pv.base_price,
--        pv.sale_price, pv.badge_label, pv.variant_type, pv.thumbnail_url
-- FROM   product_variants pv
-- JOIN   products p ON p.id = pv.product_id
-- WHERE  p.slug = 'car-charger-adapter'
--   AND  pv.is_active = 1
--   -- AND pv.variant_type = 'fast'   ← uncomment to filter by tab
-- ORDER  BY pv.sort_order;

-- Get primary image for a variant:
-- SELECT image_url, alt_text
-- FROM   product_variant_images
-- WHERE  variant_id = ? AND is_primary = 1
-- LIMIT  1;


-- ============================================================
--  END OF ADDED: items-page SQL additions
-- ============================================================
