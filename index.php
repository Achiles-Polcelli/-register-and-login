<?php
$erro = "";
$sucesso = "";

// conexão com o banco
$conn = new mysqli("localhost", "root", "", "login_cadastro");

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = $_POST["name"] ?? "";
    $sobrenome = $_POST["last_name"] ?? "";
    $nascimento = $_POST["birthdate"] ?? "";
    $email = $_POST["email"] ?? "";
    $senha = $_POST["password"] ?? "";
    $confirmar = $_POST["confirm_password"] ?? "";
    $genero = $_POST["gender"] ?? "";

    if (
        empty($nome) || empty($sobrenome) || empty($nascimento) ||
        empty($email) || empty($senha) || empty($confirmar) || empty($genero)
    ) {
        $erro = "Preencha todos os campos.";
    } elseif ($senha !== $confirmar) {
        $erro = "As senhas não coincidem.";
    } else {

        // verifca se o email ja existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $erro = "Este e-mail já está cadastrado.";
        } else {

            // criptografa a senha
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            // ajusta o genero para o ENUM no banco de dados
            if ($genero === "female") $genero = "Feminino";
            if ($genero === "male") $genero = "Masculino";
            if ($genero === "other") $genero = "Outro";

            // insera ao banco de dados
            $stmt = $conn->prepare("
                INSERT INTO usuarios 
                (nome, sobrenome, nascimento, email, senha, genero)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "ssssss",
                $nome,
                $sobrenome,
                $nascimento,
                $email,
                $senhaHash,
                $genero
            );

            if ($stmt->execute()) {
                $sucesso = "Conta criada com sucesso!";
            } else {
                $erro = "Erro ao criar conta: " . $stmt->error;
            }
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
    <title>Cadastro</title>
</head>
<body>

<main id="form_container">

    <div id="form_header">
        <h1 id="form_title">Criar conta</h1>
        <button class="btn-default" type="button">
            <a href="login.php" style="color:#fff; text-decoration:none;">
                Entrar <i class="fa-solid fa-right-to-bracket"></i>
            </a>
        </button>
    </div>

    <?php if ($erro): ?>
        <p style="color:red; margin-bottom:10px;">
            <?= $erro ?>
        </p>
    <?php endif; ?>

    <?php if ($sucesso): ?>
        <p style="color:green; margin-bottom:10px;">
            <?= $sucesso ?>
        </p>
    <?php endif; ?>

    <form method="POST">

        <div id="input_container">

            <div class="input-box">
                <label class="form-label">Primeiro nome</label>
                <div class="input-field">
                    <input type="text" name="name" class="form-control" placeholder="Primeiro nome" required>
                    <i class="fa-regular fa-user"></i>
                </div>
           </div>


            <div class="input-box">
                <label class="form-label">Sobrenome</label>
                <div class="input-field">
                    <input type="text" name="last_name" class="form-control" placeholder="Sobrenome" required>
                    <i class="fa-regular fa-user"></i>
                </div>
            </div>

            <div class="input-box">
                <label class="form-label">Nascimento</label>
                <div class="input-field">
                    <input type="date" name="birthdate" class="form-control" placeholder="Sobrenome" required>
                </div>
            </div>

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
                    <i class="fa-regular fa-eye-slash toggle-password" data-target="confirm_password"></i>
                </div>
            </div>


            <div class="input-box">
                <label class="form-label">Confirmar Senha</label>
                <div class="input-field">
                    <input type="password" name="confirm_password" class="form-control" placeholder="**************" required>
                    <i class="fa-regular fa-eye-slash password-icon"></i>
                </div>
            </div>

            <div class="radio-container">
                <label class="form-label">Gênero</label>

                <div id="gender_inputs">
                    <div class="radio-box">
                        <input type="radio" name="gender" value="female" required>
                        <label for="female" class="form-label">Feminino</label>
                    </div>

                    <div class="radio-box">
                        <input type="radio" name="gender" value="male" required>
                        <label for="male" class="form-label">
                            Masculino
                        </label>
                    </div>

                    <div class="radio-box">
                        <input type="radio" name="gender" value="other" required>
                        <label for="other" class="form-label">Outro</label>
                    </div>

                </div>
            </div>

<br>


        </div>

        <button type="submit" class="btn-default">
            <i class="fa-solid fa-check"></i> Criar conta
        </button>

    </form>

</main>

</body>
</html>
