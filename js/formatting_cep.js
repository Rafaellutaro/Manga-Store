var cep = document.getElementById('cep');

new Cleave(cep, {
    delimiters: ['-'],
    blocks: [5,3],
    numericOnly: true
});

function validar_cep(input){
    const value = input.value;
    if (value.length < 9 || isNaN(value.replace(/[^\d]/g, ''))){
        input.setCustomValidity("Insira um cep valido");
    } else {
        input.setCustomValidity("");
    }
}