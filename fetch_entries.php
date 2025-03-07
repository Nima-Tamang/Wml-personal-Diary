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

// Get current page number
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$entries_per_page = 5; // Show 5 entries per page
$offset = ($page - 1) * $entries_per_page;

// Get total count of entries
$count_query = "SELECT COUNT(*) as total FROM entries";
$count_result = $conn->query($count_query);
$count_row = $count_result->fetch_assoc();
$total_entries = $count_row['total'];
$total_pages = ceil($total_entries / $entries_per_page);

// Query to get entries for current page
$sql = "SELECT id, title, DATE_FORMAT(created_at, '%d-%m-%Y') as date FROM entries ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $entries_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Start WML output
echo '<?xml version="1.0"?>';
echo '<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml">';
echo '<wml>';
echo '<card id="entryList" title="Diary Entries - Page ' . $page . '">';
echo '<p>Your Diary Entries (' . $total_entries . ' total):</p>';

// If no entries found
if ($result->num_rows == 0) {
    echo '<p>No entries found.</p>';
} else {
    // Display entries
    while ($row = $result->fetch_assoc()) {
        echo '<p>';
        echo $row['date'] . ': ';
        echo '<a href="view_entry.php?id=' . $row['id'] . '">' . htmlspecialchars($row['title']) . '</a><br/>';
        echo '</p>';
    }
    
    // Pagination links
    echo '<p>';
    if ($page > 1) {
        echo '<a href="fetch_entries.php?page=' . ($page - 1) . '">Previous</a> ';
    }
    if ($page < $total_pages) {
        echo '<a href="fetch_entries.php?page=' . ($page + 1) . '">Next</a>';
    }
    echo '</p>';
}

// Add link to return to home
echo '<p><a href="index.wml">Return to Home</a></p>';

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