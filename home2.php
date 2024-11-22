<?php
// Start the session
session_start();

if (!isset($_SESSION['username']) || $_SESSION['client'] !== 'ADMIN') {
    header("Location: login.php");
    exit;
}

// Database connection - Singleton Pattern
class Database {
    private static $conn;

    private function __construct() {} // Prevent instantiation
    private function __clone() {} // Prevent cloning

    public static function getConnection() {
        if (self::$conn === null) {
            self::$conn = new mysqli("localhost", "root", "", "qms");
            if (self::$conn->connect_error) {
                die("Connection failed: " . self::$conn->connect_error);
            }
        }
        return self::$conn;
    }

    public static function closeConnection() {
        if (self::$conn) {
            self::$conn->close();
            self::$conn = null;
        }
    }
}

// Question class representing a question entity (Factory Pattern)
class Question {
    public $questiontext;
    public $optionA;
    public $optionB;
    public $optionC;
    public $optionD;
    public $answer;
    public $difficulty;
    public $subject;

    public function __construct($questiontext, $optionA, $optionB, $optionC, $optionD, $answer, $difficulty, $subject) {
        $this->questiontext = $questiontext;
        $this->optionA = $optionA;
        $this->optionB = $optionB;
        $this->optionC = $optionC;
        $this->optionD = $optionD;
        $this->answer = $answer;
        $this->difficulty = $difficulty;
        $this->subject = $subject;
    }
}

// Factory Class to create questions (Factory Pattern)
class QuestionFactory {
    public static function create($questiontext, $optionA, $optionB, $optionC, $optionD, $answer, $difficulty, $subject) {
        return new Question($questiontext, $optionA, $optionB, $optionC, $optionD, $answer, $difficulty, $subject);
    }

    public static function save(Question $question) {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("INSERT INTO question (questiontext, optionA, optionB, optionC, optionD, answer, difficulty, subject) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "ssssssss",
            $question->questiontext,
            $question->optionA,
            $question->optionB,
            $question->optionC,
            $question->optionD,
            $question->answer,
            $question->difficulty,
            $question->subject
        );

        if ($stmt->execute()) {
            return "Question added successfully!";
        } else {
            return "Error adding question: " . $stmt->error;
        }
    }

    public static function updateQuestion($question_id, $questiontext, $optionA, $optionB, $optionC, $optionD, $answer, $difficulty, $subject) {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("UPDATE question SET questiontext = ?, optionA = ?, optionB = ?, optionC = ?, optionD = ?, answer = ?, difficulty = ?, subject = ? WHERE question_id = ?");
        $stmt->bind_param("ssssssssi", $questiontext, $optionA, $optionB, $optionC, $optionD, $answer, $difficulty, $subject, $question_id);

        if ($stmt->execute()) {
            return "Question updated successfully!";
        } else {
            return "Error updating question: " . $stmt->error;
        }
    }

    public static function deleteQuestion($question_id) {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("DELETE FROM question WHERE question_id = ?");
        $stmt->bind_param("i", $question_id);

        if ($stmt->execute()) {
            return "Question deleted successfully!";
        } else {
            return "Error deleting question: " . $stmt->error;
        }
    }

    public static function viewAllQuestions() {
        $conn = Database::getConnection();
        $result = $conn->query("SELECT * FROM question");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function viewStudentInfo() {
        $conn = Database::getConnection();
        $result = $conn->query("SELECT username, rewardPoints, totalMarks, badge FROM student");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

// View Strategy Pattern
interface ViewStrategy {
    public function render();
}

class ViewQuestionsStrategy implements ViewStrategy {
    public function render() {
        $questions = QuestionFactory::viewAllQuestions();
        echo "<h3>All Questions</h3>";
        echo "<table border='1'>
                <tr>
                    <th>ID</th>
                    <th>Question Text</th>
                    <th>Options</th>
                    <th>Answer</th>
                    <th>Difficulty</th>
                    <th>Subject</th>
                    <th>Action</th>
                </tr>";
        foreach ($questions as $q) {
            echo "<tr>
                    <td>{$q['question_id']}</td>
                    <td>{$q['questiontext']}</td>
                    <td>{$q['optionA']}, {$q['optionB']}, {$q['optionC']}, {$q['optionD']}</td>
                    <td>{$q['answer']}</td>
                    <td>{$q['difficulty']}</td>
                    <td>{$q['subject']}</td>
                    <td><form method='POST' action='home2.php'>
                        <input type='hidden' name='action' value='delete'>
                        <input type='hidden' name='question_id' value='{$q['question_id']}'>
                        <button type='submit' onclick='return confirm(\"Are you sure you want to delete this question?\")'>Delete</button>
                    </form></td>
                </tr>";
        }
        echo "</table>";
    }
}

class ViewStudentsStrategy implements ViewStrategy {
    public function render() {
        $students = QuestionFactory::viewStudentInfo();
        echo "<h3>All Students</h3>";
        echo "<table border='1'>
                <tr>
                    <th>Username</th>
                    <th>Reward Points</th>
                    <th>Total Marks</th>
                    <th>Batch</th>
                </tr>";
        foreach ($students as $s) {
            echo "<tr>
                    <td>{$s['username']}</td>
                    <td>{$s['rewardPoints']}</td>
                    <td>{$s['totalMarks']}</td>
                    <td>{$s['badge']}</td>
                </tr>";
        }
        echo "</table>";
    }
}

// Context for Strategy
class ViewContext {
    private $strategy;

    public function setStrategy(ViewStrategy $strategy) {
        $this->strategy = $strategy;
    }

    public function render() {
        if ($this->strategy !== null) {
            $this->strategy->render();
        } else {
            echo "<p>No strategy set!</p>";
        }
    }
}

// Controller logic for form submission
$message = '';
$action = $_POST['action'] ?? '';
$viewContext = new ViewContext();

if (isset($_POST['viewAllQuestions'])) {
    $viewContext->setStrategy(new ViewQuestionsStrategy());
} elseif (isset($_POST['viewAllStudents'])) {
    $viewContext->setStrategy(new ViewStudentsStrategy());
}

if ($action === 'add') {
    // Add a new question
    $question = QuestionFactory::create($_POST['questiontext'], $_POST['optionA'], $_POST['optionB'], $_POST['optionC'], $_POST['optionD'], $_POST['answer'], $_POST['difficulty'], $_POST['subject']);
    $message = QuestionFactory::save($question);
} elseif ($action === 'update') {
    // Update an existing question
    $message = QuestionFactory::updateQuestion($_POST['question_id'], $_POST['questiontext'], $_POST['optionA'], $_POST['optionB'], $_POST['optionC'], $_POST['optionD'], $_POST['answer'], $_POST['difficulty'], $_POST['subject']);
} elseif ($action === 'delete') {
    // Delete a question
    $message = QuestionFactory::deleteQuestion($_POST['question_id']);
}

// Fetch all questions and student info
$questions = QuestionFactory::viewAllQuestions();
$students = QuestionFactory::viewStudentInfo();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
</head>
<body>
    <h2>Admin Dashboard</h2>
    <p><?= $message ?></p>

    <h3>Add a New Question</h3>
    <form action="home2.php" method="POST">
        <input type="hidden" name="action" value="add">
        <label for="questiontext">Question Text:</label>
        <input type="text" name="questiontext" id="questiontext" required><br><br>
        <label for="optionA">Option A:</label>
        <input type="text" name="optionA" id="optionA" required><br><br>
        <label for="optionB">Option B:</label>
        <input type="text" name="optionB" id="optionB" required><br><br>
        <label for="optionC">Option C:</label>
        <input type="text" name="optionC" id="optionC" required><br><br>
        <label for="optionD">Option D:</label>
        <input type="text" name="optionD" id="optionD" required><br><br>
        <label for="answer">Answer:</label>
        <input type="text" name="answer" id="answer" required><br><br>
        <label for="difficulty">Difficulty:</label>
        <input type="text" name="difficulty" id="difficulty" required><br><br>
        <label for="subject">Subject:</label>
        <input type="text" name="subject" id="subject" required><br><br>
        <button type="submit">Save Question</button>
    </form>

    <!-- Link to the quiz session page -->
    <p><a href="quizs.php">Go to Create Quiz Session</a></p>

    <h3>Actions</h3>
    <form action="home2.php" method="POST">
        <button type="submit" name="viewAllQuestions">View All Questions</button>
        <button type="submit" name="viewAllStudents">View All Students</button>
    </form>

    <?php
    // Render the view based on the selected strategy
    $viewContext->render();
    ?>
<a href="logout.php">Logout</a>
</body>
</html>
