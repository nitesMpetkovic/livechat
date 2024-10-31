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

// Proveri da li su podaci poslati
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Proveri korisničko ime i lozinku
    $stmt = $conn->prepare("SELECT id, first_name, last_name FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        header("Location: index.php"); // Redirekcija na index.php
        exit();
    } else {
        // Ako su podaci pogrešni
        header("Location: login.html?error=incorrect"); // Vraća na login sa greškom
        exit();
    }
}

$conn->close();
?>
