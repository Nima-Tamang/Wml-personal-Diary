<?php
// Set content type for WML
header("Content-type: text/vnd.wap.wml");

// Database connection parameters
$servername = "localhost:8080";
$username = "root";
$password = "";
$dbname = "diaryDB";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    outputWML("Connection failed: " . $conn->connect_error);
    exit();
}

// Get form data
$title = isset($_POST['title']) ? $_POST['title'] : '';
$content = isset($_POST['content']) ? $_POST['content'] : '';

// Validate input
if (empty($title) || empty($content)) {
    outputWML("Title and content cannot be empty.");
    exit();
}

// Prepare and execute SQL query
$stmt = $conn->prepare("INSERT INTO entries (title, content) VALUES (?, ?)");
$stmt->bind_param("ss", $title, $content);

if ($stmt->execute()) {
    // Success response
    echo '<?xml version="1.0"?>';
    echo '<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml">';
    echo '<wml><card><p>Entry added successfully!</p>';
    echo '<do type="accept" label="Home"><go href="index.wml"/></do></card></wml>';
} else {
    // Error response
    outputWML("Error: " . $stmt->error);
}

// Close connection
$stmt->close();
$conn->close();

// Helper function to output error messages
function outputWML($message) {
    echo '<?xml version="1.0"?>';
    echo '<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml">';
    echo '<wml><card><p>' . $message . '</p>';
    echo '<do type="accept" label="Try Again"><prev/></do></card></wml>';
}
?>