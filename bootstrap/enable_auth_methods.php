<?php
// docker/bootstrap/enable_auth_methods.php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

echo "🔧 Enabling authentication methods...\n";

global $CFG, $DB;

/*
 * Email-based self-registration (auth = 'email')
 * This requires enabling the plugin and setting registerauth = email
 */
echo "➡ Enabling email-based self-registration...\n";
set_config('registerauth', 'email');            // Allow self-registration
set_config('auth_email_autocreate', 1, 'auth/email'); // Auto-create accounts
set_config('recaptchachallenge', 0);            // Optional: disable captcha for dev

// Enable the plugin in the list of enabled auth methods
$enabled = explode(',', $CFG->auth);
if (!in_array('email', $enabled)) {
    $enabled[] = 'email';
    set_config('auth', implode(',', array_filter($enabled)));
}
echo "✔ Email-based self-registration enabled\n";

/*
 * Web services authentication (auth = 'webservice')
 * Required for external service login/token access
 */
echo "➡ Enabling Web Services authentication...\n";
if (!in_array('webservice', $enabled)) {
    $enabled[] = 'webservice';
    set_config('auth', implode(',', array_filter($enabled)));
}

set_config('oauth2issuer', '', 'auth/webservice');  // No issuer required
set_config('enablewsdocumentation', 1);             // Optional, useful for debugging

echo "✔ Web Services authentication enabled\n";

echo "🎉 Authentication bootstrap complete.\n";
