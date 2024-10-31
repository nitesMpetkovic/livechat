<?php
session_start();
$servername = "localhost";
$username = "root";
$password = ""; // stavi svoju lozinku ovde
$dbname = "chat_db";
$conn = new mysqli($servername, $username, $password, $dbname);

// Proveri vezu sa bazom podataka
if ($conn->connect_error) {
    die("Neuspešna konekcija: " . $conn->connect_error);
}

// Funkcija za učitavanje korisnika
function loadUsers($conn) {
    $sql = "SELECT id, first_name, last_name FROM users";
    $result = $conn->query($sql);
    $users = [];
    
    while ($user = $result->fetch_assoc()) {
        $users[] = $user;
    }
    return $users;
}

// Funkcija za učitavanje poruka
function loadMessages($conn) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT c.message, u.first_name, u.last_name 
            FROM chat c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.recipient_id = $user_id OR c.user_id = $user_id 
            ORDER BY c.id ASC";
    
    // Izvrši upit i proveri grešku
    $result = $conn->query($sql);
    
    if (!$result) {
        die("Greška u upitu: " . $conn->error . " | SQL: " . $sql);
    }
    
    $messages = [];

    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    return $messages;
}

// Funkcija za slanje poruke
function sendMessage($conn, $user_id, $recipient_id, $message) {
    $sql = "INSERT INTO chat (user_id, message, recipient_id) VALUES ('$user_id', '$message', '$recipient_id')";
    return $conn->query($sql);
}

// Proveri da li je korisnik prijavljen
if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit();
}

// Obradi slanje poruke
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['message'], $_POST['recipient'])) {
    sendMessage($conn, $_SESSION['user_id'], $_POST['recipient'], $_POST['message']);
}

// Učitaj korisnike i poruke
$users = loadUsers($conn);
$messages = loadMessages($conn);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Chat</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Live Chat</h1>
    <div id="chat-box" style="border: 1px solid #ccc; padding: 10px; height: 300px; overflow-y: scroll;">
        <?php foreach ($messages as $msg): ?>
            <p><strong><?php echo htmlspecialchars($msg['first_name'] . " " . $msg['last_name']); ?>:</strong> <?php echo htmlspecialchars($msg['message']); ?></p>
        <?php endforeach; ?>
    </div>

    <form id="message-form" action="" method="post" style="margin-top: 10px;">
        <select name="recipient" id="recipient">
            <?php foreach ($users as $user): ?>
                <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                    <option value="<?php echo $user['id']; ?>">
                        <?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
        <textarea name="message" id="message" placeholder="Unesi poruku"></textarea>
        <button type="submit">Pošalji</button>
    </form>

    <form action="logout.php" method="post" style="margin-top: 10px;">
        <button type="submit">Odjavi se</button>
    </form>
    
    <script src="js/chat.js"></script>
</body>
</html>
