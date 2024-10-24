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

// Conecta ao banco de dados
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verifica se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Obtém o ID do professor da sessão
$professor_id = $_SESSION['user_id'];

// Prepara a query SQL para buscar as turmas do professor
$sql = "SELECT id_turmas, nome FROM turmas WHERE professor = ?";
$stmt = $conn->prepare($sql);

// Vincula o parâmetro à query
$stmt->bind_param("i", $professor);

// Executa a query
$stmt->execute();

// Obtém o resultado da query
$result = $stmt->get_result();

// Armazena todas as turmas em um array associativo
$turmas = $result->fetch_all(MYSQLI_ASSOC);

// Fecha a declaração e a conexão com o banco de dados
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Tela Principal do Professor</title>
    <style>
       
    </style>
</head>
<body>
    <!-- Barra de navegação superior -->
    <nav class="navbar">
        <span class="navbar-brand">Nome do Professor: <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        <a href="logout.php" class="btn-sair">Sair</a>
    </nav>

    <div class="container">
        <!-- Exibição de mensagens de sucesso ou erro -->
        <?php
        if (isset($_SESSION['sucesso'])) {
            echo "<div class='alert alert-success'>" . $_SESSION['sucesso'] . "</div>";
            unset($_SESSION['sucesso']);
        }
        if (isset($_SESSION['erro'])) {
            echo "<div class='alert alert-danger'>" . $_SESSION['erro'] . "</div>";
            unset($_SESSION['erro']);
        }
        ?>

        <!-- Botão para cadastrar nova turma -->
        <a href="cadastrar_turma.php" class="btn-cadastrar">Cadastrar turma</a>

        <!-- Título da seção de turmas -->
        <h2>Turmas</h2>

        <?php if (empty($turmas)): ?>
            <p>Você ainda não possui turmas cadastradas.</p>
        <?php else: ?>
            <!-- Tabela de turmas -->
            <table>
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Nome</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($turmas as $turma): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($turma['id_turmas']); ?></td>
                        <td><?php echo htmlspecialchars($turma['nome']); ?></td>
                        <td>
                            <!-- Botões de ação para cada turma -->
                            <a href="confirmar_exclusao_turma.php?id=<?php echo $turma['id_turmas']; ?>" class="btn-acao btn-excluir">Excluir</a>
                            <a href="atividades_turma.php?id=<?php echo $turma['id_turmas']; ?>" class="btn-acao btn-visualizar">Visualizar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>