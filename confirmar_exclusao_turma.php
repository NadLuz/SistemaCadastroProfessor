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

// Verifica se o ID da turma foi fornecido na URL
if (!isset($_GET['id'])) {
    // Se não foi fornecido, redireciona para a página principal
    header("Location: principal.php");
    exit();
}

// Obtém o ID da turma da URL
$turma_id = $_GET['id'];

// Conecta ao banco de dados
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verifica se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Prepara a query para verificar se a turma pertence ao professor logado
$stmt = $conn->prepare("SELECT nome FROM Turma WHERE id = ? AND professor_id = ?");
$stmt->bind_param("ii", $turma_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

// Se a turma não foi encontrada ou não pertence ao professor, redireciona para a página principal
if ($result->num_rows === 0) {
    header("Location: principal.php");
    exit();
}

// Obtém o nome da turma
$turma = $result->fetch_assoc();

// Fecha a declaração e a conexão com o banco de dados
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Exclusão de Turma</title>
    <style>
        /* Estilos CSS para a página de confirmação de exclusão */
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
        p {
            margin-bottom: 20px;
        }
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-danger {
            background-color: #f44336;
            color: white;
        }
        .btn-secondary {
            background-color: #f4f4f4;
            color: #333;
            margin-left: 10px;
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
        <h2>Confirmar Exclusão</h2>
        <p>Você tem certeza que deseja excluir a turma "<?php echo htmlspecialchars($turma['nome']); ?>"?</p>
        <!-- Formulário de confirmação de exclusão -->
        <form action="excluir_turma.php" method="POST">
            <input type="hidden" name="turma_id" value="<?php echo $turma_id; ?>">
            <button type="submit" class="btn btn-danger">Sim, excluir</button>
            <a href="principal.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>