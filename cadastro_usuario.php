<?php 
    session_start();
    require_once 'conexao.php';

    // Verifica se o usuário tem permissão supondo que o perfil 1 seja o ADMIN
    if($_SESSION['perfil'] != 1) {
        echo "Acesso Negado!";
    }

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $id_perfil = $_POST['id_perfil'];

        $sql = "INSERT INTO usuario(nome, email, senha, id_perfil) VALUES(:nome, :email, :senha, :id_perfil)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':id_perfil', $id_perfil);

        if($stmt->execute()) {
            echo "<script> alert('Usuário cadastrado com sucesso!'); </script>";
        }
        else {
            echo "<script> alert('Erro ao cadastrar usuário! :('); </script>";
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
    <title>Cadastrar Usuário</title>
    <link rel="stylesheet" href="styles.css">
    <script src="validacoes.js"></script>
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

    <h2>Cadastrar Usuário</h2>
    <form action="cadastro_usuario.php" method="POST">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>

        <label for="id_perfil">Perfil</label>
        <select id="id_perfil" name="id_perfil" required>
            <option value="1">Administrador</option>
            <option value="2">Secretária</option>
            <option value="3">Almoxarife</option>
            <option value="4">Cliente</option>
        </select>

        <button type="submit">Salvar</button>
        <button type="reset">Cancelar</button>
    </form>

    <a href="principal.php" style="color: white; border: none; border-radius: 5px; padding: 10px; background-color: #007bff; font-size: 16px; text-decoration: none; /* remove o sublinhado */ transform: translateY(50px);">Voltar</a>
    <center><address style="transform: translateY(30px);">Guilherme do Nascimento</address></center>
</body>
</html>