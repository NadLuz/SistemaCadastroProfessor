<?php
session_start();
require_once 'config.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verifica se o ID da turma foi fornecido
if (!isset($_GET['turma_id'])) {
    header("Location: principal.php");
    exit();
}

$turma_id = $_GET['turma_id'];

// Conecta ao banco de dados
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se a turma pertence ao professor logado
$stmt = $conn->prepare("SELECT nome FROM Turma WHERE id = ? AND professor_id = ?");
$stmt->bind_param("ii", $turma_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: principal.php");
    exit();
}

$turma = $result->fetch_assoc();
$nome_turma = $turma['nome'];
$stmt->close();

$mensagem = '';

// Processa o formulário de cadastro de atividade
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descricao = trim($_POST['descricao']);
    
    if (empty($descricao)) {
        $mensagem = "Por favor, informe a descrição da atividade.";
    } else {
        $stmt = $conn->prepare("INSERT INTO Atividade (descricao, turma_id) VALUES (?, ?)");
        $stmt->bind_param("si", $descricao, $turma_id);
        
        if ($stmt->execute()) {
            $mensagem = "Atividade cadastrada com sucesso!";
        } else {
            $mensagem = "Erro ao cadastrar a atividade: " . $conn->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Atividade</title>
    <style>
        /* Estilos CSS para a página de cadastro de atividade */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2, h4 {
            color: #333;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        .btn-primary {
            background-color: #2196F3;
            color: white;
        }
        .btn-secondary {
            background-color: #f4f4f4;
            color: #333;
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
    <div class="container">
        <h2>Cadastrar Nova Atividade</h2>
        <h4>Turma: <?php echo htmlspecialchars($nome_turma); ?></h4>
        
        <?php if (!empty($mensagem)): ?>
            <div class="alert <?php echo strpos($mensagem, 'sucesso') !== false ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div>
                <label for="descricao">Descrição da Atividade</label>
                <input type="text" id="descricao" name="descricao" required>
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar Atividade</button>
        </form>
        
        <a href="atividades_turma.php?id=<?php echo $turma_id; ?>" class="btn btn-secondary">Voltar para Lista de Atividades</a>
    </div>
</body>
</html>