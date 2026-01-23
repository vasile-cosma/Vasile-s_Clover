<?php
require_once(__DIR__ . "/common.php");
$errors = [];
$users = $blackjackDataAccess->getAllUsers();
$registeredUsers = count($blackjackDataAccess->getAllUsers());

$email = '';
$password = '';
$remember = false;

// Si ya hay sesión iniciada, redirigimos a blackjack.php
if (isset(($_SESSION['email']))) {
    header('Location: main_page.php');
    die;
}

if (isset($_COOKIE['id'])) {
    $userRemembered = $blackjackDataAccess->getUserById($_COOKIE['id']);

    if ($userRemembered != null) {
        $email = $userRemembered->getEmail();
        $remember = true;
    }
}

// Si el método es POST, validamos la información recibida
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $remember = filter_input(INPUT_POST, 'remember', FILTER_VALIDATE_BOOLEAN) ?? false;

    $user = $blackjackDataAccess->getUserByEmail($email);

    // Ante cualquier discrepancia, añadimos un error a nuestro array
    if (empty($email)) {
        $errors[] = 'ERROR: Debe introducir un email';
    } elseif (!empty($email) && !isset($user)) {
        $errors[] = 'ERROR: No existe ningún usuario asociado a este email.';
    }

    if (!isset($password) || empty($password)) {
        $errors[] = 'ERROR: Debe introducir una contraseña.';
    } elseif (isset($user) && !password_verify($password, $user->getPassword())) {
        $errors[] = 'ERROR: La contraseña introducida no es correcta.';
    }

    if (empty($errors)) {
        if ($remember) {
            setcookie('id', $user->getId(), time() + 3600);
        }

        $_SESSION['email'] = $email;
        $_SESSION['user_id'] = $user->getId();
        header('Location: main_page.php');
        die;
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="./static/css/index.css">
</head>

<body class="bg mt-0 pt-0 bt-0">
    <?php
    // Si hay errores, los mostramos
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($errors)) {
        echo '<div class="container"> <i class="bi bi-exclamation-triangle-fill me-2"></i>';
        foreach ($errors as $error) {
            echo '<div class="alert alert-danger">' . $error . '</div>';
        }
        echo '</div>';
    }
    ?>

    <div
        class="container mt-5 border border-dark rounded-5 p-4 card bg-dark bg-opacity-75 text-white align-items-center shadow">
        <img src="static/images/logo.png" class="logo-casino mb-3" alt="Vasile's Clover">
        <div class="row">
            <h1 class="fw-bold text-center text-light border-bottom border-2 border-secondary">INICIO DE SESIÓN</h1>
        </div>

        <form method="post" novalidate>
            <div class="form-group row mt-1">
                <label for="email" class="text-center">Email</label>
                <input type="email" name="email" id="email" placeholder="tu_email@ejemplo.com"
                    class="form-control border rounded-pill" required value="<?= $email ?? '' ?>">
            </div>

            <div class="form-group row mt-1">
                <label for="password" class="text-center">Contraseña</label>
                <input type="password" name="password" id="password" class="form-control border rounded-pill col-2"
                    placeholder="Contraseña" required>
            </div>

            <div class="form-check d-flex justify-content-center align-items-center gap-2 mt-1">
                <input type="checkbox" name="remember" id="remember" value="true" class="form-check-input"
                    style="border-color:white" <?php if ($remember)
                        echo "checked"; ?>>
                <label for="remember" class="form-check-label text-center" style="color:white"
                    value="true">Recuérdame</label>
            </div>

            <div class="row">
                <button type=" submit" value="submit" class="btn btn-light border border-dark rounded-3">Iniciar
                    sesión</button>
            </div>
        </form>

        <div class="text-center mt-1">Usuarios registrados: <?= $registeredUsers ?></div>
        <div class="text-center lead mt-2">¿No tienes cuenta? <a href="register.php"
                class="btn btn-light rounded-3">¡Regístrate!</a>
        </div>

    </div>
    <?php


    ?>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous"></script>

</html>