<?php

error_reporting(E_NONE);
ini_set('display_errors', 0);

define('BASE_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('WEB_DIR', BASE_DIR);
define('INCLUDE_DIR', BASE_DIR.'include'.DIRECTORY_SEPARATOR);

require_once(INCLUDE_DIR.'config.php');
require_once(INCLUDE_DIR.'functions.php');

@date_default_timezone_set('Europe/London');
