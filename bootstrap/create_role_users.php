<?php
// docker/bootstrap/create_role_users.php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

global $DB, $CFG;
require_once("$CFG->dirroot/user/lib.php");
require_once("$CFG->libdir/accesslib.php");

echo "👤 Creating users for each Moodle archetype...\n";

$syscontext = context_system::instance();

// Roles to create (username => shortname)
$rolemap = [
    'manageruser'        => 'manager',
    'coursecreatoruser'  => 'coursecreator',
    'editingteacheruser' => 'editingteacher',
    'teacheruser'        => 'teacher',
    'guestuser'          => 'guest',
];

// Default password
$pwd = 'Welcome@123';

// Helper to create + assign role
function create_and_assign($username, $role_shortname, $pwd, $syscontext) {
    global $DB, $CFG;

    // Look up role
    $role = $DB->get_record('role', ['shortname' => $role_shortname]);
    if (!$role) {
        echo "❌ Role '$role_shortname' not found — skipping\n";
        return;
    }

    // Check user
    $user = $DB->get_record('user', [
        'username' => $username,
        'mnethostid' => $CFG->mnet_localhost_id
    ]);

    if (!$user) {
        $u = new stdClass();
        $u->username  = $username;
        $u->password  = $pwd;
        $u->firstname = ucfirst($username);
        $u->lastname  = 'User';
        $u->email     = "$username@example.com";
        $u->auth      = 'manual';
        $u->confirmed = 1;
        $u->mnethostid = $CFG->mnet_localhost_id;
        $userid = user_create_user($u);
        echo "✔ Created user '$username' (id=$userid)\n";
    } else {
        $userid = $user->id;
        echo "✔ User '$username' already exists (id=$userid)\n";
    }

    // Assign role
    if (!$DB->record_exists('role_assignments', [
        'roleid' => $role->id, 'userid' => $userid, 'contextid' => $syscontext->id
    ])) {
        role_assign($role->id, $userid, $syscontext->id);
        echo "   ➕ Assigned role '$role_shortname' to '$username'\n";
    } else {
        echo "   ✔ '$username' already has role '$role_shortname'\n";
    }
}

// Create one user per role
foreach ($rolemap as $user => $role) {
    create_and_assign($user, $role, $pwd, $syscontext);
}

// Now handle 5 student users
echo "\n👥 Creating 5 student users...\n";

$student_role = $DB->get_record('role', ['shortname' => 'student']);
if (!$student_role) {
    echo "❌ Role 'student' not found — stopping student creation.\n";
    exit;
}

for ($i = 1; $i <= 5; $i++) {
    $username = "student$i";

    $user = $DB->get_record('user', [
        'username' => $username,
        'mnethostid' => $CFG->mnet_localhost_id
    ]);

    if (!$user) {
        $u = new stdClass();
        $u->username  = $username;
        $u->password  = $pwd;
        $u->firstname = "Student";
        $u->lastname  = "$i";
        $u->email     = "$username@example.com";
        $u->auth      = 'manual';
        $u->confirmed = 1;
        $u->mnethostid = $CFG->mnet_localhost_id;
        $userid = user_create_user($u);
        echo "✔ Created '$username' (id=$userid)\n";
    } else {
        $userid = $user->id;
        echo "✔ '$username' already exists (id=$userid)\n";
    }

    if (!$DB->record_exists('role_assignments', [
        'roleid' => $student_role->id, 'userid' => $userid, 'contextid' => $syscontext->id
    ])) {
        role_assign($student_role->id, $userid, $syscontext->id);
        echo "   ➕ Assigned role 'student' to '$username'\n";
    } else {
        echo "   ✔ '$username' already has role 'student'\n";
    }
}

echo "\n🎉 Finished creating role users.\n";
