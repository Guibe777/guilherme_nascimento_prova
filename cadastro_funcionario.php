<?php 
    session_start();
    require_once 'conexao.php';

    // Verifica se o usuário tem permissão supondo que o perfil 1 seja o ADMIN
    if($_SESSION['perfil'] != 1) {
        echo "Acesso Negado!";
    }

    // Pegando as variáveis via método POST
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome = $_POST['nome_funcionario'];
        $email = $_POST['email'];
        $endereco = $_POST['endereco'];
        $telefone = $_POST['telefone'];

        // Aqui falando onde deve mudar as informações
        $sql = "INSERT INTO funcionario(nome_funcionario, email, endereco, telefone) VALUES(:nome_funcionario, :email, :endereco, :telefone)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome_funcionario', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':endereco', $endereco);
        $stmt->bindParam(':telefone', $telefone);

        // O que deve falar se o funcionário for cadastrado ou se der erro
        if($stmt->execute()) {
            echo "<script> alert('Funcionário cadastrado com sucesso!'); </script>";
        }
        else {
            echo "<script> alert('Erro ao cadastrar funcionário! :('); </script>";
        }
    }

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
    <title>Cadastrar Funcionário</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="validacoes.js"></script>

    <!-- Deixando os botões mais bonitinhos -->
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

    <!-- Formulário de cadastro -->
    <center><h2 style="transform: translateY(20px);">Cadastrar Funcionário</h2></center>
    <form action="cadastro_funcionario.php" method="POST" onsubmit="return validarFuncionario()">
        <label for="nome_funcionario">Nome:</label>
        <input type="text" id="nome_funcionario" name="nome_funcionario" oninput="this.value=this.value.replace(/[^a-zA-ZÀ-ÿ\s]/g,'')" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="endereco">Endereço:</label>
        <input type="text" id="endereco" name="endereco" required>

        <label for="telefone">Telefone:</label>
        <input type="tel" id="telefone" name="telefone" maxlength="15" required>

        <!-- Botões -->
        <button type="submit">Salvar</button>
        <button type="reset">Cancelar</button>
    </form>

    <!-- Botão de voltar -->
    <center><a href="principal.php" class="btn btn-primary mt-3" style="transform: translateY(-40px);">Voltar</a></center>
    <center><address style="transform: translateY(30px);">Guilherme do Nascimento</address></center>
</body>
</html>