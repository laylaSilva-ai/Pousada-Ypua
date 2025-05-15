create database pousadaypua;
use pousadaypua;

CREATE TABLE hospedes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    ddd VARCHAR(3) NOT NULL,
    telefone VARCHAR(10) NOT NULL,
    pedidos_especiais TEXT
);

CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hospede_id INT,
    checkin DATE NOT NULL,
    checkout DATE NOT NULL,
    noites INT NOT NULL,
    numero_hospedes INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    tipo_acomodacao VARCHAR(50) DEFAULT 'Domo',
    FOREIGN KEY (hospede_id) REFERENCES hospedes(id)
);

CREATE TABLE pagamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reserva_id INT,
    metodo_pagamento VARCHAR(50),
    status_pagamento VARCHAR(50),
    data_pagamento DATETIME,
    FOREIGN KEY (reserva_id) REFERENCES reservas(id)
);


CREATE DATABASE pousada;

USE pousada;

CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    ddd VARCHAR(5) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    pedidos_especiais TEXT,
    checkin DATE NOT NULL,
    checkout DATE NOT NULL,
    noites INT NOT NULL,
    numero_hospedes INT NOT NULL,
    criancas INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    data_reserva TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);




CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    ddd VARCHAR(3) NOT NULL,
    telefone VARCHAR(10) NOT NULL,
    pedidos_especiais TEXT,
    checkin DATE NOT NULL,
    checkout DATE NOT NULL,
    noites INT NOT NULL,
    numero_hospedes INT NOT NULL,
    criancas INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    data_reserva TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);