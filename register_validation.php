<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include 'connection.php';

// informação do usuario
$nome = $_POST['nome'];
$sobrenome = $_POST['sobrenome'];
$nome_completo = $nome . ' ' . $sobrenome;
$cpf = $_POST['cpf'];
$cep = $_POST['cep'];
$nascimento = $_POST['nascimento'];
$email = $_POST['email'];
$senha = $_POST['senha'];
$celular = $_POST['celular'];

// endereço
$rua = $_POST['rua'];
$bairro = $_POST['bairro'];
$cidade = $_POST['cidade'];
$estado = $_POST['estado'];
$local = $_POST['local'];
$numero_casa = $_POST['n_casa'];


// criptografar senha
$hashedPassword = password_hash($senha, PASSWORD_DEFAULT);
// formatar data de nascimento
list($day, $month, $year) = explode('/', $nascimento);


$checkCpfQuery = "SELECT COUNT(*) FROM llx_societe WHERE idprof4 = ?";
$stmt = $conn->prepare($checkCpfQuery);
$stmt->bind_param("s", $cpf);
$stmt->execute();
$stmt->bind_result($cpfCount);
$stmt->fetch();
$stmt->close();

$checkEmailQuery = "SELECT COUNT(*) FROM llx_societe WHERE email = ?";
$stmt = $conn->prepare($checkEmailQuery);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($emailCount);
$stmt->fetch();
$stmt->close();

$checkCelularQuery = "SELECT COUNT(*) FROM llx_societe WHERE phone = ?";
$stmt = $conn->prepare($checkCelularQuery);
$stmt->bind_param("s", $celular);
$stmt->execute();
$stmt->bind_result($celularCount);
$stmt->fetch();
$stmt->close();

$fk_user = 1;

$errors = array();

// verificar data de nascimento
if ($day < 1 || $day > 31) {
    $errors['nascimento'] = "dia invalido";
}

if ($month < 1 || $month > 12) {
    $errors['nascimento'] = "mês invalido";
}

$ano_atual = date("Y");
$ano_minimo = 1900;

if ($year < $ano_minimo || $year > $ano_atual) {
    $errors['nascimento'] = "ano invalido";
}

$formatted_nascimento = "$year/$month/$day";

// Validate the first name (nome)
if (!preg_match("/^[a-zA-ZÀ-ú\s']+$/", $nome)) {
    $errors['nome'] = "Nome Invalido";
}

// Validate the last name (sobrenome)
if (!preg_match("/^[a-zA-ZÀ-ú\s']+$/", $sobrenome)) {
    $errors['sobrenome'] = "Sobrenome Invalido";
}

if (!preg_match("/^[a-zA-Z0-9À-ú\s']+@[a-zA-Z0-9À-ú\s']+\\.[a-zA-ZÀ-ú\s']+$/", $email)) {
    $errors['email'] = "Email Invalido";
}

if (!preg_match("/^[a-zA-ZÀ-ú\s']+$/", $local)) {
    $errors['local'] = "Local Invalido";
}

if ($cpfCount > 0) {
    $errors['cpf'] = "CPF Já Existe";
}

if ($celularCount > 0) {
    $errors['celular'] = "Celular Já Existe";
}

if ($emailCount > 0) {
    $errors['email'] = "Email Já Existe";
}


// Fetch the highest user code and generate the next code
$userCodePrefix = 'CU2409-';
$userCodeLength = 5; // Number of digits in the user code

// Get the highest user code
$maxCodeQuery = "SELECT code_client FROM llx_societe ORDER BY code_client DESC LIMIT 1";
$result = $conn->query($maxCodeQuery);

$newUserCode = $userCodePrefix . '00001'; // Default starting code
if ($result && $row = $result->fetch_assoc()) {
    $highestCode = $row['code_client'];
    $numberPart = (int)substr($highestCode, strlen($userCodePrefix));
    $newNumberPart = str_pad($numberPart + 1, $userCodeLength, '0', STR_PAD_LEFT);
    $newUserCode = $userCodePrefix . $newNumberPart;
}


// Check for any errors
if (count($errors) === 0) {
    $_SESSION['userData'] = [
        'nome_completo' => $nome_completo,
        'cpf' => $cpf,
        'nascimento' => $formatted_nascimento,
        'email' => $email,
        'senha' => $hashedPassword,
        'celular' => $celular,
        'cep' => $cep,
        'rua' => $rua,
        'bairro' => $bairro,
        'cidade' => $cidade,
        'estado' => $estado,
        'local' => $local,
        'numero_casa' => $numero_casa,
        'newUserCode' => $newUserCode
    ];

    include_once "email.php";

    SendVerificationCode($email);
}else{
    $_SESSION['errors'] = $errors; 
    header("Location: user_register.php"); 
    exit();
}