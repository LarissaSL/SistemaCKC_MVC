<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- google fontes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

    <script src="https://unpkg.com/@phosphor-icons/web"></script> <!-- ONDE PEGUEI OS ICON TEMPORARIOS 'phosphor-icons' -->
    <script defer src="/views/Js/nav.js"></script> <!-- O atributo "defer" serve para que o script roda depois do html -->

    <link rel="stylesheet" href="/sistemackc/views/Css/variaveis.css">
    <link rel="stylesheet" href="/sistemackc/views/Css/alterarSenha.css">

    <title>Senha</title>
</head>

<body>
    <?php
    if (!isset($_SESSION)) {
        session_start();
    }
    ?>

    <header class="header">
        <nav class="nav">
            <a class="logo" href="/sistemackc/"><img src="/sistemackc/views/Img/ImgSistema/logoCKC.png" alt="logo do CKC"></a>

            <button class="hamburger"></button>
            <ul class="nav-list">
                <?php
                if (isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'Comum' || !isset($_SESSION['tipo'])) {
                    echo "<li><a href='#'>História</a></li>";
                ?>
                    <li class="drop-down">
                        <a href="#" class="dropdown-toggle">Corridas<i class="ph ph-caret-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Etapas</a></li>
                            <li><a href="#">Classificação</a></li>
                            <li><a href="/sistemackc/kartodromo">Kartódromos</a></li>
                        </ul>
                    </li>
                <?php } elseif (isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'Administrador') {
                    echo "<li><a href='/sistemackc/admtm85/usuario'>Usuarios</a></li>";
                    echo "<li><a href='#'>Corridas</a></li>";
                    echo "<li><a href='/sistemackc/admtm85/kartodromo'>Kartodromos</a></li>";
                    echo "<li><a href='#'>Resultados</a></li>";
                } ?>

                <?php
                if (isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'Comum') { ?>
                    <li class="drop-down">
                        <?php echo "<a href='#' class='dropdown-toggle'>Olá, " . $_SESSION['nome'] . "<i class='ph ph-caret-down'></i></a>"; ?>
                        <ul class="dropdown-menu">
                            <?php echo "<li><a href='/sistemackc/usuario/{$_SESSION['id']}'>Perfil</a></li>"; ?>
                            <?php echo "<li><a href='/sistemackc/logout'>Logout</a></li>"; ?>
                        </ul>
                    </li>
                <?php } elseif (isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'Administrador') { ?>
                    <li class='drop-down'>
                        <?php echo "<a href='#' class='dropdown-toggle'>Olá, " . $_SESSION['nome'] . "<i class='ph ph-caret-down'></i></a>"; ?>
                        <ul class='dropdown-menu'>
                            <?php echo "<li><a href='/sistemackc/usuario/{$_SESSION['id']}'>Perfil</a></li>"; ?>
                            <li><a href='/sistemackc/admtm85/menu'>Dashboard</a></li>
                            <li><a href='/sistemackc/logout'>Logout</a></li>
                        </ul>
                    </li>
                <?php } else {
                    echo "<a href='/sistemackc/usuario/login'>Entrar</a>";
                }
                ?>
            </ul>
        </nav>
    </header>

    <!-- Conteúdo da página -->
    <?php
    if (isset($_SESSION['tipo'])) {
        $tipoUsuario = $_SESSION['tipo'];
    } else {
        $tipoUsuario = null;
    }


    if (!isset($usuario)) {
        echo "<h1>Usuário não encontrado</h1>";
        exit;
    }


    if ((isset($_SESSION['email']) && $_SESSION['email'] == $usuario['Email']) || $tipoUsuario == 'Administrador') {
    ?>
        <main class="container-conteudo">
            <div id="bt-go-back">
                <?php
                if ($tipoUsuario == 'Administrador') {
                    echo "<a href='/sistemackc/admtm85/usuario/{$usuario['Id']}'><i class='ph ph-caret-left'></i>Voltar</a>";
                } else {
                    echo "<a href='/sistemackc/usuario/{$usuario['Id']}'><i class='ph ph-caret-left'></i>Voltar</a>";
                }
                ?>
            </div>

            <h1 class="titulo">Alterar senha</h1>
            <p class="aviso"><i class="ph ph-warning"></i>Por motivos de segurança, <strong>não exibimos a senha do usuário.</strong> Se deseja alterá-la, preencha o campo abaixo:</p>

            <?php if (isset($_SESSION['tipo']) && $tipoUsuario == 'Administrador') {
                echo "<form action='/sistemackc/admtm85/usuario/atualizar/senha/{$usuario['Id']}' method='POST'>";
            } else {
                echo "<form action='/sistemackc/usuario/atualizar/senha/{$usuario['Id']}' method='POST'>";
            } ?>

                <?php if (isset($feedback)) {
                    if (isset($feedback) && $feedback != '') {
                        echo "<div class='container-feedback'>";
                        if ($classe == 'erro') {
                            echo "<span class='$classe'><i class='ph ph-warning-circle'></i><strong>$feedback</strong></span>";
                        } else {
                            echo "<span class='$classe'><i class='ph ph-check-square'></i><strong>$feedback</strong></span>";
                        }
                        echo "</div>";
                    }
                } ?>

                <div class="campos">
                    <div class="campo">
                        <label class="senha" for="senha">Senha:</label>
                        <input type="password" name="senha" required>
                    </div>

                    <div class="campo">
                        <label class="senha" for="senha">Confirmação de Senha:</label>
                        <input type="password" name="confirmarSenha" required>
                    </div>
                </div>

                <div class="botao">
                    <button type="submit" class="bt-alterar">Alterar</button>
                </div>
            </form>
        </main>
    <?php } else {
        echo "<h1>Acesso não autorizado</h1>";
    } ?>
    <footer>
        <div>
            <span class="copyright">© 2024 Copyright: ManasCode</span>
            <div>
                <img src="/sistemackc/views/Img/ImgIcones/github.png">
                <a target="_blank" href="https://github.com/LarissaSL/SistemaCKC_MVC">Repositório do Projeto</a>
            </div>
        </div>
    </footer>
</body>
</html>