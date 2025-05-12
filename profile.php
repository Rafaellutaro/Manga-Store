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

    // pegar dados do produto
    $sql = "SELECT delivery_address, fk_statut, date_commande, fk_soc, ref, rowid
        FROM llx_commande
        WHERE fk_soc = $user_id
        ORDER BY date_commande DESC";

    $boughtMangas = $conn->query($sql);

    // function to set the status of the product

    function setStatus($status)
    {
        switch ($status) {
            case 0:
                return  "Pagamento Pendente";
            case 1:
                return "Pagamento Confirmado";
            case 2:
                return "Em Preparação";
            case 3:
                return "Enviado";
            case 4:
                return "Entregue";
            default:
                return "Desconhecido";
        }
    }

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

            <div class="user_label" id="boughtProductsLabel">
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

        <div class="result" id="boughtProductsDetails">
            <div class="result_container">
                <a class="icon_y"><i class="fa-solid fa-user"></i></a>
                <div class="sub_container">
                    <div class="text">
                        <h4>Produtos Comprados</h4>
                        <div class="text_bought">
                            <?php
                            if ($boughtMangas->num_rows <= 0) {
                                echo "<h1>Você ainda não comprou nada</h1>";
                            }

                            while ($row = $boughtMangas->fetch_assoc()) {
                                $commandeId = $row["rowid"];
                                $ref = $row["ref"];
                                $date_commande = $row["date_commande"];
                                $fk_soc = $row["fk_soc"];
                                $fk_statut = $row["fk_statut"];
                                $delivery_address = $row["delivery_address"];

                                $sql_product = "SELECT llx_commandedet.fk_product
                                FROM llx_commandedet
                                WHERE llx_commandedet.fk_commande = $commandeId";
                                $result_product = $conn->query($sql_product);

                                while ($row_product = $result_product->fetch_assoc()) {
                                    // id de todos os produtos comprados
                                    $fk_product = $row_product["fk_product"];

                                    $sql_product_final = "SELECT llx_product.*, llx_ecm_files.filepath, llx_ecm_files.filename
                                        FROM llx_product
                                        JOIN llx_ecm_files ON llx_product.rowid = llx_ecm_files.src_object_id
                                        WHERE llx_product.rowid = $fk_product";
                                    $result_product_final = $conn->query($sql_product_final);

                                    while ($row_product_final = $result_product_final->fetch_assoc()) {
                                        //$img = "http://$dbhost/img/" . $row_product_final["filepath"] . "/" . $row_product_final["filename"];
                                        $img = "https://" . $_SERVER['HTTP_HOST'] . "/img/" . $row_product_final["filepath"] . "/" . $row_product_final["filename"];
                                        $url = $row_product_final["url"];

                                        echo "<div class='product-entry-container' onclick=\"window.location.href='#?id=$commandeId'\">";
                                        echo "  <div class='product-entry'>";
                                        echo "    <a href='sproduct.php?url=$url' onclick=\"event.stopPropagation();\">";
                                        echo "      <img src='" . $img . "' alt='Product Image' class='product-image'>";
                                        echo "    </a>";
                                        echo "    <div class='product-details'>";
                                        echo "      <div>ID: $ref</div>";
                                        echo "      <div>Status do pedido: " . setStatus($fk_statut) . "</div>";
                                        echo "      <div>Dia da compra: $date_commande</div>";
                                        echo "      <div>Endereço de entrega: $delivery_address</div>";
                                        echo "    </div>";
                                        echo "  </div>";
                                        echo "</div>";
                                    }
                                }
                            }
                            ?>
                        </div>
                    </div>
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