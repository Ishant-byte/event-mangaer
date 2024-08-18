<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = $_POST['location'];
    $organizer_id = $_SESSION['user_id'];

    $sql = "INSERT INTO events (title, description, date, time, location, organizer_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$title, $description, $date, $time, $location, $organizer_id]);
    header("Location: index.php");
    exit();
}

// Fetch venues
$sql = "SELECT id, name, address FROM venues";
$stmt = $conn->prepare($sql);
$stmt->execute();
$venues = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Host Event</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Host Event</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="host_event.php">Host Event</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="login-register-container">
            <h1>Host an Event</h1>
            <form method="post">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" required>
                <label for="time">Time:</label>
                <input type="time" id="time" name="time" required>
                <label for="location">Location:</label>
                <select id="location" name="location" required>
                    <option value="">Select a venue</option>
                    <?php foreach ($venues as $venue): ?>
                        <option value="<?php echo htmlspecialchars($venue['name'] . ', ' . $venue['address']); ?>">
                            <?php echo htmlspecialchars($venue['name'] . ', ' . $venue['address']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="create_event">Create Event</button>
            </form>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 Simple Event Manager</p>
    </footer>
</body>
</html>
