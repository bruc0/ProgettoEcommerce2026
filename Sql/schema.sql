

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

CREATE TABLE IF NOT EXISTS auto_images (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    auto_id INT UNSIGNED NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    is_cover TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_auto_images_auto FOREIGN KEY (auto_id) REFERENCES autos(id) ON DELETE CASCADE,
    INDEX idx_auto_images_auto_id (auto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS utenti (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cognome VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    reset_token_hash VARCHAR(64) NULL,
    reset_expires_at DATETIME NULL,
    telefono VARCHAR(30) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_utenti_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_user (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_admin_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cart (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    car_id INT UNSIGNED NOT NULL,
    added_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cart_user FOREIGN KEY (user_id) REFERENCES utenti(id) ON DELETE CASCADE,
    CONSTRAINT fk_cart_auto FOREIGN KEY (car_id) REFERENCES autos(id) ON DELETE CASCADE,
    UNIQUE KEY uq_cart_item (user_id, car_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    order_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES utenti(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    car_id INT UNSIGNED NOT NULL,
    price_at_purchase DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_order_items_auto FOREIGN KEY (car_id) REFERENCES autos(id) ON DELETE RESTRICT,
    UNIQUE KEY uq_order_item_auto (car_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Gli admin iniziali bruc0, trencio e lello vengono creati automaticamente
-- da Core/AdminManager.php al primo login con password iniziale: admin123.
