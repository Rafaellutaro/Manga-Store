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
            <div class="user_label" id="contalabel">
                <a class="icon_u"><i class="fa-solid fa-user"></i></a>
                <div class="text_container">
                    <h3> Minha Conta</h3>
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
                <a class="icon_u"><i class="fa-solid fa-right-from-bracket"></i></a>
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
                    <h4>Minha Conta</h4>
                    <hr>

                    <div class="info-grid">
                        <div class="info-group">
                            <h1>Nome</h1>
                            <a class="item"><?php echo $user_data["nom"] ?></a>
                        </div>

                        <div class="info-group">
                            <h1>Telefone</h1>
                            <a class="item"><?php echo $user_data["phone"] ?></a>
                        </div>

                        <div class="info-group">
                            <h1>CPF</h1>
                            <a class="item"><?php echo $user_data["idprof4"] ?></a>
                        </div>

                        <div class="info-group">
                            <h1>Email</h1>
                            <a class="item"><?php echo $user_data["email"] ?></a>
                        </div>

                        <div class="info-group">
                            <h1>Data de nascimento</h1>
                            <a class="item"><?php echo $user_data["idprof5"] ?></a>
                        </div>
                    </div>

                    <div class="button-group">
                        <button type="submit" class="btn primary-btn">Alterar Dados</button>
                        <form action="email.php" method="post" style="display: inline-flex;">
                            <button type="submit" class="btn secondary-btn">Alterar Senha</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="result" id="boughtProductsDetails">
            <div class="result_container">
                <a class="icon_y"><i class="fa-solid fa-box-open"></i></a>
                <div class="sub_container">
                    <div class="text">
                        <h4>Historico de compras</h4>
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
                                $fk_statut = setStatus($fk_statut);
                                $delivery_address = $row["delivery_address"];

                                $sql_product = "SELECT llx_commandedet.fk_product
                                FROM llx_commandedet
                                WHERE llx_commandedet.fk_commande = $commandeId
                                LIMIT 1";
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
                                        // $img = "http://$dbhost/img/" . $row_product_final["filepath"] . "/" . $row_product_final["filename"];
                                        $img = "https://" . $_SERVER['HTTP_HOST'] . "/img/" . $row_product_final["filepath"] . "/" . $row_product_final["filename"];
                                        $url = $row_product_final["url"];

                                        echo " <form action='orderDetails.php' method='post' class='product-entry-container' onclick = 'this.submit()'>";
                                        echo "<input type='hidden' name='commandeId' value='$commandeId'>";
                                        echo "<input type='hidden' name='fk_statut' value='$fk_statut'>";
                                        echo "  <div class='product-entry'>";
                                        echo "    <a href='sproduct.php?url=$url'>";
                                        echo "      <img src='" . $img . "' alt='Product Image' class='product-image'>";
                                        echo "    </a>";
                                        echo "    <div class='product-details'>";
                                        echo "      <div>ID: $ref</div>";
                                        echo "      <b><div>Status do pedido: $fk_statut</div></b>";
                                        echo "      <div>Dia da compra: $date_commande</div>";
                                        echo "      <div>Endereço de entrega: $delivery_address</div>";
                                        echo "    </div>";
                                        echo "  </div>";
                                        echo "</form>";
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
                    <h4>Endereços</h4>
                    <hr>
                    <div class="info-grid">
                        <div class="info-group">
                            <h1>Endereço</h1>
                            <a class="item"><?php echo $user_data["address"] ?></a>
                        </div>

                        <div class="info-group">
                            <h1>Cidade</h1>
                            <a class="item"><?php echo $user_data["town"] ?> <?php echo $user_data["siren"] ?></a>
                        </div>

                        <div class="info-group">
                            <h1>Complemento</h1>
                            <a class="item"><?php echo $user_data["fax"] ?></a>
                        </div>

                        <div class="info-group">
                            <h1>CEP</h1> <a class="item"><?php echo $user_data["zip"] ?></a>
                        </div>
                    </div>

                    <div class="button-group">
                        <a href="change_address.php">
                            <button type="button" class="btn primary-btn">Alterar Endereço</button>
                        </a>
                        <a href="add_new_address.php">
                            <button type="button" class="btn secondary-btn">Adicionar Endereço</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <script src="js/profile.js"></script>
</body>

</html>