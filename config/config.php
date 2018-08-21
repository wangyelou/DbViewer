<?php
define('PROJECT_NAME','DbViewer');
define('SEPATATOR', strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'?'\\':'/');
define('BASE_PATH',dirname(dirname(__FILE__)).SEPATATOR);
define('EXT','php');
define('DBCONFIGS', json_decode(file_get_contents(BASE_PATH . 'config' . SEPATATOR . 'dbconfig.ini'), true));