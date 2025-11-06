<?php
unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = getenv('MOODLE_DBTYPE');
$CFG->dblibrary = 'native';
$CFG->dbhost    = getenv('MOODLE_DBHOST');
$CFG->dbname    = getenv('MOODLE_DBNAME');
$CFG->dbuser    = getenv('MOODLE_DBUSER');
$CFG->dbpass    = getenv('MOODLE_DBPASS');
$CFG->prefix    = 'mdl_';

$CFG->dataroot  = '/var/www/moodledata';
$CFG->admin     = 'admin';
$CFG->directorypermissions = 0777;

$host = getenv('HOST_IP') ?: '127.0.0.1';
$CFG->wwwroot = "http://{$host}";

require_once(__DIR__ . '/lib/setup.php');
