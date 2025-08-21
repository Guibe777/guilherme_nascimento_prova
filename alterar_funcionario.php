<?php
    session_start();
    require_once 'conexao.php';

    // Verifica se o usuário tem permissão de ADM
    if($_SESSION['perfil'] !=1) {
        echo "<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
        exit();
    }

    // Inicializa variáveis
    $funcionario = null;
    
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(!empty($_POST['busca_funcionario'])) {
            $busca = trim($_POST['busca_funcionario']);

            // Verifica se a busca é um número (ID) ou um NOME
            if(is_numeric($busca)) {
                $sql = "SELECT * FROM funcionario WHERE id_funcionario = :busca";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
            }
            else {
                $sql = "SELECT * FROM funcionario WHERE nome_funcionario LIKE :busca_nome_funcionario";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':busca_nome_funcionario', "$busca%", PDO::PARAM_STR);
            }

            $stmt->execute();
            $funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Se o FUNCIONÁRIO não for encontrado, exibe um alerta
            if(!$funcionario) {
                echo "<script>alert('Funcionário não encontrado!');</script>";
            }
        }
    }

// Menu
// Obtendo o nome do perfil do usuário logado
$id_perfil = $_SESSION['perfil'];
$sqlPerfil = "SELECT nome_perfil FROM perfil WHERE id_perfil = :id_perfil";
$stmtPerfil = $pdo->prepare($sqlPerfil);
$stmtPerfil->bindParam(':id_perfil',$id_perfil);
$stmtPerfil->execute();
$perfil = $stmtPerfil->fetch(PDO::FETCH_ASSOC);
$nomePerfil = $perfil['nome_perfil'];

// Definição das permissões por perfil
$permissoes = [
    1 => ["Cadastrar" => ["cadastro_usuario.php", "cadastro_perfil.php", "cadastro_cliente.php",
                           "cadastro_fornecedor.php", "cadastro_produto.php", "cadastro_funcionario.php"],

          "Buscar" => ["buscar_usuario.php", "buscar_perfil.php", "buscar_cliente.php",
                       "buscar_fornecedor.php", "buscar_produto.php", "buscar_funcionario.php"],

          "Alterar" => ["alterar_usuario.php", "alterar_perfil.php", "alterar_cliente.php",
                       "alterar_fornecedor.php", "alterar_produto.php", "alterar_funcionario.php"],

          "Excluir" => ["excluir_usuario.php", "excluir_perfil.php", "excluir_cliente.php",
                       "excluir_fornecedor.php", "excluir_produto.php", "excluir_funcionario.php"]],


    2 => ["Cadastrar" => ["cadastro_cliente.php"],

          "Buscar" => ["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],

          "Alterar" => ["alterar_fornecedor.php", "alterar_produto.php"],

          "Excluir" => ["excluir_produto.php"]],


    3 => ["Cadastrar" => ["cadastro_fornecedor.php", "cadastro_produto.php"],

          "Buscar" => ["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],

          "Alterar" => ["alterar_fornecedor.php", "alterar_produto.php"],

          "Excluir" => ["excluir_produto.php"]],


    4 => ["Cadastrar" => ["cadastro_cliente.php"],

          "Buscar" => ["buscar_produto.php"],

          "Alterar" => ["alterar_cliente.php"]],
];

// Obtendo as opções disponíveis para o perfil logado
$opcoes_menu = $permissoes["$id_perfil"];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Funcionário</title>
    <link rel="stylesheet" href="styles.css">

    <!-- Certifique-se de que o JAVASCRIPT está sendo carregado corretamente -->
    <script src="scripts_func.js"></script>
</head>
<body>

<!-- Menu -->
    <nav>
        <ul class="menu">
            <?php foreach($opcoes_menu as $categoria => $arquivos): ?>
                <li class="dropdown">
                    <a href="#"> <?=$categoria ?></a>
                    <ul class="dropdown-menu">
                        <?php foreach($arquivos as $arquivo): ?>
                            <li>
                                <a href=" <?=$arquivo ?>">
                                <?=ucfirst(str_replace("_"," ",basename($arquivo,".php"))); ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <h2>Alterar Funcionário</h2>
    <form action="alterar_funcionario.php" method="POST">
        <label for="busca_funcionario">Digite o ID ou nome do funcionário</label>
        <input type="text" id="busca_funcionario" name="busca_funcionario" required onkeyup="buscarSugestoes()">

        <!-- DIV para exibir sugestões de funcionários -->
         <div id="sugestoes"></div>
        <button type="submit">Buscar</button>
    </form>

    <?php if($funcionario): ?>
        <!-- Formulário para alterar funcionário -->
        <form action="processa_alteracao_funcionario.php" method="POST">
            <input type="hidden" name="id_funcionario" value="<?=htmlspecialchars($funcionario['id_funcionario'])?>">

            <label for="nome_funcionario">Nome:</label>
            <input type="text" id="nome" name="nome_funcionario" value="<?=htmlspecialchars($funcionario['nome_funcionario'])?>" required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" value="<?=htmlspecialchars($funcionario['email'])?>" required>

            <label for="endereco">Endereço:</label>
            <input type="text" id="endereco" name="endereco" value="<?=htmlspecialchars($funcionario['endereco'])?>" required>

            <label for="telefone">Telefone:</label>
            <input type="tel" id="telefone" name="telefone" value="<?=htmlspecialchars($funcionario['telefone'])?>" required>

            <button type="submit">Alterar</button>
            <button type="reset">Cancelar</button>
        </form>
    <?php endif; ?>
    <a href="principal.php" style="color: white; border: none; border-radius: 5px; padding: 10px; background-color: #007bff; font-size: 16px; text-decoration: none; /* remove o sublinhado */">Voltar</a>
    <center><address style="transform: translateY(220px);">Guilherme do Nascimento</address></center>
</body>
</html>