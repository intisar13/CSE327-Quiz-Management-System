<?php
// Start the session
session_start();

// Define the EmployDao interface
interface EmployDao {
    public function create($client, $username, $hashedPassword);
}

// Implementation class
class EmployDaoImpl implements EmployDao {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli("localhost", "root", "", "qms");
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Create a new user in the database
    public function create($client, $username, $hashedPassword) {
        if ($client === 'ADMIN') {
            $stmt = $this->conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
        } else if ($client === 'STUDENT') {
            $stmt = $this->conn->prepare("INSERT INTO student (username, password) VALUES (?, ?)");
        } else {
            throw new Exception("Invalid client type.");
        }

        $stmt->bind_param("ss", $username, $hashedPassword);
        if ($stmt->execute()) {
            echo "User created successfully.<br>";
        } else {
            throw new Exception("Error creating user: " . $stmt->error);
        }
        $stmt->close();
    }

    public function __destruct() {
        $this->conn->close();
    }
}

// Proxy class
class EmployDaoProxy implements EmployDao {
    private $employDao;

    public function __construct(EmployDao $dao) {
        $this->employDao = $dao;
    }

    public function create($client, $username, $hashedPassword) {
        $this->logCreationAttempt($client, $username);
        $this->employDao->create($client, $username, $hashedPassword);
    }

    private function logCreationAttempt($client, $username) {
        // Log the user creation attempt
        error_log("User creation attempt for client: $client, username: $username");
    }
}

// Password hashing (use bcrypt)
function hashPassword($password) {
    return $password; // Plaintext storage (not secure)
}

// Main registration logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $client = strtoupper($_POST['client']); // Convert to uppercase to match ADMIN/STUDENT

    $proxy = new EmployDaoProxy(new EmployDaoImpl());

    try {
        $hashedPassword = hashPassword($password);
        $proxy->create($client, $username, $hashedPassword);
        echo "Account created successfully. You can now log in.<br>";
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
    <form action="register.php" method="POST">
        <label for="client">Client Type (ADMIN/STUDENT):</label>
        <input type="text" name="client" id="client" required>
        <br>
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
        <br>
        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>
