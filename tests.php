<?php

require_once __DIR__ . '/data-access/BlackjackDataAccess.php';
require_once __DIR__ . '/entities/User.php';
require_once __DIR__ . '/entities/Score.php';
require_once __DIR__ . "/entities/Card.php";
require_once __DIR__ . '/utils/SecUtils.php';


?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pruebas de ScoreDataAccess</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

</head>

<body class="container">


    <h1>Probando BlackjackDataAccess</h1>

    <h2>Ubicación del fichero php.ini</h2>
    <p>
        <?= php_ini_loaded_file() ?>
    </p>
    <p>Si no hay fichero .ini, hay que copiar el fichero ini "development", y renombrarlo a php.ini.</p>
    <p>Modificar el fichero para hacer los siguientes cambios:</p>
    <ul>
        <li>Descomentar la línea <em>;extension_dir = "ext"</em>, quitando el punto y coma.</li>
        <li>Descomentar la línea <em>;extension=pdo_sqlite</em>, quitando el punto y coma.</li>
    </ul>

    <?php

    // Ruta del fichero de la BD
    $dbFile = __DIR__ . '/blackjack.db';

    // Borrar el fichero de la BD si existe, para que las pruebas funcionen sin errores
    if (file_exists($dbFile)) {
        // Eliminar el archivo
        unlink($dbFile);
    }

    // Crear un objeto para acceso a la BD
    $blackjackDataAccess = new BlackjackDataAccess($dbFile);

    // Inicializar resultados
    $results = [];

    // 1. Obtener todos los usuarios
    echo "<h2>Todos los usuarios</h2><ul>";
    $users = $blackjackDataAccess->getAllUsers();
    foreach ($users as $user) {
        echo "<li>ID: {$user->getId()}, Ususario: {$user->getUsername()}, Nombre: {$user->getFirstName()} {$user->getLastName()}, Email: {$user->getEmail()}</li>";
    }
    echo "</ul>";

    // 2. Crear un nuevo usuario
    $newUser = new User('new.user@example.com', password_hash('newpassword', PASSWORD_DEFAULT), 'UserNuevo', 'Nuevo', 'Usuario', '1992-01-01', 0);
    $created = $blackjackDataAccess->createUser($newUser);
    echo "<h2>Crear usuario</h2>" . ($created ? "Usuario creado." : "Error al crear usuario.");

    // 3. Obtener un usuario por ID
    echo "<h2>Usuario por su id</h2>";
    $user = $blackjackDataAccess->getUserById(1);
    echo $user ? "Usuario encontrado: {$user->getFirstName()} {$user->getLastName()}" : "Usuario no encontrado.";

    // 4. Actualizar un usuario
    $userToUpdate = new User('updated.user@example.com', password_hash('updatedpassword', PASSWORD_DEFAULT), 'NuevoUser', 'Usuario', 'Actualizado', '2007-01-01', 10);
    $updated = $blackjackDataAccess->updateUser($userToUpdate);
    echo "<h2>Actualizar usuario</h2>" . ($updated ? "Usuario actualizado." : "Error al actualizar usuario.");

    // 5. Obtener todos los usuarios, otra vez
    echo "<h2>Todos los usuarios</h2><ul>";
    $users = $blackjackDataAccess->getAllUsers();
    foreach ($users as $user) {
        echo "<li>ID: {$user->getId()}, Usuario: {$user->getUsername()}, Nombre: {$user->getFirstName()} {$user->getLastName()}, Email: {$user->getEmail()}</li>";
    }
    echo "</ul>";

    // 6. Obtener todos los scores de un usuario
    echo "<h2>Scores por ID de usuario</h2><ul>";
    $scores = $blackjackDataAccess->getScoresByUserId(1);
    foreach ($scores as $score) {
        echo "<li>ID: {$score->getId()}, Score: {$score->getScore()}";
    }
    echo "</ul>";

    // 7. Crear un nuevo score
    $newScore = new Score(1, 5000);
    $scoreCreated = $blackjackDataAccess->createScore($newScore);
    echo "<h2>Crear scoreo</h2>" . ($scoreCreated ? "Score creado." : "Error al crear score.");

    // 8. Obtener un scoreo por ID
    echo "<h2>Obtener score por ID</h2>";
    $score = $blackjackDataAccess->getScoreById(1);
    echo $score ? "Scoreo encontrado: {$score->getScore()}" : "Score no encontrado.";

    // 9. Actualizar un score
    $scoreToUpdate = new Score(1, 500, 1);
    $scoreUpdated = $blackjackDataAccess->updateScore($scoreToUpdate);
    echo "<h2>Actualizar scoreo</h2>" . ($scoreUpdated ? "Score actualizado con éxito." : "Error al actualizar scoreo.");

    // 10. Obtener todos los scores de un usuario
    echo "<h2>Obtener scores por ID de usuario</h2><ul>";
    $scores = $blackjackDataAccess->getScoresByUserId(1);
    foreach ($scores as $score) {
        echo "<li>ID del score: {$score->getId()}, Score: {$score->getScore()}</li>";
    }
    echo "</ul>";

    // 11. Eliminar un score
    echo "<h2>Eliminar score</h2>";
    $scoreDeleted = $blackjackDataAccess->deleteScore(1);
    echo $scoreDeleted ? "Score eliminado con éxito." : "Error al eliminar score.";

    // 12. Eliminar un usuario
    echo "<h2>Eliminar usuario</h2>";
    $userDeleted = $blackjackDataAccess->deleteUserById(1);
    echo $userDeleted ? "Usuario eliminado con éxito." : "Error al eliminar usuario.";

    // 10. Obtener todos los scores
    echo "<h2>Obtener top scores</h2><ul>";
    $scores = $blackjackDataAccess->getTopScores();
    $cont = 1;
    foreach ($scores as $score) {
        echo "<li> {$cont}. " . "{$score['username']} {$score['score']}</li>";
        $cont++;
    }
    echo "</ul>";

    // 11. Obtener todas las cartas
    echo "<h2>Obtener cartas</h2><ul>";
    $cartas = $blackjackDataAccess->getAllCards();
    foreach ($cartas as $card) {
        echo "<li>ID: {$card->getId()}, Carta: {$card->getName()}, Valor: {$card->getValue()}, Palo: {$card->getSuit()}</li>
                <li><img src='{$card->getImg()}' alt= '{$card->getValue()} de {$card->getSuit()}'></li>";
    }
    echo "</ul>";

    // 3. Obtener carta por ID
    echo "<h2>Carta por su id</h2>";
    $card = $blackjackDataAccess->getCardById(1);
    echo $card ? "Carta encontrada: {$card->getName()} {$card->getValue()} {$card->getSuit()}" : "Usuario no encontrado.";

    ?>

</body>

</html>