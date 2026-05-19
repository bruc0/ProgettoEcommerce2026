CREATE DATABASE IF NOT EXISTS progetto_ecommerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE progetto_ecommerce;

CREATE TABLE IF NOT EXISTS autos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(100) NOT NULL,
    modello VARCHAR(100) NOT NULL,
    prezzo INT UNSIGNED NOT NULL,
    chilometraggio INT UNSIGNED NOT NULL,
    immatricolazione DATE NOT NULL,
    carburante ENUM('Benzina', 'Diesel', 'Elettrica', 'Ibrida', 'GPL') NOT NULL,
    potenza_cv INT UNSIGNED NOT NULL,
    colore VARCHAR(50) NOT NULL,
    garanzia TINYINT(1) NOT NULL DEFAULT 0,
    optional JSON NULL,
    porte TINYINT UNSIGNED NOT NULL DEFAULT 5,
    tipo_venditore ENUM('Concessionario', 'Privato') NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_autos_marca_modello (marca, modello),
    INDEX idx_autos_prezzo (prezzo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS utenti (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cognome VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    telefono VARCHAR(30) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_utenti_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
