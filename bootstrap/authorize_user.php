<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

$wsuser = getenv('WS_ADMIN_USERNAME');
$service_shortname = getenv('WS_SERVICE_SHORTNAME');

global $DB, $CFG;

$user = $DB->get_record('user', ['username' => $wsuser]);
$service = $DB->get_record('external_services', ['shortname' => $service_shortname]);

if (!$user || !$service) die("✖ Cannot authorize user\n");

if (!$DB->record_exists('external_services_users', [
    'userid' => $user->id,
    'externalserviceid' => $service->id
])) {
    $r = new stdClass();
    $r->userid = $user->id;
    $r->externalserviceid = $service->id;
    $r->timecreated = time();
    $DB->insert_record('external_services_users', $r);

    echo "✔ Authorized WS Admin\n";
}
