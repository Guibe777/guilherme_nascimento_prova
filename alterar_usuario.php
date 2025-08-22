<?php
    session_start();
    require_once 'conexao.php';

    // Verifica se o usuário tem permissão de ADM
    if($_SESSION['perfil'] !=1) {
        echo "<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
        exit();
    }

    // Inicializa variáveis
    $usuario = null;
    
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(!empty($_POST['busca_usuario'])) {
            $busca = trim($_POST['busca_usuario']);

            // Verifica se a busca é um número (ID) ou um NOME
            if(is_numeric($busca)) {
                $sql = "SELECT * FROM usuario WHERE id_usuario = :busca";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
            }
            else {
                $sql = "SELECT * FROM usuario WHERE nome LIKE :busca_nome";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':busca_nome', "%$busca%", PDO::PARAM_STR);
            }

            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Se o USUÁRIO não for encontrado, exibe um alerta
            if(!$usuario) {
                echo "<script>alert('Usuário não encontrado!');</script>";
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
    <title>Alterar Usuário</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Certifique-se de que o JAVASCRIPT está sendo carregado corretamente -->
    <script src="scripts.js"></script>

    <style>
        button {
            margin: 10px;
            border-radius: 5px;
        }
    </style>

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

    <center><h2 style="transform: translateY(25px);">Alterar Usuário</h2></center>
    <form action="alterar_usuario.php" method="POST">
        <label for="busca_usuario">Digite o ID ou nome do usuário</label>
        <input type="text" id="busca_usuario" name="busca_usuario" required onkeyup="buscarSugestoes()">

        <!-- DIV para exibir sugestões de usuários -->
         <div id="sugestoes"></div>
        <button type="submit">Buscar</button>
    </form>

    <?php if($usuario): ?>
        <!-- Formulário para alterar usuário -->
        <form action="processa_alteracao_usuario.php" method="POST">
            <input type="hidden" name="id_usuario" value="<?=htmlspecialchars($usuario['id_usuario'])?>">

            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?=htmlspecialchars($usuario['nome'])?>" required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" value="<?=htmlspecialchars($usuario['email'])?>" required>

            <label for="id_perfil">Perfil:</label>
            <select id="id_perfil" name="id_perfil">
                <option value="1" <?=$usuario['id_perfil'] == 1 ?'select':''?>>Administrador</option>
                <option value="2" <?=$usuario['id_perfil'] == 2 ?'select':''?>>Secretária</option>
                <option value="3" <?=$usuario['id_perfil'] == 3 ?'select':''?>>Almoxarife</option>
                <option value="4" <?=$usuario['id_perfil'] == 4 ?'select':''?>>Cliente</option>
            </select>

            <!-- Se o usuário logado for ADM, exibir a opção de alterar senha -->
            <?php if($_SESSION['perfil'] == 1): ?>
                <label for="nova_senha">Nova senha</label>
                <input type="password" id="nova_senha" name="nova_senha">
            <?php endif; ?>

            <button type="submit">Alterar</button>
            <button type="reset">Cancelar</button>
        </form>
    <?php endif; ?>

    <!-- Botão de voltar -->
    <center><a href="principal.php" class="btn btn-primary mt-3">Voltar</a></center>
    
    <center><address style="transform: translateY(220px);">Guilherme do Nascimento</address></center>
</body>
</html>