<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die('Please log in first');
}

echo "<h1>PHP Upload Configuration</h1>";

echo "<h3>File Upload Settings:</h3>";
echo "file_uploads: " . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . "<br>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "<br>";
echo "max_execution_time: " . ini_get('max_execution_time') . " seconds<br>";
echo "max_input_time: " . ini_get('max_input_time') . " seconds<br>";
echo "memory_limit: " . ini_get('memory_limit') . "<br>";

echo "<h3>Upload Directory Info:</h3>";
$upload_dir = 'uploads/';
echo "Upload directory: " . realpath($upload_dir) . "<br>";
echo "Directory exists: " . (file_exists($upload_dir) ? 'Yes' : 'No') . "<br>";
echo "Directory writable: " . (is_writable($upload_dir) ? 'Yes' : 'No') . "<br>";
echo "Directory permissions: " . substr(sprintf('%o', fileperms($upload_dir)), -4) . "<br>";

echo "<h3>Temp Directory Info:</h3>";
echo "sys_get_temp_dir(): " . sys_get_temp_dir() . "<br>";
echo "upload_tmp_dir: " . ini_get('upload_tmp_dir') . "<br>";

echo "<h3>Error Reporting:</h3>";
echo "error_reporting: " . error_reporting() . "<br>";
echo "display_errors: " . ini_get('display_errors') . "<br>";
echo "log_errors: " . ini_get('log_errors') . "<br>";
echo "error_log: " . ini_get('error_log') . "<br>";

echo "<hr>";
echo "<a href='test-upload.php'>Test File Upload</a><br>";
echo "<a href='create-post.php'>Back to Create Post</a>";
?>
