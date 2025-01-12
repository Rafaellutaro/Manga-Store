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

$data_atual = date("Y-m-d H:i:s");
$fk_user = 1;

$sql = "INSERT INTO llx_societe (nom, idprof4, idprof5, email, idprof6, phone, client, datec, fk_user_creat, fk_user_modif, code_client) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

// sql statement
$stmt = $conn->prepare($sql);

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
    // If there are no validation errors, proceed with inserting data into the database

    if (!$stmt) {
        die("Error: " . $conn->error);
    }

    $client = 1;
    // bind os parametros e executa
    $stmt->bind_param("ssssssisiis", $nome_completo, $cpf, $formatted_nascimento, $email, $hashedPassword, $celular, $client, $data_atual, $fk_user, $fk_user, $newUserCode);

    if ($stmt->execute()) {
        // Get the ID of the newly inserted row
        $user_id = $stmt->insert_id;

        // Prepare the complete address
        $endereco_completo = $rua . ", " . $numero_casa . " - " . $bairro;

        // Update the same user with the address and other information
        $endereco_query = "UPDATE llx_societe SET address = ?, zip = ?, town = ?, siren = ?, fax = ? WHERE rowid = ?";
        $stmt_endereco = $conn->prepare($endereco_query);
        $stmt_endereco->bind_param("sssssi", $endereco_completo, $cep, $cidade, $estado, $local, $user_id);

        if ($stmt_endereco->execute()) {
            header("location: success_register.php");
        } else {
            echo "Erro ao inserir o endereço: " . $stmt_endereco->error;
        }

        $stmt_endereco->close();

        $stmt->close();
        $conn->close();
    } else {
        echo "Error: " . $stmt->error;
    }
}else{
    $_SESSION['errors'] = $errors; 
    header("Location: user_register.php"); 
    exit();
}
?>
