<?php
// docker/bootstrap/configure_outgoing_mail.php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

echo "🔧 Configuring outgoing SMTP mail...\n";

/*
 * Moodle SMTP settings:
 *
 *  smtphosts      → host:port
 *  smtpsecure     → '' | ssl | tls
 *  smtpauthtype   → 'PLAIN' | 'LOGIN'
 *  smtpuser       → username
 *  smtppass       → password
 *  noreplyaddress → default no-reply addr
 */

// 1) SMTP Host (security: none → no ssl/tls)
set_config('smtphosts', 'smtp.freesmtpservers.com');
set_config('smtpsecure', '');  // none
echo "✔ SMTP host set to smtp.freesmtpservers.com\n";

// 2) SMTP Authentication (none → so disable auth)
set_config('smtpauthtype', 'PLAIN');
set_config('smtpuser', '');
set_config('smtppass', '');
echo "✔ SMTP authentication disabled (plain, no username/password)\n";

// 3) No-reply address
set_config('noreplyaddress', 'nithin.saai.shiva@gmail.com');
echo "✔ No-reply email set to nithin.saai.shiva@gmail.com\n";

echo "🎉 Outgoing mail configured.\n";
