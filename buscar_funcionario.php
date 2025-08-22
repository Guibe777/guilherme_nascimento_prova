<?php
    session_start();
    require_once 'conexao.php';

    // VERIFICA SE O USUÁRIO TEM PERMISSÃO DE ADM OU SECRETARIA
    if($_SESSION['perfil']!=1 && $_SESSION['perfil']!=2){
        echo "<script>alert('Acesso negado!');window.location.href='principal.php';</script>";
        exit();
    } 
    $funcionario = []; // INICIALIZA A VARIAVEL PARA EVITAR ERROS

    // SE O FORMULARIO FOR ENVIADO, BUSCA O FUNCIONÁRIO PELO ID OU NOME
    if($_SERVER["REQUEST_METHOD"]== "POST" && !empty($_POST['busca'])){
        $busca = trim($_POST['busca']);

        // VERIFICA SE A BUSCA É UM NUMERO OU UM NOME.
        if(is_numeric($busca)){
            $sql="SELECT * FROM funcionario WHERE id_funcionario = :busca ORDER BY nome_funcionario ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':busca',$busca, PDO::PARAM_INT);
        } else {
            $sql = "SELECT * FROM funcionario WHERE nome_funcionario LIKE :busca_nome_funcionario ORDER BY nome_funcionario ASC";
        
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':busca_nome_funcionario', "$busca%", PDO::PARAM_STR);
        }
    } else {
        $sql="SELECT * FROM funcionario ORDER BY nome_funcionario ASC";
        $stmt = $pdo->prepare($sql);
    }
$stmt->execute();
$funcionarios = $stmt->fetchALL(PDO::FETCH_ASSOC);

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
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Buscar Funcionário</title>

    <!-- Deixando os botões bonitinhos -->
    <style>
        table {
            max-width: 90%;
            margin-left: 5%;
            border: 10px solid;
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

    <!-- Campo de busca do funcionário -->
    <center><h2>Lista de Funcionários</h2></center>

    <form action="buscar_funcionario.php" method="POST">
        <label for="busca">Digite o ID ou NOME</label>
        <input type="text" id="busca" name="busca">
        <button type="submit">Pesquisar</button>
    </form>

    <!-- Botão de voltar -->
    <center><a href="principal.php" class="btn btn-primary mt-3" style="transform: translateY(-40px);">Voltar</a></center>
    
    <!-- Tabela com informações dos funcionários -->
        <?php if(!empty($funcionarios)): ?>
            <table class="table table-sm table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Endereço</th>
                    <th>Telefone</th>
                    <th>Ações</th>
                </tr>

            <!-- Pegando as informações de cada funcionário -->
            <?php foreach($funcionarios as $funcionario): ?>

                <tr>
                    <td><?=htmlspecialchars($funcionario['id_funcionario'])?></td>
                    <td><?=htmlspecialchars($funcionario['nome_funcionario'])?></td>
                    <td><?=htmlspecialchars($funcionario['email'])?></td>
                    <td><?=htmlspecialchars($funcionario['endereco'])?></td>
                    <td><?=htmlspecialchars($funcionario['telefone'])?></td>
                    <td>
                        <!-- Botões de excluir e alterar -->
                        <a class="btn btn-success" href="alterar_funcionario.php?id=<?=htmlspecialchars($funcionario['id_funcionario'])?>">Alterar</a>
                        <a class="btn btn-danger" href="excluir_funcionario.php?id=<?=htmlspecialchars($funcionario['id_funcionario'])?>"
                        onclick="return confirm('Tem certeza que deseja excluir este funcionário?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach;?>    
            </table>
            <?php else:?>
                <p>Nenhum funcionário encontrado.</p>
            <?php endif;?>

            <center><address>Guilherme do Nascimento</address></center>
</body>
</html>