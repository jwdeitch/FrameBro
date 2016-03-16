<?php

require '../root.php';
require ABSOLUTE_PATH . '/vendor/autoload.php';

if (file_exists(ABSOLUTE_PATH . '/.env')) {
    $dotenv = new Dotenv\Dotenv( ABSOLUTE_PATH );
    $dotenv->load();
}

//core files including config file
require 'config/config.php';
require 'core/autoload.php';
