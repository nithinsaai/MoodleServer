<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

$wsuser  = getenv('WS_ADMIN_USERNAME');
$wspass  = getenv('WS_ADMIN_PASSWORD');
$wsemail = getenv('WS_ADMIN_EMAIL');
$wsfirstname = getenv('WS_ADMIN_FIRSTNAME');
$wslastname = getenv('WS_ADMIN_LASTNAME');

global $CFG, $DB;
require_once("$CFG->dirroot/user/lib.php");

// Check user
$user = $DB->get_record('user', [
    'username' => $wsuser,
    'mnethostid' => $CFG->mnet_localhost_id
]);

if (!$user) {
    $u = new stdClass();
    $u->username   = $wsuser;
    $u->password   = $wspass;
    $u->firstname  = $wsfirstname;
    $u->lastname   = $wslastname;
    $u->email      = $wsemail;
    $u->auth       = 'manual';
    $u->confirmed  = 1;
    $u->mnethostid = $CFG->mnet_localhost_id;

    $userid = user_create_user($u);
    echo "✔ WS Admin created (id=$userid)\n";
} else {
    $userid = $user->id;
    echo "✔ WS Admin exists (id=$userid)\n";
}

// Promote to admin
$admins = explode(',', $CFG->siteadmins);
if (!in_array($userid, $admins)) {
    $admins[] = $userid;
    set_config('siteadmins', implode(',', $admins));
    echo "✔ WS Admin promoted to site admin\n";
}
