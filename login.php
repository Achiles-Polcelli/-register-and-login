<?php
session_start();

// conexão com o banco de dados 
$host = "localhost";
$usuario = "root";
$senhaBanco = "";
$banco = "login_cadastro";

$conn = new mysqli($host, $usuario, $senhaBanco, $banco);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// rprocessa o login
$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"] ?? "";
    $senha = $_POST["password"] ?? "";

    if (empty($email) || empty($senha)) {
        $erro = "Preencha todos os campos.";
    } else {
        $stmt = $conn->prepare("SELECT id, senha FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();

            if (password_verify($senha, $usuario["senha"])) {
                $_SESSION["usuario_id"] = $usuario["id"];
                $_SESSION["email"] = $email;

                header("Location: dashboard.php");
                exit;
            } else {
                $erro = "Senha incorreta.";
            }
        } else {
            $erro = "Usuário não encontrado.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="src/styles/styles.css">
    <title>Login</title>
</head>
<body>

<main id="form_container">

    <form method="POST">

        <div id="form_header">
            <h1 id="form_title">Entrar na conta</h1>
            <pre>       </pre>
            <button class="btn-default" type="button">
                <a href="index.php" style="color:#fff; text-decoration:none;">
                    Criar conta <i class="fa-solid fa-right-to-bracket"></i>
                </a>
            </button>
        </div>

        <?php if (!empty($erro)): ?>
            <p style="color:red; margin-bottom:10px;">
                <?= $erro ?>
            </p>
        <?php endif; ?>

        <div class="input-box">
            <label class="form-label">E-mail</label>
            <div class="input-field">
                <input type="email" name="email" class="form-control" placeholder="E-mail" required>
                <i class="fa-regular fa-envelope"></i>
            </div>
        </div>

        <div class="input-box">
            <label class="form-label">Senha</label>
            <div class="input-field">
                <input type="password" name="password" class="form-control" placeholder="**************" required>
                <i class="fa-regular fa-eye-slash"></i>
            </div>
        </div>
            <br>
        <button type="submit" class="btn-default">
            <i class="fa-solid fa-check"></i> Entrar
        </button>

    </form>

</main>

<script src="src/javascript/script.js"></script>
</body>
</html>
