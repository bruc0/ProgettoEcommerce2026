-- database.sql
CREATE DATABASE IF NOT EXISTS concessionario;
USE concessionario;

-- Tabella utenti
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabella auto (prodotti)
CREATE TABLE cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    make VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    body_type VARCHAR(50),
    registration_year INT,
    conditions VARCHAR(20),   -- nuovo, usato
    fuel VARCHAR(30),
    price DECIMAL(10,2) NOT NULL,
    location VARCHAR(100),
    mileage INT,
    transmission VARCHAR(20),
    power INT,                -- cavalli / kW
    seats_doors VARCHAR(50),
    color VARCHAR(30),
    description TEXT,
    image VARCHAR(255) DEFAULT 'default-car.jpg',
    is_sold BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabella carrello (solo utenti loggati)
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    car_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (user_id, car_id)
);

-- Tabella ordini (testata)
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Tabella dettagli ordine
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    car_id INT NOT NULL,
    price_at_purchase DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (car_id) REFERENCES cars(id)
);

-- Inserimento auto di esempio (20+ veicoli per testare filtri)
INSERT INTO cars (make, model, body_type, registration_year, conditions, fuel, price, location, mileage, transmission, power, seats_doors, color, description, image, is_sold) VALUES
('Fiat', '500', 'Utilitaria', 2020, 'usato', 'Benzina', 12500.00, 'Milano', 25000, 'Manuale', 85, '3 porte', 'Rosso', 'Fiat 500 in ottime condizioni', 'fiat500.jpg', 0),
('Volkswagen', 'Golf', 'Berlina', 2019, 'usato', 'Diesel', 17500.00, 'Roma', 45000, 'Automatico', 150, '5 porte', 'Grigio', 'Golf 7 2.0 TDI', 'golf.jpg', 0),
('Tesla', 'Model 3', 'Berlina', 2022, 'nuovo', 'Elettrico', 42000.00, 'Torino', 5000, 'Automatico', 350, '4 porte', 'Bianco', 'Autonomia 500km', 'tesla3.jpg', 0),
('Ford', 'Fiesta', 'Utilitaria', 2018, 'usato', 'Benzina', 9500.00, 'Napoli', 68000, 'Manuale', 95, '5 porte', 'Blu', 'Fiesta ST-Line', 'fiesta.jpg', 0),
('BMW', 'Serie 3', 'Berlina', 2021, 'usato', 'Diesel', 32000.00, 'Bologna', 32000, 'Automatico', 190, '4 porte', 'Nero', '330d xDrive', 'bmw3.jpg', 0),
('Audi', 'Q5', 'SUV', 2020, 'usato', 'Ibrido', 39000.00, 'Verona', 28000, 'Automatico', 265, '5 porte', 'Argento', 'Q5 TFSI e', 'audiQ5.jpg', 0),
('Renault', 'Clio', 'Utilitaria', 2017, 'usato', 'Benzina', 7800.00, 'Palermo', 72000, 'Manuale', 75, '5 porte', 'Verde', 'Clio Energy', 'clio.jpg', 0),
('Mercedes', 'Classe C', 'Berlina', 2019, 'usato', 'Diesel', 28900.00, 'Firenze', 41000, 'Automatico', 160, '4 porte', 'Grigio scuro', 'C 220 d', 'classeC.jpg', 0),
('Toyota', 'Yaris', 'Utilitaria', 2021, 'usato', 'Ibrido', 16500.00, 'Genova', 15000, 'Automatico', 116, '5 porte', 'Rossa', 'Yaris Hybrid', 'yaris.jpg', 0),
('Peugeot', '208', 'Utilitaria', 2022, 'nuovo', 'Elettrico', 28500.00, 'Bari', 3000, 'Automatico', 136, '5 porte', 'Nera', 'e-208 GT', 'peugeot208.jpg', 0),
('Opel', 'Corsa', 'Utilitaria', 2016, 'usato', 'Benzina', 6500.00, 'Catania', 98000, 'Manuale', 90, '3 porte', 'Giallo', 'Corsa 1.4', 'corsa.jpg', 0),
('Hyundai', 'Tucson', 'SUV', 2020, 'usato', 'Diesel', 24900.00, 'Padova', 37000, 'Automatico', 185, '5 porte', 'Blu scuro', 'Tucson 2.0 CRDi', 'tucson.jpg', 0),
('Kia', 'Sportage', 'SUV', 2019, 'usato', 'Benzina', 21900.00, 'Venezia', 49000, 'Manuale', 177, '5 porte', 'Grigio', 'Sportage GT-Line', 'sportage.jpg', 0),
('Volvo', 'XC60', 'SUV', 2021, 'usato', 'Ibrido', 45500.00, 'Modena', 18000, 'Automatico', 340, '5 porte', 'Bianco', 'T6 Twin Engine', 'xc60.jpg', 0),
('Mazda', 'CX-30', 'SUV', 2022, 'nuovo', 'Benzina', 27800.00, 'Parma', 2000, 'Manuale', 186, '5 porte', 'Rosso metallizzato', 'CX-30 Skyactiv-X', 'cx30.jpg', 0),
('Nissan', 'Qashqai', 'SUV', 2018, 'usato', 'Diesel', 16900.00, 'Treviso', 62000, 'Manuale', 130, '5 porte', 'Grigio', 'Qashqai 1.5 dCi', 'qashqai.jpg', 0),
('Fiat', 'Panda', 'Utilitaria', 2015, 'usato', 'Benzina', 4900.00, 'Messina', 110000, 'Manuale', 69, '5 porte', 'Bianco', 'Panda City Cross', 'panda.jpg', 0),
('Citroen', 'C3', 'Utilitaria', 2019, 'usato', 'Benzina', 9900.00, 'Livorno', 44000, 'Manuale', 83, '5 porte', 'Arancione', 'C3 PureTech', 'c3.jpg', 0),
('Land Rover', 'Range Rover Evoque', 'SUV', 2020, 'usato', 'Diesel', 39500.00, 'Firenze', 31000, 'Automatico', 180, '3 porte', 'Verde scuro', 'Evoque D180', 'evoque.jpg', 0),
('Porsche', 'Macan', 'SUV', 2021, 'usato', 'Benzina', 58900.00, 'Milano', 22000, 'Automatico', 265, '5 porte', 'Nero', 'Macan 2.0 Turbo', 'macan.jpg', 0);