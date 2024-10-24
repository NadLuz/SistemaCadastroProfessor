<?php
session_start();
require_once 'config.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verifica se o ID da turma foi fornecido
if (!isset($_GET['id'])) {
    header("Location: principal.php");
    exit();
}

$turma_id = $_GET['id'];

// Conecta ao banco de dados
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verifica se houve erro na conexão com o banco de dados
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Busca o nome da turma e verifica se pertence ao professor logado
$stmt = $conn->prepare("SELECT nome FROM Turma WHERE id = ? AND professor_id = ?");
$stmt->bind_param("ii", $turma_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

// Verifica se a turma pertence ao professor logado
if ($result->num_rows === 0) {
    header("Location: principal.php");
    exit();
}

$turma = $result->fetch_assoc();
$nome_turma = $turma['nome'];
$stmt->close();

// Busca as atividades da turma
$stmt = $conn->prepare("SELECT id, descricao FROM Atividade WHERE turma_id = ? ORDER BY id");
$stmt->bind_param("i", $turma_id);
$stmt->execute();
$result = $stmt->get_result();
$atividades = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atividades da Turma</title>
    <style>
        /* Estilos CSS para a página de atividades da turma */
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
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
        }
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background-color: #2196F3;
            color: white;
        }
        .btn-secondary {
            background-color: #f4f4f4;
            color: #333;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
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
        <!-- Cabeçalho da página com o nome da turma e botão para cadastrar nova atividade -->
        <div class="d-flex justify-content-between align-items-center">
            <h2>Turma: <?php echo htmlspecialchars($nome_turma); ?></h2>
            <a href="cadastrar_atividade.php?turma_id=<?php echo $turma_id; ?>" class="btn btn-primary">Cadastrar Atividade</a>
        </div>

        <!-- Verifica se há atividades cadastradas e exibe a lista ou uma mensagem de aviso -->
        <?php if (empty($atividades)): ?>
            <p>Nenhuma atividade cadastrada para esta turma.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Nome</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($atividades as $atividade): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($atividade['id']); ?></td>
                        <td><?php echo htmlspecialchars($atividade['descricao']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Botão de "Voltar" posicionado abaixo da lista -->
        <a href="principal.php" class="btn btn-secondary">Voltar</a>
    </div>
</body>
</html>