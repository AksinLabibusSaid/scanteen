-- Jalankan pada DB yang sudah ada (upgrade dari skema lama).
USE scanteen;

ALTER TABLE orders
  MODIFY COLUMN payment_method ENUM('qris','cashier','midtrans') NOT NULL;

ALTER TABLE orders
  ADD COLUMN gateway_order_id VARCHAR(64) DEFAULT NULL COMMENT 'order_id ke Midtrans' AFTER updated_at;

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

CREATE TABLE IF NOT EXISTS staff_users (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  venue_id        BIGINT UNSIGNED NOT NULL,
  email           VARCHAR(180)    NOT NULL,
  password_hash   VARCHAR(255)    NOT NULL,
  name            VARCHAR(120)    NOT NULL,
  phone           VARCHAR(24)     DEFAULT NULL,
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

CREATE TABLE IF NOT EXISTS payment_gateway_transactions (
  id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id         BIGINT UNSIGNED NOT NULL,
  provider         VARCHAR(32)     NOT NULL DEFAULT 'midtrans',
  external_id      VARCHAR(120)    DEFAULT NULL,
  status           VARCHAR(40)     NOT NULL DEFAULT 'pending',
  snap_token       TEXT            DEFAULT NULL,
  gross_amount     DECIMAL(12,2)   NOT NULL DEFAULT 0.00,
  currency         VARCHAR(8)      NOT NULL DEFAULT 'IDR',
  raw_request      TEXT            DEFAULT NULL,
  raw_response     TEXT            DEFAULT NULL,
  raw_notification TEXT            DEFAULT NULL,
  created_at       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_pgt_order (order_id),
  KEY idx_pgt_provider_ext (provider, external_id),
  CONSTRAINT fk_pgt_order
    FOREIGN KEY (order_id) REFERENCES orders (id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT IGNORE INTO staff_users (venue_id, email, password_hash, name, role, warung_id, is_active) VALUES
  (1, 'admin@scanteen.local', '$2y$10$6iwpyzibN1cTeUIRHbmeEOmaws/OYCxlNIoguPv2RrG7yC2ylKbCe', 'Admin Demo', 'admin', NULL, 1),
  (1, 'kasir@scanteen.local', '$2y$10$6iwpyzibN1cTeUIRHbmeEOmaws/OYCxlNIoguPv2RrG7yC2ylKbCe', 'Kasir Demo', 'kasir', NULL, 1),
  (1, 'warung1@scanteen.local', '$2y$10$6iwpyzibN1cTeUIRHbmeEOmaws/OYCxlNIoguPv2RrG7yC2ylKbCe', 'Stan Warung 1', 'warung', 1, 1);
