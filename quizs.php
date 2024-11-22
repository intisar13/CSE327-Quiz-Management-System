<?php
// Start the session
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "qms";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch student ID
$sql_student = "SELECT student_id FROM student WHERE username = ?";
$stmt_student = $conn->prepare($sql_student);
$stmt_student->bind_param("s", $_SESSION['username']);
$stmt_student->execute();
$stmt_student->bind_result($student_id);
$stmt_student->fetch();
$stmt_student->close();

// Fetch questions for the quiz (max 10 questions)
$sql_questions = "SELECT question_id, questiontext, optionA, optionB, optionC, optionD, answer FROM question LIMIT 10";
$result_questions = $conn->query($sql_questions);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = 0; // Track total score
    $start_time = $_SESSION['quiz_start_time']; // Get the start time from session
    $end_time = time(); // Get the end time when the quiz is submitted
    $time_taken = $end_time - $start_time; // Calculate the time taken in seconds

    // Loop through each submitted answer
    foreach ($_POST as $key => $value) {
        // Check if the answer is for a question (e.g., question_1, question_2)
        if (strpos($key, 'question_') === 0) {
            $question_id = str_replace('question_', '', $key); // Extract question ID from form input name
            $selected_answer = $value;

            // Fetch the correct answer for this question from the database
            $sql_answer = "SELECT answer FROM question WHERE question_id = ?";
            $stmt_answer = $conn->prepare($sql_answer);
            $stmt_answer->bind_param("i", $question_id);
            $stmt_answer->execute();
            $stmt_answer->bind_result($correct_answer);
            $stmt_answer->fetch();
            $stmt_answer->close();

            // Compare selected answer with correct answer
            if ($selected_answer === $correct_answer) {
                $score += 1; // Increment score if correct
            }
        }
    }

    // Insert score and time taken into the quizsession table
    $sql_insert_quizsession = "INSERT INTO quizsession (score, time, student_id) VALUES (?, ?, ?)";
    $stmt_quizsession = $conn->prepare($sql_insert_quizsession);
    $stmt_quizsession->bind_param("iii", $score, $time_taken, $student_id);
    $stmt_quizsession->execute();
    $stmt_quizsession->close();

    // Add the score to the student's totalMarks in the student table
    $sql_update_student = "UPDATE student SET totalMarks = totalMarks + ? WHERE student_id = ?";
    $stmt_update_student = $conn->prepare($sql_update_student);
    $stmt_update_student->bind_param("ii", $score, $student_id);
    $stmt_update_student->execute();
    $stmt_update_student->close();

    echo "<p>Quiz submitted successfully!</p>";
    echo "<p>Your total score: $score</p>";
    echo "<p>You took $time_taken seconds to complete the quiz.</p>";
    exit;
}

// Store quiz start time in session
$_SESSION['quiz_start_time'] = time();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Quiz</title>
    <script>
        // Countdown timer
        let timeLeft = 300; // 5 minutes in seconds
        let score = 0; // Placeholder for the score
        let timerInterval;

        // Function to update the timer display
        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            document.getElementById("timer").textContent = `${minutes}:${seconds < 10 ? '0' + seconds : seconds}`;

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                document.getElementById("quizForm").submit(); // Auto-submit the form
            }

            timeLeft--;
        }

        // Start the timer when the page loads
        function startTimer() {
            timerInterval = setInterval(updateTimer, 1000);
        }

        // Function to update score (called by PHP if necessary)
        function updateScore(newScore) {
            score = newScore;
            document.getElementById("score").textContent = "Score: " + score;
        }

        // Initialize the timer when the page loads
        window.onload = function() {
            startTimer();
        };
    </script>
</head>
<body>
    <h2>Take Quiz</h2>
    <p id="timer" style="font-size: 20px; color: red;">5:00</p>
    <p id="score">Score: 0</p>

    <form id="quizForm" method="POST" action="quizs.php">
        <?php if ($result_questions->num_rows > 0): ?>
            <?php while ($row = $result_questions->fetch_assoc()): ?>
                <div>
                    <p><strong><?php echo htmlspecialchars($row['questiontext']); ?></strong></p>
                    <label><input type="radio" name="question_<?php echo $row['question_id']; ?>" value="A" required> <?php echo htmlspecialchars($row['optionA']); ?></label><br>
                    <label><input type="radio" name="question_<?php echo $row['question_id']; ?>" value="B" required> <?php echo htmlspecialchars($row['optionB']); ?></label><br>
                    <label><input type="radio" name="question_<?php echo $row['question_id']; ?>" value="C" required> <?php echo htmlspecialchars($row['optionC']); ?></label><br>
                    <label><input type="radio" name="question_<?php echo $row['question_id']; ?>" value="D" required> <?php echo htmlspecialchars($row['optionD']); ?></label><br>
                </div>
                <hr>
            <?php endwhile; ?>
            <button type="submit">Submit Quiz</button>
        <?php else: ?>
            <p>No questions available for the quiz.</p>
        <?php endif; ?>
    </form>
</body>
</html>


