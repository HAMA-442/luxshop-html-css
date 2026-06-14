-- =============================================
-- Base de données : LuxShop
-- =============================================

CREATE DATABASE IF NOT EXISTS luxshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE luxshop;

-- Table utilisateurs
CREATE TABLE IF NOT EXISTS utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('client','admin') DEFAULT 'client',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table catégories
CREATE TABLE IF NOT EXISTS categorie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    icone VARCHAR(10) DEFAULT '🛍️'
);

-- Table articles
CREATE TABLE IF NOT EXISTS article (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,3) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    image VARCHAR(255) DEFAULT 'default.jpg',
    categorie_id INT,
    badge VARCHAR(50) DEFAULT NULL,
    FOREIGN KEY (categorie_id) REFERENCES categorie(id)
);

-- =============================================
-- Données : Catégories
-- =============================================
INSERT INTO categorie (nom, icone) VALUES
('Montres & Bijoux', '⌚'),
('Parfums', '🌸'),
('Maroquinerie', '👜'),
('Électronique', '💻'),
('Mode', '👗'),
('Art de vivre', '🏡');

-- =============================================
-- Données : Utilisateurs (mot de passe = "password123" hashé)
-- =============================================
INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) VALUES
('Admin', 'LuxShop', 'admin@luxshop.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Ben Salem', 'Aymen', 'aymen@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client'),
('Trabelsi', 'Sarra', 'sarra@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client');

-- =============================================
-- Données : Articles (12 produits riches)
-- =============================================
INSERT INTO article (name, description, price, stock_quantity, image, categorie_id, badge) VALUES

-- Montres & Bijoux
('Montre Automatique Éclipse', 
 'Montre automatique pour homme avec cadran saphir, boîtier en acier inoxydable 316L et bracelet cuir véritable. Étanchéité 50m. Mouvement visible à travers le fond transparent.', 
 1290.000, 8, 'montre1.jpg', 1, 'Bestseller'),

('Bracelet Or Rose Infinity', 
 'Bracelet délicat en plaqué or rose 18 carats avec motif infini serti de zircons cubiques. Longueur ajustable 16-20cm. Livré dans un écrin cadeau premium.', 
 185.000, 25, 'bracelet1.jpg', 1, NULL),

-- Parfums
('Parfum Oud Royal 100ml', 
 'Fragrance orientale envoûtante autour du bois d\'oud, de la rose de Taïf et des épices chaudes. Tenue exceptionnelle de 12h+. Flacon en cristal taillé, coffret luxe inclus.', 
 320.000, 15, 'parfum1.jpg', 2, 'Exclusif'),

('Eau de Parfum Jasmin Blanc 50ml', 
 'Notes florales légères de jasmin tunisien, musc blanc et bois de santal. Parfum féminin intemporel, idéal pour toutes les occasions. Vaporisateur de précision.', 
 145.000, 30, 'parfum2.jpg', 2, NULL),

-- Maroquinerie
('Sac à Main Cuir Milano', 
 'Sac structuré en cuir de veau pleine fleur, doublure en suède, fermeture dorée magnétique. Intérieur organisé : 3 compartiments, porte-monnaie amovible. Fabriqué en Italie.', 
 890.000, 5, 'sac1.jpg', 3, 'Luxe'),

('Portefeuille Slim Carbon', 
 'Portefeuille ultra-fin en cuir grainé avec protection RFID intégrée. 8 emplacements cartes, 2 soufflets billets, fenêtre ID. Design minimaliste disponible en noir et cognac.', 
 95.000, 40, 'portefeuille1.jpg', 3, NULL),

-- Électronique
('Casque Audio ANC Pro X', 
 'Casque sans fil à réduction de bruit active hybride (40dB). Autonomie 35h, charge rapide USB-C (10min = 3h). Coussinets mémoire de forme, arceau aluminium. Compatible Bluetooth 5.3.', 
 749.000, 12, 'casque1.jpg', 4, 'Nouveau'),

('Montre Connectée FitLife Ultra', 
 'Smartwatch AMOLED 1.8\'\', GPS intégré, mesure SpO2/FC/ECG, 100+ modes sport. Résistance 5ATM. Appels Bluetooth, notifications, paiement sans contact. Autonomie 7 jours.', 
 599.000, 18, 'smartwatch1.jpg', 4, NULL),

-- Mode
('Veste en Cuir Moto Heritage', 
 'Veste en cuir de buffle épaisseur 1.2mm, coupe ajustée, doublure viscose, protections épaules et coudes certifiées CE. Poches intérieures sécurisées. Tailles S à XXL.', 
 1150.000, 7, 'veste1.jpg', 5, 'Tendance'),

('Foulard Soie Pure Imprimé', 
 'Foulard 100% soie de Chine, impression digitale haute définition motif géométrique, ourlet roulotté main. Dimensions 90x90cm. Peut se porter en carré, triangle ou bandeau.', 
 220.000, 22, 'foulard1.jpg', 5, NULL),

-- Art de vivre
('Diffuseur Huiles Essentielles Bambou', 
 'Diffuseur ultrasonique en bambou naturel et céramique. 400ml, 7 couleurs LED, minuterie 1h/3h/6h, arrêt automatique. Silencieux (< 25dB). Inclut 3 huiles essentielles bio 10ml.', 
 135.000, 35, 'diffuseur1.jpg', 6, NULL),

('Carafe Filtrante Design Nordique', 
 'Carafe filtrante 1.5L en verre borosilicate et bouchon liège naturel. Filtre au charbon actif inclus (dure 60L). Élimine chlore, calcaire et impuretés. Design primé, lave-vaisselle safe.', 
 89.000, 50, 'carafe1.jpg', 6, 'Éco');
