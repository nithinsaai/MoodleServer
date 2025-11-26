<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
set_config('enablewebservices', 1);
echo "✔ Web services enabled\n";
