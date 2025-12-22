<?php
require_once(__DIR__ . "/common.php");

$errors = [];
$users = $blackjackDataAccess->getAllUsers();
$registeredUsers = count($blackjackDataAccess->getAllUsers());

$email = '';
$password = '';
$recuerdame = false;

if (empty(!$_SESSION)) {
    header('Location: game.php');
    die;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $recuerdame = filter_input(INPUT_POST, 'recuerdame', FILTER_VALIDATE_BOOLEAN);

    $user = $blackjackDataAccess->getUserByEmail($email);

    if (empty($email)) {
        $errors[] = 'ERROR: Debe introducir un email';
    } elseif (!empty($email) && !isset($user)) {
        $errors[] = 'ERROR: No existe ningún usuario asociado a este email.';
    }

    if (!isset($password) || empty($password)) {
        $errors[] = 'ERROR: Debe introducir una contraseña.';
    } else if (isset($user) && !password_verify($password, $user->getPassword())) {
        $errors[] = 'ERROR: La contraseña introducida no es correcta.';
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

<body>
    <?php

    if (!empty($errors)) {
        echo '<ul>';
        foreach ($errors as $error) {
            echo '<li>' . $error . '</li>';
        }
        echo '</ul>';
    }
    ?>
    <div class="container">
        <h1 class="fw-bold text-center text-light border-bottom border-2 border-secondary">INICIO DE SESIÓN</h1>
        <form method="post" novalidate>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" placeholder="tu_email@ejemplo.com" class="form-control">
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>
            <div class="form-group">
                <label for="recuerdame">Recuérdame</label>
                <input type="checkbox" name="recuerdame" id="recuerdame" class="form-check-input">
            </div>
            <button type=" submit" value="submit" class="btn btn-light">Iniciar sesión</button>
        </form>
        <div>Usuarios registrados: <?= $registeredUsers ?></div>
        <div>¿No tienes cuenta? <a href="register.php">¡Regístrate!</a></div>
    </div>

</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous"></script>

</html>