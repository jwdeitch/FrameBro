<?php
/**
 * Created By: Jon Garcia
 * Date: 1/16/16
 **/
namespace App\Core;

use App\Core\Http\Routes;

/**
 * Class App
 * @package App\Core
 */
class App {

	private $caller;

	/**
	 * App constructor.
	 */
	public function __construct() {
		Session::init();

		//TODO move to a cron
		self::cleanLogFile();
        //sets some env variables
        self::SetXattrSupport();

		$this->caller = isset($_SERVER['SHELL']) ? 'CLI' : 'WEB';

		//if it's a web request not a cli request.
		if ($this->caller === 'WEB') {
            $routes = new Routes();
            if ($routes->parseRoutes()) {
				if ($routes->controller === 'callable') {
                    $routes->arguments = $routes->arUri ? array_values($routes->arUri) : array();
					call_user_func_array($routes->action, $routes->arguments);
				} elseif ($routes->validateRoutes()) {
					$this->fireApp($routes);
				} else {
					throw new \Exception('Your routes file could not be validated');
				}
			} else {
                $routes->callMissingPage();
			}
		}
	}

	/**
     * @param $routes Routes object type
	 *
     */
	private function fireApp(Routes $routes)
	{
		try {
            $routes->arguments = $routes->arUri ? array_values($routes->arUri) : array();
			call_user_func_array( array( $routes->controller, $routes->action) , $routes->arguments );
		}
		catch (\Exception $e) {
			if (getenv('ENV') === 'dev') {
				ddd($e);
			}
			else {
				echo 'An error has occurred';
			}
		}
	}

	/**
	 * cleanLogFile
     * TODO move to a cron
	 */
	public static function cleanLogFile()
	{
		$log = STORAGE_PATH . 'logging/app-errors.log';

		if (file_exists($log) && filesize($log) >= 100000) {
			$file = file($log);
			$file = array_splice($file, -500, 500);
			$handle = fopen($log, 'w');
			fwrite($handle, implode("", $file));
		}
	}

	/**
	 * SetXattrSupport
	 */
	private static function SetXattrSupport() {
        $supportsExtendedAttr = (int)extension_loaded('xattr');
		putenv("XATTR_SUPPORT=$supportsExtendedAttr");
        $osExtendedAttr = $supportsExtendedAttr ? (int)xattr_supported(FILES_PATH . 'README.txt') : 0;
		putenv("XATTR_ENABLED=$osExtendedAttr");
	}
}