<?php
// Inicia a sessão PHP, permitindo o uso de variáveis de sessão
session_start();

// Inclui o arquivo de configuração que contém as constantes de conexão com o banco de dados
require_once 'config.php';

// Verifica se o formulário foi submetido via método POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtém os dados do formulário
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Estabelece uma conexão com o banco de dados usando as constantes definidas em config.php
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verifica se houve erro na conexão
    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }

    // Prepara uma consulta SQL parametrizada para evitar injeção de SQL
    $stmt = $conn->prepare("SELECT id, nome FROM Professor WHERE email = ? AND senha = ?");
    
    // Vincula os parâmetros à consulta preparada
    $stmt->bind_param("ss", $email, $senha);
    
    // Executa a consulta
    $stmt->execute();
    
    // Obtém o resultado da consulta
    $result = $stmt->get_result();

    // Verifica se exatamente uma linha foi retornada (autenticação bem-sucedida)
    if ($result->num_rows == 1) {
        // Autenticação bem-sucedida
        $professor = $result->fetch_assoc();
        
        // Armazena informações do usuário na sessão
        $_SESSION['user_id'] = $professor['id'];
        $_SESSION['user_name'] = $professor['nome'];
        
        // Redireciona para a página principal
        header("Location: principal.php");
        exit();
    } else {
        // Autenticação falhou
        // Armazena uma mensagem de erro na sessão
        $_SESSION['login_error'] = "E-mail ou senha incorretos.";
        
        // Redireciona de volta para a página de login
        header("Location: login.php");
        exit();
    }

    // Fecha a declaração preparada
    $stmt->close();
    
    // Fecha a conexão com o banco de dados
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login - Sistema de Gerenciamento de Turmas</title>    
</head>
<body>
    <div class="login-container">
        <h2>Bem-vindo</h2>
        <?php
        // Verifica se existe uma mensagem de erro na sessão
        if (isset($_SESSION['login_error'])) {
            // Exibe a mensagem de erro
            echo '<div class="alert">' . $_SESSION['login_error'] . '</div>';
            // Remove a mensagem de erro da sessão para não exibi-la novamente em futuros acessos
            unset($_SESSION['login_error']);
        }
        ?>
        <!-- Formulário de login -->
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <button type="submit" class="btn-login">Entrar</button>
        </form>
    </div>
</body>
</html>