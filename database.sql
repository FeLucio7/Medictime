CREATE DATABASE IF NOT EXISTS medictime_db;

USE medictime_db;

CREATE TABLE IF NOT EXISTS usuarios (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    data_nascimento DATE,
    celular VARCHAR(20),
    email VARCHAR(100),
    usuario VARCHAR(100),
    senha VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS medicamento (
    id_medicamento INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    nome_medicamento VARCHAR(100),
    horario TIME,
    dosagem VARCHAR(100),
    observacao VARCHAR(255),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_user)
);