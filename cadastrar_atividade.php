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
    <link rel="stylesheet" href="style.css">
    <title>Cadastrar Atividade</title>
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