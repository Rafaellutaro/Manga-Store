// Function to perform address lookup
function lookupAddress() {

    const cepInput = document.getElementById('cep');
    const ruaInput = document.querySelector('.rua_input');
    const bairroInput = document.querySelector('.no_click[name="bairro"]');
    const cidadeInput = document.querySelector('.no_click[name="cidade"]');
    const estadoInput = document.querySelector('.no_click[name="estado"]');

    // Get the CEP value
    const cep = cepInput.value.replace(/\D/g, ''); //Remove letras do cep

    if (cep.length === 8) {
        // ViaCEP API endpoint
        const apiUrl = `https://viacep.com.br/ws/${cep}/json/`;

        // Fetch data from the API
        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                if (data.erro) {
                    alert('Cep não encontrado.');
                    return;
                } else {
                    // Update the UI with the address information
                    const { logradouro, bairro, localidade, uf } = data;

                    // Update the values of the existing inputs
                    ruaInput.value = logradouro;
                    bairroInput.value = bairro;
                    cidadeInput.value = localidade;
                    estadoInput.value = uf;
                }
            })
            .catch(error => {
                console.error('Erro ao procurar dados:', error);
                alert('Um erro ocorreu ao procurar o endereço. Tente novamente');
            });
    }
}

// criar um event listener para o cep
const cepInput = document.getElementById('cep');
cepInput.addEventListener('input', lookupAddress);
