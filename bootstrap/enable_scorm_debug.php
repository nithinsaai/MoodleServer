<?php
// docker/bootstrap/enable_scorm_debug.php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

echo "🔧 Enabling SCORM API debug mode...\n";

/*
 * Moodle SCORM debug settings live under plugin 'scorm'
 * Same names used in admin settings:
 *
 * scorm | allowapidebug       → Allow API debug and tracing
 */

set_config('allowapidebug', 1, 'scorm');
