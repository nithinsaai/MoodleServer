<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
set_config('webserviceprotocols', 'rest');
echo "✔ REST protocol enabled\n";
