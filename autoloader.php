<?php
/**
 * Created by PhpStorm.
 * User: Simen Bai
 * Date: 06-Oct-18
 * Time: 17:51
 */


global $user;
$user = null;
global $ini;
$ini = parse_ini_file(__dir__.'/includes/settings.ini');

require_once __DIR__ . '/includes/error.php';
require_once __DIR__.'/includes/mySQLDatabase.php';
require_once __DIR__.'/includes/sqlHandeler.php';

require_once __DIR__.'/includes/restHandeler.php';
