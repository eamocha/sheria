<?php

// debug_path.php

$logFile = '/var/log/nginx/simplesamlphp_debug.log'; // Ensure Nginx user has write access to this file/directory

file_put_contents($logFile, "--- " . date('Y-m-d H:i:s') . " ---\n", FILE_APPEND);

file_put_contents($logFile, "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n", FILE_APPEND);

file_put_contents($logFile, "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . "\n", FILE_APPEND);

file_put_contents($logFile, "PHP_SELF: " . ($_SERVER['PHP_SELF'] ?? 'N/A') . "\n", FILE_APPEND);

file_put_contents($logFile, "PATH_INFO: " . ($_SERVER['PATH_INFO'] ?? 'N/A') . "\n", FILE_APPEND);

file_put_contents($logFile, "QUERY_STRING: " . ($_SERVER['QUERY_STRING'] ?? 'N/A') . "\n", FILE_APPEND);

file_put_contents($logFile, "--- END ---\n\n", FILE_APPEND);


echo "SimpleSAMLphp Debug Script - Check /var/log/nginx/simplesamlphp_debug.log for details.";

?>
