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
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ubaci novog korisnika u bazu
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $first_name, $last_name, $username, $password);
    
    if ($stmt->execute()) {
        header("Location: login.html"); // Preusmeri na stranicu za prijavu nakon registracije
        exit();
    } else {
        echo "Greška prilikom registracije: " . $conn->error;
    }
}

$conn->close();
?>
