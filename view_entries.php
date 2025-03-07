<?php
// Database connection
$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "diaryDB"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all diary entries
$sql = "SELECT title, content FROM entries";
$result = $conn->query($sql);

// Start the WML document
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<wml>';
echo '<card title="Diary Entries">';

// Display entries in WML format
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Display each diary entry
        echo '<p><b>' . htmlspecialchars($row["title"]) . ':</b> ' . htmlspecialchars($row["content"]) . '</p>';
    }
} else {
    echo '<p>No entries found.</p>';
}

echo '<do type="accept" label="Back">';
echo '<go href="index.wml"/>';
echo '</do>';

echo '</card>';
echo '</wml>';

// Close connection
$conn->close();
?>
