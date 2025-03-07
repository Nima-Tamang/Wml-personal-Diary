<?php
// Database connection
$servername = "localhost:8080";
$username = "root";
$password = "";
$dbname = "diaryDB";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the entry ID from the query string
$entry_id = $_GET['id'];

// Delete the entry from the database
$sql = "DELETE FROM entries WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $entry_id);

if ($stmt->execute()) {
    echo "Entry deleted successfully!";
} else {
    echo "Error deleting entry: " . $conn->error;
}

// Close connection
$stmt->close();
$conn->close();

// Redirect back to view entries page
header("Location: view_entries.wml");
exit();
?>
