<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

$service_shortname = getenv('WS_SERVICE_SHORTNAME');
$functions = array_filter(array_map('trim', explode(',',
    getenv('WS_FUNCTIONS') ?: 'core_webservice_get_site_info'
)));

global $DB;

$service = $DB->get_record('external_services', ['shortname' => $service_shortname]);
if (!$service) die("✖ Service not found\n");

foreach ($functions as $fname) {
    $f = $DB->get_record('external_functions', ['name' => $fname]);
    if (!$f) continue;

    if (!$DB->record_exists('external_services_functions', [
        'externalserviceid' => $service->id,
        'functionname' => $fname
    ])) {
        $m = new stdClass();
        $m->externalserviceid = $service->id;
        $m->functionname = $fname;
        $DB->insert_record('external_services_functions', $m);
        echo "✔ Added function: $fname\n";
    }
}
