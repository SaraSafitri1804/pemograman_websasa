-- =============================================
-- DATABASE: loyaltypro
-- =============================================

CREATE DATABASE IF NOT EXISTS loyaltypro;
USE loyaltypro;

-- =============================================
-- TABEL: admin
-- =============================================
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================
-- TABEL: customer
-- =============================================
CREATE TABLE customer (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_customer VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    alamat TEXT,
    tier ENUM('Gold', 'Silver', 'Bronze') DEFAULT 'Bronze',
    total_poin INT DEFAULT 0,
    tanggal_daftar DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================
-- TABEL: transaksi
-- =============================================
CREATE TABLE transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice VARCHAR(50) NOT NULL UNIQUE,
    customer_id INT NOT NULL,
    tanggal DATE NOT NULL,
    total_belanja DECIMAL(15,2) NOT NULL,
    poin_didapat INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customer(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =============================================
-- TABEL: reward
-- =============================================
CREATE TABLE reward (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_reward VARCHAR(150) NOT NULL,
    jumlah_poin INT NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    foto VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================
-- TABEL: penukaran_reward
-- =============================================
CREATE TABLE penukaran_reward (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    reward_id INT NOT NULL,
    poin_digunakan INT NOT NULL,
    tanggal_tukar DATE NOT NULL,
    status ENUM('Berhasil', 'Dikirim', 'Digunakan') DEFAULT 'Berhasil',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customer(id) ON DELETE CASCADE,
    FOREIGN KEY (reward_id) REFERENCES reward(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =============================================
-- DATA AWAL: admin (password: admin123)
-- =============================================
INSERT INTO admin (username, password, nama_lengkap) VALUES
('admin', '$2y$10$39YQ/2ocMMqBglqtSfV4Mu5U5iWtesWPviO5WqJR/eWfwjlGn2Dhq', 'Administrator');

-- =============================================
-- DATA AWAL: customer
-- =============================================
INSERT INTO customer (id_customer, nama, email, no_hp, alamat, tier, total_poin, tanggal_daftar) VALUES
('CUST-89210', 'Budi Santoso', 'budi.santoso@example.com', '0812-3456-7890', 'Jl. Merdeka No. 10, Jakarta', 'Gold', 12450, '2022-05-15'),
('CUST-89211', 'Siti Aminah', 'siti.aminah@example.com', '0856-7890-1234', 'Jl. Sudirman No. 25, Bandung', 'Silver', 4200, '2022-08-20'),
('CUST-89212', 'Dewi Lestari', 'dewi.lestari@example.com', '0811-2233-4455', 'Jl. Gatot Subroto No. 5, Surabaya', 'Bronze', 950, '2023-01-10'),
('CUST-89213', 'Reza Rahadian', 'reza.rahadian@example.com', '0821-9988-7766', 'Jl. Diponegoro No. 8, Yogyakarta', 'Silver', 5120, '2022-11-03'),
('CUST-89214', 'Siti Rahmawati', 'siti.rahmawati@example.com', '0812-3456-7890', 'Jl. Thamrin No. 15, Jakarta', 'Gold', 24500, '2022-08-01');

-- =============================================
-- DATA AWAL: transaksi
-- =============================================
INSERT INTO transaksi (invoice, customer_id, tanggal, total_belanja, poin_didapat) VALUES
('INV-9982', 5, '2023-10-12', 450000, 450),
('INV-9101', 5, '2023-09-28', 320000, 320),
('INV-9050', 1, '2023-10-12', 1500000, 1500),
('INV-9023', 2, '2023-10-10', 750000, 750),
('INV-8990', 3, '2023-10-05', 200000, 200),
('INV-8970', 4, '2023-10-01', 500000, 500);

-- =============================================
-- DATA AWAL: reward
-- =============================================
INSERT INTO reward (nama_reward, jumlah_poin, stok) VALUES
('Voucher Kopi Rp 50.000', 500, 50),
('Potongan Belanja 10%', 1200, 30),
('Merchandise Payung Eksklusif', 2500, 15),
('Tiket Nonton Bioskop x2', 1500, 25),
('Voucher Makan Rp 100.000', 1000, 40);

-- =============================================
-- DATA AWAL: penukaran_reward
-- =============================================
INSERT INTO penukaran_reward (customer_id, reward_id, poin_digunakan, tanggal_tukar, status) VALUES
(5, 1, 500, '2023-10-01', 'Berhasil'),
(5, 2, 1200, '2023-08-15', 'Berhasil'),
(5, 3, 2500, '2023-06-10', 'Dikirim'),
(5, 4, 1500, '2023-02-02', 'Digunakan');
