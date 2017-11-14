<?php
set_time_limit(0);

define('IS_SHELL', 1);

$cmd = trim($_SERVER['argv'][1]);

$_SERVER['PATH_INFO'] = $cmd;
$_SERVER['REQUEST_URI'] = trim($_SERVER['argv'][0]) . '/' .$cmd;
define('ENVIRONMENT', (IS_SHELL == 1) ? 'product' : 'production');
include(realpath(dirname(__FILE__))."/index.php");