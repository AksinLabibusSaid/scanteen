-- Scanteen schema + seed data (Customer flow + shared master data)
-- Run in MySQL/MariaDB: mysql -u root < database/scanteen.sql

CREATE DATABASE IF NOT EXISTS scanteen
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE scanteen;

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------------
-- Master: venue (kantin / lokasi)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS venues (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name          VARCHAR(120)    NOT NULL,
  slug          VARCHAR(64)     NOT NULL,
  created_at    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_venues_slug (slug)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------------
-- Meja + token untuk QR/barcode (customer scan)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS dining_tables (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  venue_id        BIGINT UNSIGNED NOT NULL,
  table_number    VARCHAR(32)     NOT NULL,
  barcode_token   VARCHAR(64)     NOT NULL,
  is_active       TINYINT(1)      NOT NULL DEFAULT 1,
  created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_dining_tables_token (barcode_token),
  KEY idx_dining_tables_venue (venue_id),
  CONSTRAINT fk_dining_tables_venue
    FOREIGN KEY (venue_id) REFERENCES venues (id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------------
-- Warung (tenant penjual)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS warungs (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  venue_id      BIGINT UNSIGNED NOT NULL,
  name          VARCHAR(120)    NOT NULL,
  slug          VARCHAR(64)     NOT NULL,
  sort_order    INT             NOT NULL DEFAULT 0,
  is_active     TINYINT(1)      NOT NULL DEFAULT 1,
  created_at    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_warungs_venue_slug (venue_id, slug),
  KEY idx_warungs_venue (venue_id),
  CONSTRAINT fk_warungs_venue
    FOREIGN KEY (venue_id) REFERENCES venues (id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------------
-- Kategori menu (filter UI)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS menu_categories (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name          VARCHAR(80)     NOT NULL,
  slug          VARCHAR(64)     NOT NULL,
  sort_order    INT             NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uq_menu_categories_slug (slug)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------------
-- Menu per warung
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS menus (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  warung_id     BIGINT UNSIGNED NOT NULL,
  category_id   BIGINT UNSIGNED NOT NULL,
  name          VARCHAR(160)    NOT NULL,
  description   VARCHAR(255)    DEFAULT NULL,
  price         DECIMAL(12,2)   NOT NULL,
  image_url     VARCHAR(512)    DEFAULT NULL,
  is_available  TINYINT(1)      NOT NULL DEFAULT 1,
  created_at    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_menus_warung (warung_id),
  KEY idx_menus_category (category_id),
  CONSTRAINT fk_menus_warung
    FOREIGN KEY (warung_id) REFERENCES warungs (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_menus_category
    FOREIGN KEY (category_id) REFERENCES menu_categories (id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------------
-- Pesanan customer
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS orders (
  id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  venue_id          BIGINT UNSIGNED NOT NULL,
  dining_table_id   BIGINT UNSIGNED NOT NULL,
  order_number      VARCHAR(32)     NOT NULL,
  public_token      CHAR(32)        NOT NULL,
  customer_name     VARCHAR(120)    DEFAULT NULL,
  customer_email    VARCHAR(180)    DEFAULT NULL,
  dining_type       ENUM('dine_in','take_away') NOT NULL DEFAULT 'dine_in',
  payment_method    ENUM('qris','cashier') NOT NULL,
  status            ENUM(
                      'pending_payment',
                      'paid',
                      'accepted',
                      'processing',
                      'ready',
                      'completed',
                      'cancelled'
                    ) NOT NULL DEFAULT 'pending_payment',
  subtotal          DECIMAL(12,2)   NOT NULL DEFAULT 0.00,
  service_tax       DECIMAL(12,2)   NOT NULL DEFAULT 0.00,
  total             DECIMAL(12,2)   NOT NULL DEFAULT 0.00,
  payment_deadline_at DATETIME      DEFAULT NULL,
  created_at        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  gateway_order_id  VARCHAR(64)     DEFAULT NULL COMMENT 'legacy / tidak dipakai',
  PRIMARY KEY (id),
  UNIQUE KEY uq_orders_order_number (order_number),
  UNIQUE KEY uq_orders_public_token (public_token),
  KEY idx_orders_table (dining_table_id),
  KEY idx_orders_status (status),
  CONSTRAINT fk_orders_venue
    FOREIGN KEY (venue_id) REFERENCES venues (id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_orders_table
    FOREIGN KEY (dining_table_id) REFERENCES dining_tables (id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------------
-- Counter antrian pesanan per hari
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS order_daily_sequences (
  venue_id      BIGINT UNSIGNED NOT NULL,
  order_date    DATE            NOT NULL,
  last_sequence INT UNSIGNED    NOT NULL DEFAULT 0,
  updated_at    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (venue_id, order_date),
  CONSTRAINT fk_order_daily_sequences_venue
    FOREIGN KEY (venue_id) REFERENCES venues (id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------------
-- Status pengolahan per warung (satu pesanan multi-stan)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS order_warung_fulfillment (
  order_id    BIGINT UNSIGNED NOT NULL,
  warung_id   BIGINT UNSIGNED NOT NULL,
  status      ENUM('new','preparing','ready') NOT NULL DEFAULT 'new',
  updated_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (order_id, warung_id),
  CONSTRAINT fk_owf_order
    FOREIGN KEY (order_id) REFERENCES orders (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_owf_warung
    FOREIGN KEY (warung_id) REFERENCES warungs (id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------------
-- Staff: admin / kasir / warung (warung_id wajib jika role = warung)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS staff_users (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  venue_id        BIGINT UNSIGNED NOT NULL,
  email           VARCHAR(180)    NOT NULL,
  password_hash   VARCHAR(255)    NOT NULL,
  name            VARCHAR(120)    NOT NULL,
  role            ENUM('admin','kasir','warung') NOT NULL,
  warung_id       BIGINT UNSIGNED DEFAULT NULL,
  is_active       TINYINT(1)      NOT NULL DEFAULT 1,
  created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_staff_venue_email (venue_id, email),
  KEY idx_staff_role (venue_id, role),
  CONSTRAINT fk_staff_venue
    FOREIGN KEY (venue_id) REFERENCES venues (id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_staff_warung
    FOREIGN KEY (warung_id) REFERENCES warungs (id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------------
-- Log transaksi payment gateway (Midtrans Snap, dsb.)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS payment_gateway_transactions (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id        BIGINT UNSIGNED NOT NULL,
  provider        VARCHAR(32)     NOT NULL DEFAULT 'midtrans',
  external_id     VARCHAR(120)    DEFAULT NULL,
  status          VARCHAR(40)     NOT NULL DEFAULT 'pending',
  snap_token      TEXT            DEFAULT NULL,
  gross_amount    DECIMAL(12,2)   NOT NULL DEFAULT 0.00,
  currency        VARCHAR(8)      NOT NULL DEFAULT 'IDR',
  raw_request     TEXT            DEFAULT NULL,
  raw_response    TEXT            DEFAULT NULL,
  raw_notification TEXT           DEFAULT NULL,
  created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_pgt_order (order_id),
  KEY idx_pgt_provider_ext (provider, external_id),
  CONSTRAINT fk_pgt_order
    FOREIGN KEY (order_id) REFERENCES orders (id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
  id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id            BIGINT UNSIGNED NOT NULL,
  menu_id             BIGINT UNSIGNED NOT NULL,
  warung_id           BIGINT UNSIGNED NOT NULL,
  menu_name_snapshot  VARCHAR(160)    NOT NULL,
  unit_price          DECIMAL(12,2)   NOT NULL,
  quantity            INT UNSIGNED    NOT NULL DEFAULT 1,
  note                VARCHAR(255)    DEFAULT NULL,
  line_subtotal       DECIMAL(12,2)   NOT NULL,
  PRIMARY KEY (id),
  KEY idx_order_items_order (order_id),
  KEY idx_order_items_menu (menu_id),
  CONSTRAINT fk_order_items_order
    FOREIGN KEY (order_id) REFERENCES orders (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_order_items_menu
    FOREIGN KEY (menu_id) REFERENCES menus (id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_order_items_warung
    FOREIGN KEY (warung_id) REFERENCES warungs (id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------------
-- Seed
-- ---------------------------------------------------------------------------
INSERT IGNORE INTO venues (id, name, slug) VALUES
  (1, 'Kantin Demo', 'demo');

INSERT IGNORE INTO dining_tables (id, venue_id, table_number, barcode_token, is_active) VALUES
  (1, 1, '12', 'scan_demo_meja_12', 1);

INSERT IGNORE INTO warungs (id, venue_id, name, slug, sort_order, is_active) VALUES
  (1, 1, 'Warung 1', 'warung-1', 1, 1),
  (2, 1, 'Warung 2', 'warung-2', 2, 1),
  (3, 1, 'Warung 3', 'warung-3', 3, 1);

INSERT IGNORE INTO menu_categories (id, name, slug, sort_order) VALUES
  (1, 'Semua', 'semua', 0),
  (2, 'Makanan', 'makanan', 1),
  (3, 'Minuman', 'minuman', 2),
  (4, 'Jajanan', 'jajanan', 3);

INSERT IGNORE INTO menus (id, warung_id, category_id, name, description, price, image_url, is_available) VALUES
  (1, 1, 2, 'Wader Goreng', NULL, 25000.00, 'https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380', 1),
  (2, 1, 2, 'Soto Babat', NULL, 25000.00, 'https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380', 1),
  (3, 1, 2, 'Mie Instan', NULL, 12000.00, 'https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380', 1),
  (4, 2, 2, 'Rawon Jumbo', NULL, 25000.00, 'https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380', 1),
  (5, 2, 2, 'Bubur Ayam', NULL, 18000.00, 'https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380', 1),
  (6, 2, 2, 'Nasi Kuning Telur', NULL, 22000.00, 'https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380', 1),
  (7, 3, 3, 'Es Teh Manis', NULL, 5000.00, 'https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380', 1),
  (8, 3, 4, 'Keripik Tempe', NULL, 8000.00, 'https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380', 1);

-- Akun staff demo (sandi: scanteen123)
INSERT IGNORE INTO staff_users (venue_id, email, password_hash, name, role, warung_id, is_active) VALUES
  (1, 'admin@scanteen.local', '$2y$10$6iwpyzibN1cTeUIRHbmeEOmaws/OYCxlNIoguPv2RrG7yC2ylKbCe', 'Admin Demo', 'admin', NULL, 1),
  (1, 'kasir@scanteen.local', '$2y$10$6iwpyzibN1cTeUIRHbmeEOmaws/OYCxlNIoguPv2RrG7yC2ylKbCe', 'Kasir Demo', 'kasir', NULL, 1),
  (1, 'warung1@scanteen.local', '$2y$10$6iwpyzibN1cTeUIRHbmeEOmaws/OYCxlNIoguPv2RrG7yC2ylKbCe', 'Stan Warung 1', 'warung', 1, 1);
