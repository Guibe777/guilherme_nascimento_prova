<?php 
    session_start();
    require_once 'conexao.php';

    if($_SESSION['perfil'] != 1) {
        echo "<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
        exit();
    }

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $id_funcionario = $_POST['id_funcionario'];
        $nome = $_POST['nome_funcionario'];
        $email = $_POST['email'];
       // $id_perfil = $_POST['id_perfil'];
       // $nova_senha = !empty($_POST['nova_senha'])? password_hash($_POST['nova_senha'],PASSWORD_DEFAULT): null;

        // Atualiza os dados do usuário
        /* if($nova_senha) {
            $sql = "UPDATE funcionario SET nome_funcionario=:nome_funcionario,email=:email
            WHERE id_funcionario = :id ";
            $stmt = $pdo->prepare($sql);
           // $stmt->bindParam(':senha', $nova_senha);
        } */
        if ($id_funcionario) {
            $sql = "UPDATE funcionario SET nome_funcionario = :nome_funcionario, email = :email
            WHERE id_funcionario = :id";
            $stmt = $pdo->prepare($sql);
        }
        $stmt->bindParam(':nome_funcionario', $nome);
        $stmt->bindParam(':email', $email);
        // $stmt->bindParam(':id_perfil', $id_perfil);
        $stmt->bindParam(':id_funcionario', $id_funcionario);

        if($stmt->execute()) {
            echo "<script>alert('Funcionário atualizado com sucesso!');window.location.href='buscar_funcionario.php';</script>";
        }
        else {
            echo "<script>alert('Erro ao atualizar funcionário!');window.location.href='alterar_funcionario.php?id=$id_funcionario';</script>";
        }
    }
?>