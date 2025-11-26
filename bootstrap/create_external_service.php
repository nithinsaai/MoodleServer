<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

$service_shortname = getenv('WS_SERVICE_SHORTNAME');
$service_name      = getenv('WS_SERVICE_NAME');
$authorized_only   = getenv('WS_SERVICE_AUTHORIZED_ONLY');  // 1 = only authorized users, 0 = open to all

global $DB;

$service = $DB->get_record('external_services', ['shortname' => $service_shortname]);

if (!$service) {
    $s = new stdClass();
    $s->name = $service_name;
    $s->shortname = $service_shortname;
    $s->enabled = 1;
    $s->restrictedusers = $authorized_only ? 1 : 0;;
    $s->downloadfiles = 1;
    $s->uploadfiles = 1;
    $s->timecreated = time();
    $DB->insert_record('external_services', $s);

    echo "✔ External service created\n";
} else {
    echo "✔ External service already exists\n";
}
