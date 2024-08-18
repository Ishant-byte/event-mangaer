<?php
session_start();
include 'db.php';

// Fetch events for all users to display on the page
$stmt = $conn->prepare("SELECT * FROM events");
$stmt->execute();
$events = $stmt->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_event'])) {
    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['user_id'];

    // Verify that the logged-in user is the organizer of the event
    $sql = "SELECT * FROM events WHERE id = ? AND organizer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$event_id, $user_id]);
    $event = $stmt->fetch();

    if ($event) {
        // User is authorized to delete the event
        $sql = "DELETE FROM events WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$event_id]);
    }

    // Redirect to the same page to see the updated list of events
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Event Manager</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Simple Event Manager</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="host_event.php">Host Event</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main class="home-page">
        <?php if (isset($_SESSION['user_id'])): ?>
            <h2>Upcoming Events</h2>
            <div class="events">
                <?php foreach ($events as $event): ?>
                    <div class="event">
                        <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                        <p><?php echo htmlspecialchars($event['description']); ?></p>
                        <p><strong>Date:</strong> <?php echo htmlspecialchars($event['date']); ?></p>
                        <p><strong>Time:</strong> <?php echo htmlspecialchars($event['time']); ?></p>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
                        <?php if ($_SESSION['user_id'] == $event['organizer_id']): ?>
                            <form method="post" class="actions">
                                <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                <button type="submit" name="delete_event">Delete Event</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <section class="welcome">
                <h2>Welcome to the Simple Event Manager</h2>
                <p>Manage your events effortlessly with our user-friendly platform.</p>
                <a href="login.php" class="button">Login</a>
                <a href="register.php" class="button">Register</a>
            </section>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; 2024 Simple Event Manager</p>
    </footer>
</body>
</html>