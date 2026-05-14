-- Hapus metode pembayaran Midtrans (kartu/e-wallet). Jalankan sekali di DB yang sudah ada.
-- mysql -u root scanteen < database/migrate_remove_midtrans_payment.sql

USE scanteen;

UPDATE orders SET payment_method = 'cashier' WHERE payment_method = 'midtrans';

ALTER TABLE orders
  MODIFY COLUMN payment_method ENUM('qris', 'cashier') NOT NULL;
