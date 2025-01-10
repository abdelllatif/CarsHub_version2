CREATE DATABASE Carshub;

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


CREATE TABLE Themes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    theme_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    media_path VARCHAR(255),
    approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES clients(id),
    FOREIGN KEY (theme_id) REFERENCES Themes(id)
);

CREATE TABLE Tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE ArticleTags (
    article_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (article_id, tag_id),
    FOREIGN KEY (article_id) REFERENCES Articles(id),
    FOREIGN KEY (tag_id) REFERENCES Tags(id)
);

CREATE TABLE Comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES Articles(id),
    FOREIGN KEY (user_id) REFERENCES clients(id)
);

CREATE TABLE Favorites (
    user_id INT NOT NULL,
    article_id INT NOT NULL,
    PRIMARY KEY (user_id, article_id),
    FOREIGN KEY (user_id) REFERENCES clients(id),
    FOREIGN KEY (article_id) REFERENCES Articles(id)
);









INSERT INTO Themes (name, description) VALUES
('Écologie', 'Articles liés à l\'écologie et aux pratiques durables'),
('Économies', 'Articles sur les moyens d\'économiser de l\'argent');

-- Insertion de tags
INSERT INTO Tags (name) VALUES
('hybride'),
('électrique'),
('carburant');

-- Insertion d'utilisateurs
INSERT INTO Users (username, email, password, role) VALUES
('client1', 'client1@example.com', 'password1', 'client'),
('admin', 'admin@example.com', 'adminpassword', 'admin');

INSERT INTO Articles (user_id, theme_id, title, content, approved) VALUES
(1, 1, 'L\'Éco-conduite: Économisez du Carburant', 'Les meilleures pratiques pour une conduite économique et écologique.', TRUE);