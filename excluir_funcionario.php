<?php
session_start();
require 'conexao.php';

// VERIFICA SE O USUARIO TEM PERMISSÃO DE ADM OU SECRETARIA
if($_SESSION['perfil']!=1){
    echo "<script>alert('Acesso negado!');window.location.href='principal.php';</script>";
    exit();
} 

// Inicializa variavel para armazenar funcionários
$funcionarios = [];

// Buscar todos os funcionários cadastrados em ordem alfabetica
$sql="SELECT * FROM funcionario ORDER BY nome_funcionario ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$funcionarios = $stmt->fetchALL(PDO::FETCH_ASSOC);
    
// Se um id for passado via GET exclui o funcionário
if(isset($_GET['id']) && is_numeric($_GET['id'])){
    $id_funcionario = $_GET['id'];

    // Exclui o funcionário do banco de dados
    $sql = "DELETE FROM funcionario WHERE id_funcionario = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id',$id_funcionario,PDO::PARAM_INT);

    if($stmt->execute()){
        echo "<script>alert('Funcionário excluido com sucesso!');window.location.href='excluir_funcionario.php';</script>";
    } else{
        echo "<script>alert('Erro ao excluir o funcionário!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Funcionário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilização da página -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #000 !important;
        }
        .navbar-brand, .nav-link {
            color: #white !important;
        }
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .table {
            margin-top: 20px;
        }
        .table th {
            background-color: #000;
            color: white;
        }
        .container {
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        h2 {
            color: #000;
            margin-bottom: 20px;
        }
    </style>

</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Sistema</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="principal.php">Principal</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Tabela com informações dos funcionários e botão de exlcuir -->
    <div class="container">
        <h2>Excluir Funcionário</h2>
        
        <?php if(!empty($funcionarios)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Endereço</th>
                            <th>Telefone</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>

                    <!-- Pegando as informações dos funcionários -->
                        <?php foreach($funcionarios as $funcionario): ?>
                            <tr>
                                <td><?= htmlspecialchars($funcionario['id_funcionario'])?></td>
                                <td><?= htmlspecialchars($funcionario['nome_funcionario'])?></td>
                                <td><?= htmlspecialchars($funcionario['email'])?></td>
                                <td><?= htmlspecialchars($funcionario['endereco'])?></td>
                                <td><?= htmlspecialchars($funcionario['telefone'])?></td>
                                <td>
                                    <!-- Botão de excluir -->
                                    <a href="excluir_funcionario.php?id=<?= htmlspecialchars($funcionario['id_funcionario'])?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Tem certeza que deseja excluir este funcionário?')">
                                        Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Nenhum funcionário encontrado</div>
        <?php endif; ?>

        <!-- Botão de voltar -->
        <a href="principal.php" class="btn btn-primary mt-3">Voltar</a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <center><address style="transform: translateY(20px);">Guilherme do Nascimento</address></center>
</body>
</html>