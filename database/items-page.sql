-- ============================================================
--  CREATIVEKIT 3A — Items & Product Variants Additions
--  File: database/items-page.sql
--
--  This script provides sample variant inserts and test queries
--  supporting items-page / car-charger-adapters.html across
--  all 8 categories in CreativeKit 3A.
-- ============================================================

USE creativekit3a;

-- Sample Variant types for Apparel (Corporate Jackets - Product ID 7)
INSERT IGNORE INTO product_variant_types
  (product_id, type_key, type_label, sort_order)
VALUES
  (7, 'windbreaker', 'Windbreaker',     1),
  (7, 'fleece',      'Fleece Lined',    2),
  (7, 'waterproof',  'Waterproof Pro',  3);

INSERT IGNORE INTO product_variants
  (product_id, variant_type, name, slug, specs, thumbnail_url, base_price, badge_label, sort_order)
VALUES
  (7, 'windbreaker', 'Executive Light Windbreaker', 'jacket-exec-windbreaker', 'Microfiber • Water-repellent • Embroidered Chest Logo', 'assets/images/corporate-jacket.png', 720.00, 'Best Seller', 1),
  (7, 'fleece',      'Fleece-Lined Team Jacket',   'jacket-fleece-team',       'Polyester exterior • Thermal fleece inner layer',      'assets/images/corporate-jacket.png', 850.00, 'New',         2),
  (7, 'waterproof',  'Heavy Duty Waterproof Parka','jacket-waterproof-parka',  'Seam-sealed • Detachable hood • Storm flap',           'assets/images/corporate-jacket.png', 980.00, 'Premium',     3);

-- Sample Variant types for Drinkware (Stainless Steel Tumblers - Product ID 15)
INSERT IGNORE INTO product_variant_types
  (product_id, type_key, type_label, sort_order)
VALUES
  (15, '500ml', '500ml Standard', 1),
  (15, '750ml', '750ml Large',    2),
  (15, 'straw', 'With Straw Lid', 3);

INSERT IGNORE INTO product_variants
  (product_id, variant_type, name, slug, specs, thumbnail_url, base_price, badge_label, sort_order)
VALUES
  (15, '500ml', 'Stainless Tumbler 500ml Matte', 'tumbler-500ml-matte', 'Double Wall • 18/8 Stainless Steel • Matte Powder Coat', 'assets/images/stainless-tumbler.png', 310.00, 'Best Seller', 1),
  (15, '750ml', 'Stainless Tumbler 750ml Sport', 'tumbler-750ml-sport', 'Hydration Flask • Wide mouth • Handle loop cap',         'assets/images/stainless-tumbler.png', 380.00, 'New',         2),
  (15, 'straw', 'Stainless Tumbler with Straw',  'tumbler-straw-lid',    'Leak-proof flip straw lid • Car cup holder friendly',     'assets/images/stainless-tumbler.png', 350.00, 'Popular',     3);

-- Sample Variant types for Bags (Classic Zip Totes - Product ID 12)
INSERT IGNORE INTO product_variant_types
  (product_id, type_key, type_label, sort_order)
VALUES
  (12, 'canvas', 'Standard Canvas', 1),
  (12, 'pocket', 'Front Pocket',    2),
  (12, 'gusset', 'Wide Gusset',     3);

INSERT IGNORE INTO product_variants
  (product_id, variant_type, name, slug, specs, thumbnail_url, base_price, badge_label, sort_order)
VALUES
  (12, 'canvas', 'Heavy Canvas Zipper Tote', 'tote-heavy-canvas-zip', '12oz 100% Cotton Canvas • Full top zipper closure', 'assets/images/tote-bag.png', 120.00, 'Best Seller', 1),
  (12, 'pocket', 'Canvas Tote with Front Pocket', 'tote-front-pocket', 'Dual carrying handles • Easy-access front slip pocket', 'assets/images/tote-bag.png', 145.00, 'New', 2),
  (12, 'gusset', 'Wide-Bottom Grocery Tote', 'tote-wide-gusset',       'Extra wide 5" gusset • Reinforced cross-stitching',   'assets/images/tote-bag.png', 160.00, 'Eco', 3);

-- ============================================================
--  USEFUL QUERIES for the backend API serving items-page.html
-- ============================================================

-- Query 1: Get all variant types (filter tabs) for a product:
-- SELECT pvt.type_key, pvt.type_label, COUNT(pv.id) AS item_count
-- FROM   product_variant_types pvt
-- JOIN   products p ON p.id = pvt.product_id
-- LEFT JOIN product_variants pv
--   ON pv.product_id = pvt.product_id AND pv.variant_type = pvt.type_key AND pv.is_active = 1
-- WHERE  p.slug = 'car-charger-adapter'
-- GROUP  BY pvt.type_key, pvt.type_label
-- ORDER  BY pvt.sort_order;

-- Query 2: Get all active variants for a product:
-- SELECT pv.id, pv.name, pv.slug, pv.specs, pv.base_price,
--        pv.sale_price, pv.badge_label, pv.variant_type, pv.thumbnail_url
-- FROM   product_variants pv
-- JOIN   products p ON p.id = pv.product_id
-- WHERE  p.slug = 'car-charger-adapter'
--   AND  pv.is_active = 1
-- ORDER  BY pv.sort_order;
