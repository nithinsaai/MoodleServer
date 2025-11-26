<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
set_config('enablewsdocumentation', 1);
echo "✔ Developer docs enabled\n";
