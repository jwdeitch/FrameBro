<?php

/*
* Error reporting on/off
*/
if (getenv("ENV") == 'dev') {
    error_reporting(E_ALL);
    ini_set('display_errors', TRUE);
    ini_set('display_startup_errors', TRUE);
}

//Define various paths.
define('MODELS_PATH', ABSOLUTE_PATH . '/app/models/');
define('CORE_PATH', ABSOLUTE_PATH . '/app/core/');
define('VIEWS_PATH', ABSOLUTE_PATH . '/app/views/');
define('PUBLIC_PATH', ABSOLUTE_PATH . '/public/');
define('THEMES_PATH', PUBLIC_PATH . '/themes/');
define('STORAGE_PATH', ABSOLUTE_PATH . '/app/storage/');
define('FILES_PATH', PUBLIC_PATH . 'files/');
define ('DOCUMENT_ROOT', dirname(__FILE__));

define('ROUTER_FILE', ABSOLUTE_PATH . '/app/config/router.php');

/**
 * Configuration for: Hashing strength
 * This is the place where you define the strength of your password hashing/salting
 *
 * To make password encryption very safe and future-proof, the PHP 5.5 hashing/salting functions
 * come with a clever so called COST FACTOR. This number defines the base-2 logarithm of the rounds of hashing,
 * something like 2^12 if your cost factor is 12. By the way, 2^12 would be 4096 rounds of hashing, doubling the
 * round with each increase of the cost factor and therefore doubling the CPU power it needs.
 * Currently, in 2013, the developers of this functions have chosen a cost factor of 10, which fits most standard
 * server setups. When time goes by and server power becomes much more powerful, it might be useful to increase
 * the cost factor, to make the password hashing one step more secure. Have a look here
 * (@see https://github.com/panique/php-login/wiki/Which-hashing-&-salting-algorithm-should-be-used-%3F)
 * in the BLOWFISH benchmark table to get an idea how this factor behaves. For most people this is irrelevant,
 * but after some years this might be very very useful to keep the encryption of your database up to date.
 *
 * Remember: Every time a user registers or tries to log in (!) this calculation will be done.
 * Don't change this if you don't know what you do.
 *
 * To get more information about the best cost factor please have a look here
 * @see http://stackoverflow.com/q/4443476/1114320
 */

/**
 * the hash cost factor, PHP's internal default is 10. You can leave this line
 * commented out until you need another factor then 10.
 */
define("HASH_COST_FACTOR", "10");


//Time zone
date_default_timezone_set('America/New_York');

/**
 * Declare base URL as global variable
 * This includes the directory in which the app is located, if any.
 */
if (isset($_SERVER['HTTP_HOST'])) {
    $GLOBALS['base_url'] = $_SERVER['HTTP_HOST'] . substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT']));
}

ini_set("error_log" , STORAGE_PATH . '/logging/app-errors.log');