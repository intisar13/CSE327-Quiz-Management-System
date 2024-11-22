<?php 
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Database connection (replace with your own credentials)
$servername = "localhost"; // Your server name
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "qms"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch student data based on the logged-in username
$sql = "SELECT student_id, username, rewardPoints, totalMarks, badge FROM student WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$stmt->bind_result($student_id, $username, $rewardPoints, $totalMarks, $badge);
$stmt->fetch();
$stmt->close();

// Fetch all student data for leaderboard
$sql = "SELECT student_id, username, rewardPoints, totalMarks, badge FROM student";
$result = $conn->query($sql);

// Store all student data
$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

// Sort students by totalMarks in descending order
usort($students, function($a, $b) {
    return $b['totalMarks'] - $a['totalMarks']; // descending order
});

// Check if the 'View Leaderboard' button is clicked
$viewLeaderboard = isset($_POST['viewLeaderboard']);

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Home</title>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>

    <!-- Display student information at the top -->
    <table border="1">
        <tr>
            <th>Student ID</th>
            <th>Username</th>
            <th>Reward Points</th>
            <th>Total Marks</th>
            <th>Badge</th>
        </tr>
        <tr>
            <td><?php echo htmlspecialchars($student_id); ?></td>
            <td><?php echo htmlspecialchars($username); ?></td>
            <td><?php echo htmlspecialchars($rewardPoints); ?></td>
            <td><?php echo htmlspecialchars($totalMarks); ?></td>
            <td><?php echo htmlspecialchars($badge); ?></td>
        </tr>
    </table>

    <br>

    <!-- Button to take quiz -->
    <form action="quizs.php" method="get">
        <button type="submit">Take Quiz</button>
    </form>

    <br>

    <!-- Button to view leaderboard -->
    <form method="post">
        <button type="submit" name="viewLeaderboard">View Leaderboard</button>
    </form>

    <br>

    <!-- Show leaderboard table if the button was clicked -->
    <?php if ($viewLeaderboard): ?>
        <h3>Leaderboard (Sorted by Total Marks)</h3>
        <table border="1">
            <tr>
                <th>Student ID</th>
                <th>Username</th>
                <th>Reward Points</th>
                <th>Total Marks</th>
                <th>Badge</th>
            </tr>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                    <td><?php echo htmlspecialchars($student['username']); ?></td>
                    <td><?php echo htmlspecialchars($student['rewardPoints']); ?></td>
                    <td><?php echo htmlspecialchars($student['totalMarks']); ?></td>
                    <td><?php echo htmlspecialchars($student['badge']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <br>
    <a href="logout.php">Logout</a>
</body>
</html>
