<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/user_profile.css">
    <title>Conta</title>
</head>

<body>

    <?php
    include_once "header.php";

    $user_id = $_SESSION['user_id'];

    include_once "connection.php";

    // pegar dados do usuario
    $stmt = $conn->prepare("SELECT * FROM llx_societe WHERE rowid = ?");
    $stmt->bind_param("i", $user_id); // 'i' represents an integer
    $stmt->execute();
    $result = $stmt->get_result();

    $user_data = $result->fetch_assoc();

    ?>

    <section id="user">
        <div class="user_box">
            <span>Acompanhe aqui todas as informaçoes sobre seu perfil.</span>

            <div class="user_label" id="contalabel">
                <a class="icon_u"><i class="fa-solid fa-user"></i></a>
                <div class="text_container">
                    <h3>Conta</h3>
                    <p>Suas informações cadastrais</p>
                </div>
            </div>

            <div class="user_label" id="enderecolabel">
                <a class="icon_u"><i class="fa-solid fa-location-dot"></i></a>
                <div class="text_container">
                    <h3>Endereços</h3>
                    <p>Endereços cadastrados</p>
                </div>
            </div>

            <div class="user_label">
                <a class="icon_u"><i class="fa-solid fa-box-open"></i></a>
                <div class="text_container">
                    <h3>Pedidos</h3>
                    <p>Confira aqui suas compras</p>
                </div>
            </div>

            <div class="user_label">
                <a class="icon_u"><i class="fa-solid fa-star"></i></a>
                <div class="text_container">
                    <h3>Avaliar Loja</h3>
                    <p>Faça aqui a sua avaliação</p>
                </div>
            </div>

            <div class="user_label">
                <a class="icon_u"><i class="fa-solid fa-arrow-right-arrow-left"></i></a>
                <div class="text_container">
                    <h3>Devoluções</h3>
                    <p>Histórico de devoluções</p>
                </div>
            </div>

            <div class="user_label">
                <a class="icon_u"><i class="fa-solid fa-heart"></i></a>
                <div class="text_container">
                    <h3>Lista de desejos</h3>
                    <p>Visite sua lista de desejos</p>
                </div>
            </div>

            <div class="user_label" id="sair_label">
                <a class="icon_u"><i class="fa-solid fa-heart-crack"></i></a>
                <div class="text_container">
                    <h3>Sair</h3>
                    <p>Deslogue de sua conta</p>
                </div>
            </div>

        </div>

        <div class="result" id="contadetails">
            <div class="result_container">

                <a class="icon_y"><i class="fa-solid fa-user"></i></a>
                <div class="text">
                    <h4>Conta</h4>
                    <h1>Nome</h1>
                    <a class="item"><?php echo $user_data["nom"] ?></a>

                    <h1>Telefone</h1>
                    <a class="item"><?php echo $user_data["phone"] ?></a>

                    <h1>CPF</h1>
                    <a class="item"><?php echo $user_data["idprof4"] ?></a>

                    <h1>Email</h1>
                    <a class="item"><?php echo $user_data["email"] ?></a>

                    <h1>Data de nascimento</h5>
                        <a class="item"><?php echo $user_data["idprof5"] ?></a>

                        <button type="submit" class="bt1">Alterar Dados</button>

                        <form action="email.php" method="post">
                            <button type="submit" class="bt2">Alterar Senha</button>
                        </form>
                </div>
            </div>

        </div>


        <div class="result" id="enderecodetails">
            <div class="result_container">

                <a class="icon_y"><i class="fa-solid fa-location-dot"></i></a>
                <div class="text">
                    <h4>Endereço</h4>
                    <h1>Rua</h1>
                    <a class="item"><?php echo $user_data["address"] ?></a>

                    <h1>Cidade</h1>
                    <a class="item"><?php echo $user_data["town"] ?> <?php echo $user_data["siren"] ?></a>

                    <h1>Local</h1>
                    <a class="item"><?php echo $user_data["fax"] ?></a>

                    <h1>Cep</h5>
                        <a class="item"><?php echo $user_data["zip"] ?></a>

                        <a href="change_address.php">
                            <button type="submit" class="bt1">Alterar Endereço</button>
                        </a>

                        <a href="add_new_address.php">
                            <button type="submit" class="bt1">Adicionar Endereço</button>
                        </a>
                </div>
            </div>

        </div>
    </section>


    <script src="js/profile.js"></script>
</body>

</html>