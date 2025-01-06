-- Create the database
CREATE DATABASE Carshub;

-- Use the newly created database
USE Carshub;

CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('client','admin') DEFAULT 'client',
    firstName VARCHAR(100) NOT NULL,
    lastName VARCHAR(100) NOT NULL,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP
    phone VARCHAR(15)
);


CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT
);

CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    model VARCHAR(100) NOT NULL,
    brand VARCHAR(100) NOT NULL,
    description TEXT,
    pricePerDay FLOAT NOT NULL,
    status ENUM('available', 'unavailable') DEFAULT 'available',
    categoryId INT,
    characteristics JSON, 
    image VARCHAR(255),
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoryId) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicleId INT NOT NULL,
    clientId INT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5), 
    comment TEXT,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicleId) REFERENCES vehicles(id) ON DELETE CASCADE,
    FOREIGN KEY (clientId) REFERENCES clients(id) ON DELETE CASCADE
);

CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customerId INT NOT NULL,
    vehicleId INT NOT NULL,
    startDate DATETIME NOT NULL,
    endDate DATETIME NOT NULL,
    pickupLocation VARCHAR(255) NOT NULL,
    dropoffLocation VARCHAR(255) NOT NULL,
    totalPrice FLOAT NOT NULL,
    status ENUM('refused', 'confirmed', 'canceled','en Attend') DEFAULT 'en Attend',
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customerId) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicleId) REFERENCES vehicles(id) ON DELETE CASCADE
);
INSERT INTO clients (email, password, role, firstName, lastName, phone) 
VALUES ('haissouneabdellatif749@gmail.com', '$2y$10$CtMTcUHN32iZz0f/F.ueS.eHqSxz8t7B9knw7p004vq9Eh5DsefJS
', 'admin', 'Abdellatif', 'Hissoune', '1234567890');
ALTER TABLE clients ADD COLUMN archived BOOLEAN DEFAULT FALSE;