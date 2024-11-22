<?php
// Start the session
session_start();

// Define the EmployDao interface
interface EmployDao {
    public function get($client, $username);
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

    // Retrieve user details based on username and client type
    public function get($client, $username) {
        if ($client === 'ADMIN') {
            $stmt = $this->conn->prepare("SELECT * FROM admin WHERE username = ?");
        } else if ($client === 'STUDENT') {
            $stmt = $this->conn->prepare("SELECT * FROM student WHERE username = ?");
        } else {
            return null;
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc(); // Return user data as associative array
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

    // Enhanced access control and logging
    public function get($client, $username) {
        $this->logAccessAttempt($client, $username);
        return $this->employDao->get($client, $username);
    }

    private function logAccessAttempt($client, $username) {
        // Log the access attempt for auditing (simplified)
        error_log("Access attempt by client: $client, username: $username");
    }
}

// Password verification function (plain text for this example)
function verifyPassword($inputPassword, $storedPassword) {
    return $inputPassword === $storedPassword;
}

// Main login logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $client = strtoupper($_POST['client']); // Convert to uppercase to match ADMIN/STUDENT

    $proxy = new EmployDaoProxy(new EmployDaoImpl());

    try {
        $user = $proxy->get($client, $username);

        if ($user && verifyPassword($password, $user['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['client'] = $client;

            // Redirect based on the client type
            if ($client === 'ADMIN') {
                header("Location: home2.php"); // Redirect admin to home2.php
            } else if ($client === 'STUDENT') {
                header("Location: home.php"); // Redirect student to home.php
            }
            exit;
        } else {
            throw new Exception("Invalid credentials.");
        }
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
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form action="login.php" method="POST">
        <label for="client">Client Type (ADMIN/STUDENT):</label>
        <input type="text" name="client" id="client" required>
        <br>
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
        <br>
        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>
</body>
</html>
