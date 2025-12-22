<?php

require_once __DIR__ . "/../entities/User.php";
require_once __DIR__ . "/../entities/Score.php";
require_once __DIR__ . "/../entities/Card.php";
require_once __DIR__ . "/../utils/SecUtils.php";


class BlackjackDataAccess
{
    private $pdo;

    public function __construct($dbFile)
    {
        $this->pdo = new PDO("sqlite:" . $dbFile);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->createTables();
        $this->createInitialDataIfTablesEmpty();
    }

    private function createTables(): void
    {
        // Crear tabla de usuarios
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT,
                                                            email TEXT UNIQUE,
                                                            password TEXT,
                                                            username TEXT,
                                                            first_name TEXT,
                                                            last_name TEXT,
                                                            birth_date TEXT)");

        // Crear tabla de usuarios
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS scores (id INTEGER PRIMARY KEY AUTOINCREMENT,
                                                            user_id INTEGER,
                                                            score INTEGER)");

        // Crear tabla de cartas
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS cards (id INTEGER PRIMARY KEY AUTOINCREMENT,
                                                            name TEXT,
                                                            value INTEGER,
                                                            suit TEXT,
                                                            img TEXT)");


    }

    private function createInitialDataIfTablesEmpty(): void
    {
        if ($this->isUsersTableEmpty()) {
            $this->createInitialUsers();
        }
        if ($this->isScoresTableEmpty()) {
            $this->createInitialScores();
        }
        if ($this->isCardsTableEmpty()) {
            $this->createInitialCards();
        }
    }

    private function isUsersTableEmpty(): bool
    {
        $stmt = $this->pdo->query("SELECT COUNT(1) as count FROM users");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] == 0; // True si la tabla está vacía
    }

    private function isScoresTableEmpty(): bool
    {
        $stmt = $this->pdo->query("SELECT COUNT(1) as count FROM scores");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] == 0; // True si la tabla está vacía
    }

    private function isCardsTableEmpty(): bool
    {
        $stmt = $this->pdo->query("SELECT COUNT(1) as count FROM cards");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] == 0; // True si la tabla está vacía
    }

    private function createInitialUsers(): void
    {
        $this->pdo->exec("INSERT INTO users (email, password, username, first_name, last_name, birth_date) VALUES 
                                                ('vasile.cosma@example.com', '" . password_hash('password123', PASSWORD_DEFAULT) . "', 'PaniK', 'Vasile', 'Cosma', '2001-07-30'),
                                                ('omar.fernandez@example.com', '" . password_hash('pass456', PASSWORD_DEFAULT) . "', 'ElPrimo', 'Omar', 'Fernandez', '2001-01-25'),
                                                ('izar.leonel@example.com', '" . password_hash('abc789', PASSWORD_DEFAULT) . "', 'Pescaito98', 'Izar', 'Leonel', '1998-02-02'),
                                                ('yuiht.fernandez@example.com', '" . password_hash('yuiht321', PASSWORD_DEFAULT) . "', 'yuiht', 'Yuiht', 'Fernandez', '1998-08-23')");
    }

    private function createInitialScores(): void
    {
        $this->pdo->exec("INSERT INTO scores (user_id, score) VALUES (1, 5000);");
        $this->pdo->exec("INSERT INTO scores (user_id, score) VALUES (2, 7500);");
        $this->pdo->exec("INSERT INTO scores (user_id, score) VALUES (3, 10000);");
        $this->pdo->exec("INSERT INTO scores (user_id, score) VALUES (3, 6000);");
        $this->pdo->exec("INSERT INTO scores (user_id, score) VALUES (4, 300);");

    }

    private function createInitialCards(): void
    {
        $suits = ['hearts', 'clubs', 'diamonds', 'spades'];
        $names = ['ace', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'jack', 'queen', 'king'];
        $values = [11, 2, 3, 4, 5, 6, 7, 8, 9, 10, 10, 10, 10];
        foreach ($suits as $suit) {
            foreach ($names as $index => $name) {
                $img = $name . '_of_' . $suit . '.svg';
                $value = $values[$index];
                $this->pdo->exec("INSERT INTO cards (name, value, suit, img) VALUES ('$name', '$value', '$suit', '$img')");
            }
        }
    }

    // Obtener un usuario por ID
    public function getUserById(int $userId): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $user = new User(
                $result['email'],
                $result['password'],
                $result['username'],
                $result['first_name'],
                $result['last_name'],
                $result['birth_date'],
                $result['id']
            );
            return $user;
        } else {
            return null; // Devuelve null si no se encuentra el usuario
        }
    }


    // Obtener un usuario por su correo electrónico
    public function getUserByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $user = new User(
                $result['email'],
                $result['password'],
                $result['username'],
                $result['first_name'],
                $result['last_name'],
                $result['birth_date'],
                $result['id']
            );
            return $user;
        } else {
            return null; // Retorna null si no se encuentra el usuario
        }
    }

    // Obtener todos los usuarios
    public function getAllUsers(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY email ASC");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $users = [];

        foreach ($results as $row) {
            $user = new User(
                $row['email'],
                $row['password'],
                $row['username'],
                $row['first_name'],
                $row['last_name'],
                $row['birth_date'],
                $row['id']
            );
            // Añadir usuario al array. Se podría hacer con array_push también.
            // También se podría hacer usando el id del usuario como clave, y el objeto como valor.
            $users[] = $user;
        }
        return $users;
    }

    // Crear un usuario. El atributo id se ignora, porque la BD lo asigna automáticamente
    public function createUser(User $user): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO users (email, password, username, first_name, last_name, birth_date) 
                                        VALUES (:email, :password, :username, :first_name, :last_name, :birth_date)");

        $stmt->bindValue(':email', $user->getEmail());
        $stmt->bindValue(':password', $user->getPassword());
        $stmt->bindValue(':username', $user->getUsername());
        $stmt->bindValue(':first_name', $user->getFirstName());
        $stmt->bindValue(':last_name', $user->getLastName());
        $stmt->bindValue(':birth_date', $user->getBirthDate());

        // Devuelve true si la sentencia se ejecuta correctamente
        return $stmt->execute();
    }

    // Actualizar un usuario de la BD
    public function updateUser(User $user): bool
    {
        $stmt = $this->pdo->prepare("UPDATE users SET email = :email, password = :password, username = :username, first_name = :first_name, 
                                        last_name = :last_name, birth_date = :birth_date WHERE id = :id");

        $stmt->bindValue(':id', $user->getId());
        $stmt->bindValue(':email', $user->getEmail());
        $stmt->bindValue(':password', $user->getPassword());
        $stmt->bindValue(':username', $user->getUsername());
        $stmt->bindValue(':first_name', $user->getFirstName());
        $stmt->bindValue(':last_name', $user->getLastName());
        $stmt->bindValue(':birth_date', $user->getBirthDate());

        // Devuelve true si la sentencia se ejecuta correctamente
        return $stmt->execute();
    }

    // Eliminar un usuario por su id
    public function deleteUserById(int $userId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindValue(':id', $userId);

        // Devuelve true si la sentencia se ejecuta correctamente
        return $stmt->execute();
    }

    // Obtener un Score por id
    public function getScoreById(int $scoreId): ?Score
    {
        $stmt = $this->pdo->prepare("SELECT * FROM scores WHERE id = :id");
        $stmt->bindValue(':id', $scoreId);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $score = new Score(
                $row['user_id'],
                $row['score'],
                $row['id']
            );
            return $score;
        }
        return null;
    }

    public function getScores(): array
    {
        $stmt = $this->pdo->query(
            "SELECT 
            scores.score,
            users.username
         FROM scores
         JOIN users ON scores.user_id = users.id
         ORDER BY scores.score DESC"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTopScores(): array
    {
        $stmt = $this->pdo->query(
            "SELECT 
            scores.score,
            users.username
         FROM scores
         JOIN users ON scores.user_id = users.id
         ORDER BY scores.score DESC
         LIMIT 10"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener todos los Scores de un usuario.
    public function getScoresByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM scores WHERE user_id = :user_id ORDER BY score ASC");
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $scores = [];

        foreach ($results as $row) {
            $score = new Score(
                $row['user_id'],
                $row['score'],
                $row['id']
            );
            $scores[] = $score;
        }
        return $scores;
    }

    // Crear un Score.
    public function createScore(Score $score): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO scores (user_id, score) 
                                        VALUES (:user_id, :score)");

        $stmt->bindValue(':user_id', $score->getUserId());
        $stmt->bindValue(':score', $score->getScore());

        return $stmt->execute();
    }

    // Actualizar un Score.
    public function updateScore(Score $score): bool
    {
        $stmt = $this->pdo->prepare("UPDATE scores SET score = :score WHERE id = :id");

        $stmt->bindValue(':score', $score->getScore());
        $stmt->bindValue(':id', $score->getId());

        return $stmt->execute();
    }

    // Eliminar un Score
    public function deleteScore(int $scoreId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM scores WHERE id = :id");
        $stmt->bindValue(':id', $scoreId);

        return $stmt->execute();
    }

    public function getAllCards(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM cards ORDER BY value ASC");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $cards = [];

        foreach ($results as $row) {
            $card = new Card(
                $row['name'],
                $row['value'],
                $row['suit'],
                $row['id']
            );
            // Añadir carta al array. Se podría hacer con array_push también.
            $cards[] = $card;
        }
        return $cards;
    }

    public function getCardById(int $cardId): ?Card
    {
        $stmt = $this->pdo->prepare("SELECT * FROM cards WHERE id = :id");
        $stmt->execute([':id' => $cardId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $card = new Card(
                $result['name'],
                $result['value'],
                $result['suit'],
                $result['id']
            );
            return $card;
        } else {
            return null; // Devuelve null si no se encuentra la carta
        }
    }
}
