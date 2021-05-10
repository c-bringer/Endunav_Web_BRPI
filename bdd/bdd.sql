-- Créer la base de données
CREATE DATABASE endunav CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Sélectionner la base de données
USE endunav;

-- Créer la table users
CREATE TABLE users
(
    id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    name VARCHAR(50),
    email VARCHAR(80),
    password VARCHAR(255),
    status INT(1),
    admin INT(1)
)