<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Server is Working!</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Current Directory: " . __DIR__ . "</p>";

// Check if files exist
echo "<h3>Checking Important Files:</h3>";
$files_to_check = ['db_connect.php', 'login.php', 'pages/dashboard.php'];
foreach($files_to_check as $file) {
    if(file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file MISSING<br>";
    }
}

// List all files
echo "<h3>All Files in this folder:</h3>";
echo "<ul>";
$files = scandir(__DIR__);
foreach($files as $file) {
    if($file != '.' && $file != '..') {
        $size = filesize($file);
        echo "<li>" . $file . " (" . $size . " bytes)</li>";
    }
}
echo "</ul>";
?>