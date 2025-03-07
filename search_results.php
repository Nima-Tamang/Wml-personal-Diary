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

// Get search keyword
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

if (empty($keyword)) {
    outputWML("Please enter a search term");
    exit();
}

// Prepare and execute search query
$search_term = '%' . $keyword . '%';
$sql = "SELECT id, title, DATE_FORMAT(created_at, '%d-%m-%Y') as date FROM entries 
        WHERE title LIKE ? OR content LIKE ? 
        ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $search_term, $search_term);
$stmt->execute();
$result = $stmt->get_result();

// Start WML output
echo '<?xml version="1.0"?>';
echo '<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml">';
echo '<wml>';
echo '<card id="searchResults" title="Search Results">';
echo '<p>Results for: ' . htmlspecialchars($keyword) . '</p>';

// If no entries found
if ($result->num_rows == 0) {
    echo '<p>No matching entries found.</p>';
} else {
    // Display entries
    echo '<p>Found ' . $result->num_rows . ' entries:</p>';
    while ($row = $result->fetch_assoc()) {
        echo '<p>';
        echo $row['date'] . ': ';
        echo '<a href="view_entry.php?id=' . $row['id'] . '">' . htmlspecialchars($row['title']) . '</a><br/>';
        echo '</p>';
    }
}

// Navigation links
echo '<p>';
echo '<a href="search_entries.wml">Search Again</a><br/>';
echo '<a href="index.wml">Return to Home</a>';
echo '</p>';

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