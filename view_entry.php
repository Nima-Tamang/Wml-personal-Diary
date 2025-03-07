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

// Get entry ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    outputWML("Invalid entry ID");
    exit();
}

// Prepare and execute query
$sql = "SELECT title, content, DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') as date FROM entries WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Start WML output
echo '<?xml version="1.0"?>';
echo '<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml">';
echo '<wml>';

if ($result->num_rows == 0) {
    echo '<card id="notFound" title="Entry Not Found">';
    echo '<p>Entry not found.</p>';
} else {
    $row = $result->fetch_assoc();
    echo '<card id="entry' . $id . '" title="' . htmlspecialchars($row['title']) . '">';
    echo '<p><small>Date: ' . $row['date'] . '</small></p>';
    echo '<p><b>' . htmlspecialchars($row['title']) . '</b></p>';
    echo '<p>' . nl2br(htmlspecialchars($row['content'])) . '</p>';
    
    // Add edit and delete options
    echo '<p>';
    echo '<a href="edit_entry.wml?id=' . $id . '">Edit this entry</a><br/>';
    echo '<a href="delete_entry.php?id=' . $id . '">Delete this entry</a>';
    echo '</p>';
}

// Add link to return to all entries
echo '<p><a href="fetch_entries.php?page=1">Back to All Entries</a></p>';

// Back button
echo '<do type="prev" label="Back">';
echo '<prev/>';
echo '</do>';

echo '</card>';
echo '</wml>';

// Close connection
$stmt->close();
$conn->close();

// Helper function to output error messages
function outputWML($message) {
    echo '<?xml version="1.0"?>';
    echo '<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml">';
    echo '<wml><card><p>' . $message . '</p>';
    echo '<do type="accept" label="Back"><prev/></do></card></wml>';
}
?>