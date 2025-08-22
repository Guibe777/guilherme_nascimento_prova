<?php 
    session_start();
    require_once 'conexao.php';

    // Verificando se o usuário é ADM
    if($_SESSION['perfil'] != 1) {
        echo "<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
        exit();
    }

    // Pegando as informações do funcionário
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $id_funcionario = $_POST['id_funcionario'];
        $nome = $_POST['nome_funcionario'];
        $email = $_POST['email'];
        $endereco = $_POST['endereco'];
        $telefone = $_POST['telefone'];

        // Atualiza os dados do funcionário
            $sql = "UPDATE funcionario SET nome_funcionario = :nome_funcionario, email = :email, endereco = :endereco,
            telefone = :telefone
            WHERE id_funcionario = :id";
            $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':nome_funcionario', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':endereco', $endereco);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':id', $id_funcionario);

        // Mensagens
        if($stmt->execute()) {
            echo "<script>alert('Funcionário atualizado com sucesso!');window.location.href='buscar_funcionario.php';</script>";
        }
        else {
            echo "<script>alert('Erro ao atualizar funcionário!');window.location.href='alterar_funcionario.php?id=$id_funcionario';</script>";
        }
    }
?>