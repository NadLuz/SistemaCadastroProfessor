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

// Variável para armazenar mensagens de feedback para o usuário
$mensagem = '';

// Verifica se o formulário foi submetido via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Remove espaços em branco do início e fim do nome da turma
    $nome_turma = trim($_POST['nome_turma']);
    // Obtém o ID do professor da sessão
    $professor_id = $_SESSION['user_id'];

    // Verifica se o nome da turma não está vazio
    if (!empty($nome_turma)) {
        // Conecta ao banco de dados
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // Verifica se a conexão foi bem-sucedida
        if ($conn->connect_error) {
            die("Conexão falhou: " . $conn->connect_error);
        }

        // Prepara a query SQL para inserir a nova turma
        $stmt = $conn->prepare("INSERT INTO Turma (nome, professor_id) VALUES (?, ?)");
        // Vincula os parâmetros à query
        $stmt->bind_param("si", $nome_turma, $professor_id);

        // Executa a query e verifica se foi bem-sucedida
        if ($stmt->execute()) {
            $mensagem = "Turma cadastrada com sucesso!";
        } else {
            $mensagem = "Erro ao cadastrar a turma: " . $conn->error;
        }

        // Fecha a declaração e a conexão com o banco de dados
        $stmt->close();
        $conn->close();
    } else {
        $mensagem = "Por favor, informe o nome da turma.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Turma</title>
    <style>
        /* Estilos CSS para a página de cadastro de turma */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .navbar {
            background-color: #2196F3;
            color: white;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar-brand {
            font-size: 1.2em;
        }
        .btn-sair {
            background-color: transparent;
            border: 1px solid white;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-primary {
            background-color: #2196F3;
            color: white;
        }
        .btn-secondary {
            background-color: #f4f4f4;
            color: #333;
            text-decoration: none;
            display: inline-block;
            margin-left: 10px;
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #dff0d8;
            border-color: #d0e9c6;
            color: #3c763d;
        }
        .alert-danger {
            background-color: #f2dede;
            border-color: #ebcccc;
            color: #a94442;
        }
    </style>
</head>
<body>
    <!-- Barra de navegação superior -->
    <nav class="navbar">
        <span class="navbar-brand">Nome do Professor: <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        <a href="logout.php" class="btn-sair">Sair</a>
    </nav>

    <div class="container">
        <h2>Cadastro de Nova Turma</h2>
        <?php
        // Exibe mensagens de feedback para o usuário
        if (!empty($mensagem)) {
            $alertClass = strpos($mensagem, 'sucesso') !== false ? 'alert-success' : 'alert-danger';
            echo "<div class='alert $alertClass' role='alert'>$mensagem</div>";
        }
        ?>
        <!-- Formulário de cadastro de turma -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="nome_turma">Nome da Turma</label>
                <input type="text" id="nome_turma" name="nome_turma" required>
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
            <a href="principal.php" class="btn btn-secondary">Voltar</a>
        </form>
    </div>
</body>
</html>