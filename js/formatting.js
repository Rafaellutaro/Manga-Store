var cpf = document.getElementById('cpf');
var celular = document.getElementById('cel');
var cep = document.getElementById('cep');
var mail = document.getElementById('email');
var nascimento = document.getElementById('nascimento');
var numero_casa = document.getElementById('n_casa');
var errorElement = document.getElementById('email-error'); 

errorElement.style.display = 'none';

new Cleave(cpf, {
    delimiters: ['.', '.', '-'],
    blocks: [3, 3, 3, 2],
    numericOnly: true
});

new Cleave(celular, {
    delimiters: ['(', ') ', '-', ''],
    blocks: [0, 2 , 5, 4],
    numericOnly: true
});

new Cleave(cep, {
    delimiters: ['-'],
    blocks: [5,3],
    numericOnly: true
});

new Cleave(nascimento, {
    delimiters: ['/', '/'],
    blocks: [2, 2, 4],
    numericOnly: true
});

new Cleave(numero_casa, {
    blocks: [10],
    numericOnly: true
});

function validar_cpf(input){
    const value = input.value;
    if (value.length < 14 || isNaN(value.replace(/[^\d]/g, ''))){
        input.setCustomValidity("Insira um cpf valido");
    } else {
        input.setCustomValidity("");
    }
}

function validar_cel(input){
    const value = input.value;
    if (value.length < 15 || isNaN(value.replace(/[^\d]/g, ''))){
        input.setCustomValidity("Insira um número valido");
    } else {
        input.setCustomValidity("");
    }
}

function validar_cep(input){
    const value = input.value;
    if (value.length < 9 || isNaN(value.replace(/[^\d]/g, ''))){
        input.setCustomValidity("Insira um cep valido");
    } else {
        input.setCustomValidity("");
    }
}

function validar_nascimento(input){
    const value = input.value;
    if (value.length < 10 || isNaN(value.replace(/[^\d]/g, ''))){
        input.setCustomValidity("Insira uma data de nascimento valida");
    } else {
        input.setCustomValidity("");
    }
}

function validar_box(input){
    let value = input.value;
        let newValue = '';

        if (value === '' || value === '0') {
            newValue = '1';
        } else {
            for (let i = 0; i < value.length; i++) {
                if (!isNaN(value[i]) && value[i] !== ' ') {
                    newValue += value[i];
                }
            }
        }

        input.value = newValue;
    }

mail.addEventListener('input', function () {
    var email = mail.value;
    var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    var errorText = "Email Inválido"; //

    if (!emailRegex.test(email)) {
        // formato do email invalido
        errorElement.textContent = errorText;
        errorElement.style.display = 'block'; // mensagem de erro

    } else {
        // formato do email correto, esconder mensagen
        errorElement.style.display = 'none';
    }
});

