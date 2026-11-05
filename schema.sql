CREATE DATABASE quiz_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE quiz_db;

CREATE TABLE jogadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nick VARCHAR(20) NOT NULL UNIQUE,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pontuacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jogador_id INT NOT NULL,
    quiz ENUM(
        'basico',
        'intermediario',
        'avancado'
    ) NOT NULL,
    pontos INT NOT NULL DEFAULT 0,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY jogador_quiz (jogador_id, quiz),
    CONSTRAINT fk_jogador FOREIGN KEY (jogador_id) REFERENCES jogadores (id) ON DELETE CASCADE
);