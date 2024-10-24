CREATE DATABASE saep_db2;
USE saep_db;
CREATE TABLE Professor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);
CREATE TABLE Turma (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    professor_id INT,
    FOREIGN KEY (professor_id) REFERENCES Professor(id)
);

-- Tabela Atividade
CREATE TABLE Atividade (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descricao VARCHAR(255) NOT NULL,
    data DATE NOT NULL,
    turma_id INT,
    FOREIGN KEY (turma_id) REFERENCES Turma(id)
);


-- Inserindo registros na tabela Professor
INSERT INTO Professor (nome, email, senha) VALUES
('Olivia Souza', 'olivia@email.com', '123'),
('Maiara Lima', 'maiara@email.com', '123'),
('Eric Dias', 'eric@email.com', '123');

-- Inserindo registros na tabela Turma
INSERT INTO Turma (nome, professor_id) VALUES
('Técnico DS', 1),
('Informática básica', 2),
('Programação JAVA', 3);

-- Inserindo registros na tabela Atividade
INSERT INTO Atividade (descricao, data, turma_id) VALUES
('Atividade de recuperação', '2024-07-01', 1),
('Treinamento em planilhas', '2024-07-02', 1),
('Simulação de Sistema', '2024-07-03', 2);

-- Adicionando a restrição de exclusão para turmas com atividades
DELIMITER $$
CREATE TRIGGER before_delete_turma
BEFORE DELETE ON Turma
FOR EACH ROW
BEGIN
    IF EXISTS (SELECT 1 FROM Atividade WHERE turma_id = OLD.id) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Você não pode excluir uma turma com atividades cadastradas';
    END IF;
END$$
DELIMITER ;