<?php
// docker/bootstrap/enable_token_creation_for_authenticated_users.php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

global $DB, $CFG;

require_once("$CFG->dirroot/lib/accesslib.php");

echo "🔧 Enabling token creation for ALL authenticated users...\n";

// Fetch the built-in "Authenticated user" role
$role = $DB->get_record('role', ['shortname' => 'user']);

if (!$role) {
    echo "✖ ERROR: Could not find role 'user'.\n";
    exit(1);
}

$roleid = $role->id;
echo "✔ Found role 'user' (id=$roleid)\n";

// Assign capability
$syscontext = context_system::instance();

assign_capability(
    'moodle/webservice:createtoken',
    CAP_ALLOW,
    $roleid,
    $syscontext->id,
    true   // overwrite if previously set
);

echo "✔ Capability 'moodle/webservice:createtoken' granted to all authenticated users.\n";
echo "🎉 Any logged-in user can now create their own web service tokens.\n";
