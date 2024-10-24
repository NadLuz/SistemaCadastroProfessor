<?php
// Inicia a sessão PHP
session_start();

// Verifica se o usuário já está autenticado
if (!isset($_SESSION['user_id'])) {
    // Redireciona para login.php se o usuário não estiver autenticado
    header("Location: login.php");
    exit();
}

// Se o usuário estiver autenticado, redireciona para a página principal
header("Location: principal.php");
exit();
?>