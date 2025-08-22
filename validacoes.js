function validarFuncionario() {
    let nome = document.getElementById("nome_funcionario").value;
    let telefone = document.getElementById("telefone").value;
    let email = document.getElementById("email").value;
    let valor = email.target.value;
    const valorLimpo = valor.replace(/\D/g,'');

    let formatado = valorLimpo;

    // Tamanho mínimo do nome
    if (nome.length < 3) {
        alert("O nome do funcionário deve ter pelo menos 3 caracteres.");
        return false;
    }

    // Validando o telefone
    let regexTelefone = /^[0-9]{10,11}$/;
    if (!regexTelefone.test(telefone)) {
        alert("Digite um telefone válido (10 ou 11 dígitos).");
        return false;
    }

    if(valorLimpo.length <= 10) {
        formatado = valor.Limpo.replace(/^(\d{2}) (\d{4}) (\d{0,4})$/, '($1) $2-$3');
    }
    else {
        formatado = valorLimpo.replace(/^(\d{2}) (\d{5}) (\d{4})$/, '($1) $2-$3');
    }

    e.target.value = formatado;

    // Validando o Email
    let regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!regexEmail.test(email)) {
        alert("Digite um e-mail válido.");
        return false;
    }

    return true;

}