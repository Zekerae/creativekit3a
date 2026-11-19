-- ============================================================
--  CreativeKit3A — Full Database Schema & Seed Data
--  Database: creativekit3a_db
--  Engine: InnoDB | Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
-- ============================================================

DROP DATABASE IF EXISTS creativekit3a_db;
CREATE DATABASE creativekit3a_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE creativekit3a_db;

-- ============================================================
--  TABLE 1: users (Role-Based Access Control)
-- ============================================================
CREATE TABLE users (
  id            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  first_name    VARCHAR(100)    NOT NULL,
  last_name     VARCHAR(100)    NOT NULL,
  email         VARCHAR(255)    NOT NULL UNIQUE,
  phone         VARCHAR(30)     DEFAULT NULL,
  password_hash VARCHAR(255)    NOT NULL COMMENT 'Bcrypt hash',
  role          ENUM('admin','staff') NOT NULL DEFAULT 'staff',
  is_active     TINYINT(1)      NOT NULL DEFAULT 1,
  created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  last_login_at DATETIME        DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE INDEX idx_email (email),
  INDEX idx_role (role)
) ENGINE=InnoDB;

-- ============================================================
--  TABLE 2: categories
-- ============================================================
CREATE TABLE categories (
  id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  parent_id   INT UNSIGNED    DEFAULT NULL,
  name        VARCHAR(150)    NOT NULL,
  slug        VARCHAR(160)    NOT NULL UNIQUE,
  description TEXT            DEFAULT NULL,
  icon        VARCHAR(50)     DEFAULT 'fa-box',
  sort_order  SMALLINT        NOT NULL DEFAULT 0,
  is_active   TINYINT(1)      NOT NULL DEFAULT 1,
  created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_parent (parent_id),
  CONSTRAINT fk_cat_parent
    FOREIGN KEY (parent_id) REFERENCES categories(id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
--  TABLE 3: products (Full CRUD target)
-- ============================================================
CREATE TABLE products (
  id            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  category_id   INT UNSIGNED    NOT NULL,
  name          VARCHAR(200)    NOT NULL,
  slug          VARCHAR(220)    NOT NULL,
  sku           VARCHAR(50)     DEFAULT NULL UNIQUE,
  description   TEXT            DEFAULT NULL,
  specs         VARCHAR(255)    DEFAULT NULL,
  price_min     DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  image         VARCHAR(255)    DEFAULT '/assets/images/1.png',
  badge         VARCHAR(50)     DEFAULT NULL,
  status        ENUM('active','draft','archived') NOT NULL DEFAULT 'active',
  is_featured   TINYINT(1)      NOT NULL DEFAULT 0,
  created_by    INT UNSIGNED    DEFAULT NULL,
  created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_category (category_id),
  INDEX idx_status   (status),
  INDEX idx_featured (is_featured),
  CONSTRAINT fk_prod_category
    FOREIGN KEY (category_id) REFERENCES categories(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_prod_creator
    FOREIGN KEY (created_by) REFERENCES users(id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
--  TABLE 4: quote_requests (Lead Management)
-- ============================================================
CREATE TABLE quote_requests (
  id             INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  product_id     INT UNSIGNED    DEFAULT NULL,
  customer_name  VARCHAR(100)    NOT NULL,
  company        VARCHAR(100)    DEFAULT NULL,
  email          VARCHAR(100)    NOT NULL,
  phone          VARCHAR(30)     DEFAULT NULL,
  product_type   VARCHAR(100)    DEFAULT NULL,
  quantity       INT             DEFAULT 100,
  budget         VARCHAR(50)     DEFAULT NULL,
  deadline       DATE            DEFAULT NULL,
  details        TEXT            DEFAULT NULL,
  status         ENUM('new','reviewing','quoted','accepted','declined') NOT NULL DEFAULT 'new',
  assigned_to    INT UNSIGNED    DEFAULT NULL,
  created_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_status (status),
  CONSTRAINT fk_quote_product
    FOREIGN KEY (product_id) REFERENCES products(id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_quote_assigned
    FOREIGN KEY (assigned_to) REFERENCES users(id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
--  TABLE 5: activity_log (Audit Trail)
-- ============================================================
CREATE TABLE activity_log (
  id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  user_id     INT UNSIGNED    DEFAULT NULL,
  action      VARCHAR(50)     NOT NULL,
  entity_type VARCHAR(50)     DEFAULT NULL,
  entity_id   INT UNSIGNED    DEFAULT NULL,
  details     TEXT            DEFAULT NULL,
  ip_address  VARCHAR(45)     DEFAULT NULL,
  created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_user    (user_id),
  INDEX idx_action  (action),
  INDEX idx_created (created_at),
  CONSTRAINT fk_log_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;


-- ============================================================
--  SEED DATA
-- ============================================================

-- ---- Users ----
-- Default password for admin: admin123
-- Default password for staff: staff123
INSERT INTO users (id, first_name, last_name, email, phone, password_hash, role, is_active) VALUES
  (1, 'Admin',   'Manager', 'admin@creativekit3a.com',  '+63 917 123 4567', '$2y$12$/JvSmT2QiFAI7g2GqAKFIei.LodOsSf.NCR8p6YRcCaoy.RBEIo5O', 'admin', 1),
  (2, 'Maria',   'Santos',  'maria@creativekit3a.com',  '+63 933 992 7410', '$2y$12$ADGGe7Q/OVwQaN5yvItdY.zlzlFD/lUUO64ozQwwapHYANYFZeIxe', 'staff', 1),
  (3, 'Juan',    'Cruz',    'juan@creativekit3a.com',   '+63 917 714 2774', '$2y$12$ADGGe7Q/OVwQaN5yvItdY.zlzlFD/lUUO64ozQwwapHYANYFZeIxe', 'staff', 1),
  (4, 'Regina',  'Reyes',   'regina@creativekit3a.com', '+63 918 555 1234', '$2y$12$ADGGe7Q/OVwQaN5yvItdY.zlzlFD/lUUO64ozQwwapHYANYFZeIxe', 'staff', 1);

-- ---- Categories (8 Main Categories + Subcategories) ----
INSERT INTO categories (id, parent_id, name, slug, description, icon, sort_order, is_active) VALUES
  (1, NULL, 'Accessories & Gadgets', 'accessories-gadgets', 'Car, computer, desk, mobile accessories and gadgets', 'fa-plug',         1, 1),
  (2, NULL, 'Apparel',               'apparel',             'Caps, jackets, and corporate shirts',                 'fa-tshirt',       2, 1),
  (3, NULL, 'Bags & Pouches',        'bags-pouches',        'Backpacks, eco bags, laptop bags, tote bags',         'fa-shopping-bag', 3, 1),
  (4, NULL, 'Drinkware',             'drinkware',           'Tumblers, mugs, insulated water bottles',             'fa-glass-water',  4, 1),
  (5, NULL, 'Gift Set',              'giftset',             'Curated corporate gift sets for all occasions',       'fa-gift',         5, 1),
  (6, NULL, 'Home Living',           'home-living',         'Cleaning essentials, cutlery, towels, pillows',       'fa-house',        6, 1),
  (7, NULL, 'Pen & Paper',           'pen-paper',           'Pens, notebooks, calendars, padfolios',               'fa-pen',          7, 1),
  (8, NULL, 'PU Leather',            'pu-leather',          'Card cases, desk mats, wallets, keychains',           'fa-wallet',       8, 1),
  -- Subcategories under Accessories & Gadgets (id: 1)
  (9,  1,   'Car Accessories',       'car-accessories',     'In-car chargers, organizers, dashcams',               'fa-car',          1, 1),
  (10, 1,   'Computer Accessories',  'computer-accessories','Mice, pads, USB hubs, audio',                         'fa-laptop',       2, 1),
  (11, 1,   'Desk Accessories',      'desk-accessories',    'Clocks, pen holders, stands',                         'fa-desktop',      3, 1),
  (12, 1,   'Mobile Accessories',    'mobile-accessories',  'Power banks, stands, cables',                         'fa-mobile',       4, 1),
  (13, 1,   'Gadgets',               'gadgets',             'Portable fans, rechargeable gadgets',                 'fa-microchip',    5, 1);

-- ---- Products ----
INSERT INTO products (id, category_id, name, slug, sku, description, specs, price_min, image, badge, status, is_featured, created_by) VALUES
  (1,  9,  'Single USB Car Charger',        'car-charger-single',     'ACC-CC-001', 'Custom-branded single USB car charger adapter for promotional giveaways', '5V / 2.1A • USB-A • Compact',              85.00,  '/assets/images/1.png',                    '',            'active',   1, 1),
  (2,  9,  'Dual USB Fast Car Adapter',     'car-charger-dual',       'ACC-CC-002', 'Dual-port fast charging car adapter with custom full-color logo print',   '2x USB-A • 3.1A Total • Logo Printing',   110.00,  '/assets/images/dual-usb-adapter.png',     'Best Seller', 'active',   1, 1),
  (3,  9,  'USB-C Car Charger Adapter',     'car-charger-usbc',       'ACC-CC-003', 'Modern USB-C compact car charger with rapid Power Delivery support',      '5V / 3A • USB-C • Compact',                95.00,  '/assets/images/usb-c-adapter.png',        'New',         'active',   1, 1),
  (4,  9,  'Car Seat-Back Organizer',       'car-organizer',          'ACC-CO-001', 'Multi-pocket Oxford cloth seat organizer for corporate travel gifts',     'Oxford Cloth • 5 Pockets',                 88.00,  '/assets/images/car-organizer.png',        '',            'active',   1, 1),
  (5,  9,  '1080p HD Dash Cam',             'car-dashcam',            'ACC-DC-001', 'Full HD dash camera with 140-degree wide angle and night vision lens',    '1080p • 140° FOV • Loop Recording',       379.90,  '/assets/images/dash-cam.png',             '',            'active',   0, 1),
  (6,  10, 'Custom Branded Mouse Pad',      'mouse-pad',              'ACC-MP-001', 'Full-color sublimation mouse pad with durable stitched non-slip base',    'Full-color print • Non-slip base',          80.00,  '/assets/images/mouse-pad.png',            '',            'active',   1, 1),
  (7,  10, '4-Port USB Hub Adapter',        'usb-hub',                'ACC-UH-001', 'High-speed 4-port USB 3.0 expander with custom laser etched brand logo',  '4-Port • USB 3.0 • Plug & Play',          195.00,  '/assets/images/usb-hub.png',              '',            'active',   0, 1),
  (8,  10, 'Wireless Bluetooth Earbuds',    'audio-accessories',      'ACC-AU-001', 'TWS wireless earbuds with custom printed charging case',                   'Bluetooth 5.0 • TWS • Charging Case',     380.00,  '/assets/images/audio-accessories.png',    '',            'active',   0, 1),
  (9,  11, 'Woven Bamboo Desktop Clock',    'woven-clock',            'ACC-WC-001', 'Eco-friendly desktop clock with natural bamboo weave exterior casing',    'Bamboo frame • Silent mechanism',          149.40,  '/assets/images/wooven-clock.png',         '',            'active',   1, 1),
  (10, 11, 'Leatherette Desk Organizer',    'desk-organizer',         'ACC-DO-001', 'Multi-compartment desk caddy with debossed corporate logo placement',     'PU Leather • 5 slots • Logo emboss',        95.00,  '/assets/images/desk-organizer.png',       '',            'active',   0, 1),
  (11, 12, '10,000mAh Slim Power Bank',     'mobile-powerbank',       'ACC-PB-001', 'Slim portable power bank with dual fast charging outputs & LED display',   '10000mAh • Dual USB • LED indicator',     350.00,  '/assets/images/mobile-powerbank.png',     'Best Seller', 'active',   1, 1),
  (12, 12, 'Foldable Aluminum Phone Stand', 'mobile-stand',           'ACC-MS-001', 'Pocket foldable universal smartphone & tablet desktop desk holder',       'Aluminum • Foldable • Universal fit',       65.00,  '/assets/images/mobile-stand.png',         '',            'active',   0, 1),
  (13, 13, 'Portable Rechargeable Mini Fan','portable-fan',           'ACC-PF-001', 'Handheld 3-speed USB rechargeable fan with lanyard for outdoor events',    'USB-C charge • 3 speed • 6hr battery',    180.00,  '/assets/images/portable-fan.png',         '',            'active',   1, 1),
  (14, 13, '20,000mAh High Capacity Bank',  'powerbank-20000',        'ACC-PB-002', 'Heavy-duty power bank with 20W Power Delivery for business travelers',    '20000mAh • PD 20W • LED display',         380.00,  '/assets/images/power-banks.png',          'Premium',     'active',   0, 1),
  (15, 2,  'Custom Corporate Jacket',       'corporate-jacket',       'APP-CJ-001', 'Premium lightweight windbreaker jacket with embroidered breast logo',     'Polyester blend • Custom embroidery',      650.00,  '/assets/images/corporate-jacket.jpg',     '',            'active',   1, 1),
  (16, 3,  'Executive Laptop Backpack',     'laptop-backpack',        'BAG-LB-001', 'Waterproof business laptop backpack with padded straps & USB pass-thru',  '15.6" laptop • Water resistant',           167.90,  '/assets/images/laptop-bag.png',           '',            'active',   1, 1),
  (17, 4,  'Eco Bamboo Tumbler 450ml',      'bamboo-tumbler',         'DRK-BT-001', 'Vacuum insulated stainless steel tumbler with natural bamboo outer shell', '450ml • Bamboo Shell • Leak-Proof',        250.00,  '/assets/images/bamboo-tumbler.png',       'Eco-Friendly','active',   1, 1),
  (18, 6,  'Cordless Handheld Vacuum',      'handheld-vacuum',        'HOM-HV-001', 'Compact rechargeable car and desk vacuum with HEPA washable filter',      'USB-C charge • HEPA • 4000Pa',            499.00,  '/assets/images/handheld-vacuum.png',      '',            'active',   1, 1),
  (19, 1,  'Car Charger Adapters Set',      'car-charger-adapters',   'ACC-CB-001', 'Showcase line of custom promotional car charger adapters and cables',     'Single, Dual, Fast-Charge variants',       85.00,  '/assets/images/car-charger-adapters.png', '',            'active',   0, 1),
  (20, 4,  'EcoSip Double-Wall Tumbler',    'ecosip-tumbler',         'DRK-ES-001', 'Double-walled reusable travel tumbler with splash-proof slider lid',      '500ml • BPA-Free • Double-walled',         220.00,  '/assets/images/EcoSip_list_us.png',       'New',         'draft',    0, 2);

-- ---- Quote Requests ----
INSERT INTO quote_requests (id, product_id, customer_name, company, email, phone, product_type, quantity, budget, deadline, details, status, assigned_to, created_at) VALUES
  (1,  1,    'Carlo Reyes',     'TechCorp Philippines', 'carlo@techcorp.ph',     '+63 917 111 1111', 'Car Charger',     500,  '₱50,000',  '2026-12-15', 'Need custom logo printing on single USB chargers for year-end team giveaway.',   'new',       NULL, '2026-11-10 09:15:00'),
  (2,  17,   'Angela Lim',      'GreenLeaf Ventures',   'angela@greenleaf.com',  '+63 917 222 2222', 'Bamboo Tumbler',  1000, '₱250,000', '2026-12-01', 'Laser-engraved company logo on bamboo tumblers for annual CSR summit.',           'reviewing', 2,    '2026-11-11 10:30:00'),
  (3,  15,   'Roberto Tan',     'Apex BPO Solutions',   'roberto@apexbpo.com',   '+63 917 333 3333', 'Corporate Jacket',200,  '₱130,000', '2027-01-10', 'Embroidered jackets for new employee onboarding welcome kits.',                  'quoted',    2,    '2026-11-12 14:20:00'),
  (4,  11,   'Lisa Chen',       'FinTech Innovators',   'lisa@fintech.co',       '+63 917 444 4444', 'Power Bank',      300,  '₱105,000', '2026-12-20', 'Custom branded 10,000mAh power banks featuring corporate gradient logo.',         'accepted',  3,    '2026-11-13 16:45:00'),
  (5,  16,   'Mark Villanueva', 'EventsPro Manila',     'mark@eventspro.ph',     '+63 917 555 5555', 'Laptop Bag',      150,  '₱30,000',  '2027-01-05', 'Laptop backpacks for tech conference VIP speaker tokens.',                         'new',       NULL, '2026-11-14 11:00:00'),
  (6,  NULL, 'Sarah Garcia',    'Manila Heritage Council','sarah@heritage.gov.ph','+63 917 666 6666', 'Mixed Giveaways', 2000, '₱500,000', '2027-02-14', 'Assorted corporate giveaways for foundation anniversary: tumblers, pens, notebooks.','new',    NULL, '2026-11-15 13:10:00'),
  (7,  6,    'David Sy',        'Pixel Studio Design',  'david@pixelstudio.ph',  '+63 917 777 7777', 'Mouse Pad',       1000, '₱80,000',  '2026-12-28', 'Full-color custom printed gaming-grade mouse pads for client appreciation gifts.', 'declined', 3,    '2026-11-16 15:30:00');

-- ---- Activity Log ----
INSERT INTO activity_log (id, user_id, action, entity_type, entity_id, details, ip_address, created_at) VALUES
  (1,  1, 'login',          NULL,       NULL, 'Admin User logged in successfully',                        '127.0.0.1',   '2026-11-10 09:00:00'),
  (2,  1, 'create_product', 'product',  1,    'Created product: Single USB Car Charger (ACC-CC-001)',     '127.0.0.1',   '2026-11-10 09:10:00'),
  (3,  1, 'create_product', 'product',  2,    'Created product: Dual USB Fast Car Adapter (ACC-CC-002)',  '127.0.0.1',   '2026-11-10 09:25:00'),
  (4,  2, 'login',          NULL,       NULL, 'Staff Maria Santos logged in successfully',                '192.168.1.5', '2026-11-11 10:00:00'),
  (5,  2, 'update_quote',   'quote',    2,    'Assigned Quote #2 to Maria Santos and set to reviewing',   '192.168.1.5', '2026-11-11 10:45:00'),
  (6,  1, 'create_product', 'product',  15,   'Created product: Custom Corporate Jacket (APP-CJ-001)',    '127.0.0.1',   '2026-11-12 11:30:00'),
  (7,  3, 'login',          NULL,       NULL, 'Staff Juan Cruz logged in successfully',                   '192.168.1.10','2026-11-13 14:00:00'),
  (8,  3, 'update_quote',   'quote',    4,    'Updated Quote #4 status to accepted',                      '192.168.1.10','2026-11-13 16:50:00'),
  (9,  1, 'create_user',    'user',     4,    'Registered new staff account: Regina Reyes',               '127.0.0.1',   '2026-11-14 09:20:00'),
  (10, 2, 'update_quote',   'quote',    3,    'Sent quotation for Quote #3 (200 jackets)',                '192.168.1.5', '2026-11-15 15:00:00');
