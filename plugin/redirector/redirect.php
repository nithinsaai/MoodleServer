<?php
require('../../config.php');

$next = optional_param('next', '', PARAM_URL); // Full URL

redirect($next);
