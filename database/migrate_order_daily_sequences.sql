-- Add daily sequence table for order numbers like ORD-1405-001

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
