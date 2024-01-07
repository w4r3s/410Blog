<?php
session_start();

// Ensure the user is logged in and has admin privileges.
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../config.php';

// Clear any previous output buffer
ob_end_clean();

// Define the name of the backup file
$backupFileName = date('Y-m-d-H-i-s') . '-' . str_replace(' ', '-', BLOG_TITLE) . '-database-backup.sql';

// Set the content type to SQL and suggest a filename for the download
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $backupFileName . '"');

// Define the path to the temporary options file
$tempOptionsFile = tempnam(sys_get_temp_dir(), 'mysqldump');

// Write the options to the file
$optionsContent = "[mysqldump]\nuser=" . DB_USER . "\npassword=" . DB_PASS . "\nhost=" . DB_HOST;
file_put_contents($tempOptionsFile, $optionsContent);

// Command to perform the backup using the temporary options file
$command = "mysqldump --defaults-extra-file=" . escapeshellarg($tempOptionsFile) . " " . escapeshellarg(DB_NAME);

// Execute the command and output the result directly to the browser
passthru($command);

// Remove the temporary options file
unlink($tempOptionsFile);

// Terminate the script to prevent any further output
exit();
