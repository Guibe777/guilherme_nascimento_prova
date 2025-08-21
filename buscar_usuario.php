<?php
    session_start();
    require_once 'conexao.php';

    // VERIFICA SE O USUARIO TEM PERMISSÃO DE ADM OU SECRETARIA
    if($_SESSION['perfil']!=1 && $_SESSION['perfil']!=2){
        echo "<script>alert('Acesso negado!');window.location.href='principal.php';</script>";
        exit();
    } 
    $usuario = []; // INICIALIZA A VARIAVEL PARA EVITAR ERROS

    // SE O FORMULARIO FOR ENVIADO, BUSCA O USUARIO PELO ID OU NOME
    if($_SERVER["REQUEST_METHOD"]== "POST" && !empty($_POST['busca'])){
        $busca = trim($_POST['busca']);

        // VERIFICA SE A BUSCA É UM NUMERO OU UM NOME.
        if(is_numeric($busca)){
            $sql="SELECT * FROM usuario WHERE id_usuario = :busca ORDER BY nome ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':busca',$busca, PDO::PARAM_INT);
        } else {
            $sql = "SELECT * FROM usuario WHERE nome LIKE :busca_nome ORDER BY nome ASC";
        
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':busca_nome', "$busca%", PDO::PARAM_STR);
        }
    } else {
        $sql="SELECT * FROM usuario ORDER BY nome ASC";
        $stmt = $pdo->prepare($sql);
    }
$stmt->execute();
$usuarios = $stmt->fetchALL(PDO::FETCH_ASSOC);

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
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Buscar Usuario</title>
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

    <center><h2>Lista de Usuario</h2></center>

    <form action="buscar_usuario.php" method="POST">
        <label for="busca">Digite o ID ou NOME</label>
        <input type="text" id="busca" name="busca">
        <button type="submit">Pesquisar</button>
    </form>

    <a href="principal.php" class="voltar" style="color: white; border: none; border-radius: 5px; padding: 10px; background-color: #007bff; font-size: 16px; text-decoration: none; /* remove o sublinhado */;">Voltar</a>

        <?php if(!empty($usuarios)): ?>
            <table class="table table-dark table-striped">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Perfil</th>
                    <th>Ações</th>
                </tr>

            <?php foreach($usuarios as $usuario): ?>

                <tr>
                    <td><?=htmlspecialchars($usuario['id_usuario'])?></td>
                    <td><?=htmlspecialchars($usuario['nome'])?></td>
                    <td><?=htmlspecialchars($usuario['email'])?></td>
                    <td><?=htmlspecialchars($usuario['id_perfil'])?></td>
                    <td>
                        <a href="alterar_usuario.php?id=<?=htmlspecialchars($usuario['id_usuario'])?>">Alterar</a>
                        <a href="excluir_usuario.php?id=<?=htmlspecialchars($usuario['id_usuario'])?>"
                        onclick="return confirm('Tem certeza que deseja excluir este usuario?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach;?>    
            </table>
            <?php else:?>
                <p>Nenhum usuario encontrado.</p>
            <?php endif;?>
            <center><address>Guilherme do Nascimento</address></center>
</body>
</html>