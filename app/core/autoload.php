<?php

/**
 * the auto-loading function, which will be called every time a file "is missing"
 * NOTE: don't get confused, this is not "__autoload", the now deprecated function
 * The PHP Framework Interoperability Group (@see https://github.com/php-fig/fig-standards) recommends using a
 * standardized auto-loader https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md, so we do:
 *
 * @param $class string the class to load.
 */
function autoload( $class) {
    $dirs = explode('\\', $class);
    $class = array_pop($dirs);
    $path = strtolower(implode(DIRECTORY_SEPARATOR, $dirs));
    $namespaceClass = ABSOLUTE_PATH . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $class . '.php';

    if (file_exists( $namespaceClass )) {
        require_once ( $namespaceClass );
    } elseif (file_exists(CORE_PATH . $class . ".php")) {
        require_once CORE_PATH . $class . ".php";
    }
}

// spl_autoload_register defines the function that is called every time a file is missing. as we created this
// function above, every time a file is needed, autoload(THENEEDEDCLASS) is called
spl_autoload_register("autoload");