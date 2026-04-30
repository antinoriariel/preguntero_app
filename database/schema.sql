CREATE DATABASE IF NOT EXISTS preguntero_app
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE preguntero_app;

SET NAMES utf8mb4;
SET time_zone = '+00:00';

DROP TABLE IF EXISTS respuestas;
DROP TABLE IF EXISTS preguntas;
DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
    dni_usuario CHAR(8) NOT NULL,
    username VARCHAR(50) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (dni_usuario),
    UNIQUE KEY uk_usuarios_username (username),
    UNIQUE KEY uk_usuarios_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE preguntas (
    id_pregunta INT UNSIGNED NOT NULL AUTO_INCREMENT,
    enunciado VARCHAR(500) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_pregunta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE respuestas (
    id_respuesta INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_pregunta INT UNSIGNED NOT NULL,
    respuesta VARCHAR(255) NOT NULL,
    es_correcta TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_respuesta),
    KEY idx_respuestas_id_pregunta (id_pregunta),
    KEY idx_respuestas_correcta (id_pregunta, es_correcta),
    CONSTRAINT fk_respuestas_preguntas
        FOREIGN KEY (id_pregunta)
        REFERENCES preguntas (id_pregunta)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;