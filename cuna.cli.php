<?php

use App\Core\Http\Routes;

$action = $argv[1];
unset($argv[0]);
unset($argv[1]);

define('ABSOLUTE_PATH', getcwd());

require ABSOLUTE_PATH . '/vendor/autoload.php';
require ABSOLUTE_PATH . '/app/config/config.php';
require ABSOLUTE_PATH . '/app/core/autoload.php';

if ($action === 'routes') {
	$routes = new Routes();
	$patterns = [ '@.*\s+\[|\]|string|\(\d+\)@', '@\n{2}@', '@\$routes::getRoutes\(\)@', '@Called from\s\+\d+\s[a-z\/\.]+\.php@i' ];
	$replacements = [ "", "\n", "      Cuna Framework", "" ];
	echo preg_replace($patterns, $replacements , @Kint::dump($routes::getRoutes()));
}