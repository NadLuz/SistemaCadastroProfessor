<?php
// Inicia a sessão PHP
session_start();

// Inclui o arquivo de configuração com as credenciais do banco de dados
require_once 'config.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header("Location: login.php");
    exit();
}

// Verifica se o ID da turma foi fornecido via POST
if (!isset($_POST['turma_id'])) {
    // Se não foi fornecido, redireciona para a página principal
    header("Location: principal.php");
    exit();
}

// Obtém o ID da turma do POST
$turma_id = $_POST['turma_id'];

// Conecta ao banco de dados
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verifica se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se a turma tem atividades cadastradas
$stmt = $conn->prepare("SELECT COUNT(*) FROM Atividade WHERE turma_id = ?");
$stmt->bind_param("i", $turma_id);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

// Se a turma tem atividades, não permite a exclusão
if ($count > 0) {
    $_SESSION['erro'] = "Você não pode excluir uma turma com atividades cadastradas";
    header("Location: principal.php");
    exit();
}

// Prepara a query para excluir a turma
$stmt = $conn->prepare("DELETE FROM Turma WHERE id = ? AND professor_id = ?");
$stmt->bind_param("ii", $turma_id, $_SESSION['user_id']);
$stmt->execute();

// Verifica se a turma foi excluída com sucesso
if ($stmt->affected_rows > 0) {
    $_SESSION['sucesso'] = "Turma excluída com sucesso";
} else {
    $_SESSION['erro'] = "Não foi possível excluir a turma";
}

// Fecha a declaração e a conexão com o banco de dados
$stmt->close();
$conn->close();

// Redireciona para a página principal
header("Location: principal.php");
exit();
?>