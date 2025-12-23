/* 


CREATE DATABASE IF NOT EXISTS login_cadastro;
USE login_cadastro;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    sobrenome VARCHAR(100) NOT NULL,
    nascimento DATE NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    genero ENUM('Feminino','Masculino','Outro') NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


*/ 