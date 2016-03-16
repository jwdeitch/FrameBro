<?php
/**
 * Author: Jon Garcia
 * Date: 2/5/16
 * Time: 9:25 AM
 */

namespace App\Core\Http;

use App\Core\Interpreter;

/**
 * Class Routes
 * @package App\Core\Http
 */
class Routes
{
    private static $routes = array();
    private static $missing;
    public $uri;
    public $arUri = array();

    public $arguments = array();
    public $controller;
    public $action = 'index';

    /**
     * Routes constructor.
     */
    public function __construct()
    {
        $this->arUri = $this->parseURL();
        $this->includeRouter();
    }

    /**
     *
     */
    public function callMissingPage()
    {
        if ( !empty( self::$missing ) ) {
            call_user_func( self::$missing );
        } else {
            !ddd( ['No controller', debug_backtrace()] );
        }
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function validateRoutes()
    {
        $result = false;
        if (file_exists(ABSOLUTE_PATH . '/app/controllers/' . $this->controller . 'Controller.php')) {
            $this->controller = "\\App\\Controllers\\" . $this->controller . 'Controller';
        }
        elseif (file_exists(ABSOLUTE_PATH . '/app/controllers/' . $this->controller . '.php')) {
            $this->controller = "\\App\\Controllers\\" . $this->controller;
        }

        $this->controller = new $this->controller(); //throws exception if not exists.

        if( method_exists($this->controller, $this->action )) {
            $result = true;
        }
        else {
            throw new \Exception("Invalid method $this->action");
        }
        return $result;
    }

    /**
     * @param $route
     * @param $endpoint
     * @param $via array
     */
    private static function get( $route, $endpoint, array $via = array('via' => NULL) ) {
        self::all($route, $endpoint, $via, 'GET');
    }

    /**
     * @param $route
     * @param $endpoint
     * @param $via array
     */
    private static function post( $route, $endpoint, array $via = array('via' => NULL) ) {
        self::all($route, $endpoint, $via, 'POST');
    }

    /**
     * @param $route
     * @param $endpoint
     * @param $method
     * @param $via array
     * @throws \Exception
     */
    private static function all($route, $endpoint, array $via = array('via' => NULL), $method = 'ALL' ) {

        if (is_callable($endpoint)) {
            self::$routes[] = [
                'route' => $route,
                'controller' => 'callable',
                'action' => $endpoint,
                'method' => $method,
                'via' => $via['via']
            ];
        }
        else {
            $actionController = explode('@', $endpoint);
            if (!isset($actionController[0]) || !isset($actionController[1])) {
                throw new \Exception("Bad configuration on your routes file near $endpoint");
            }
            self::$routes[] = [
                'route' => $route,
                'controller' => $actionController[0],
                'action' => $actionController[1],
                'method' => $method,
                'via' => $via['via']
            ];
        }
    }


    /**
     * @param $route
     * @param $controller string
     * @param $actions array
     * @throws \Exception
     */
    public static function resources( $route, $controller, array $actions) {
        foreach( $actions as $methods ) {
            foreach ($methods as $method => $action) {
                $method = strtoupper($method);

                if ($method !== 'GET' && $method !== 'POST' && $method !== 'ALL') {
                    throw new \Exception("Invalid method $method");
                }
                foreach ($action as $v) {
                    $path = ($v === 'index') ? '' : "/" . $v;
                    $thisRoute = "$route$path";
                    $endpoint = "$controller@$v";
                    self::all($thisRoute, $endpoint, [ 'via' => $route . '_' . $v ], $method);
                }
            }
        }
    }

    /**
     * @param $closure
     */
    public static function missing( callable $closure ) {
        self::$missing = $closure;
    }


    /**
     * @return array
     */
    public function parseURL() {
        if ( isset($_GET['url'] )) {
            $this->uri = rtrim($_GET['url'], '/');

            $url = explode( '/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL ));

            $GLOBALS['arUrl'] = $url;
            $GLOBALS['url'] = $this->uri;
            unset($_GET['url']);
        }
        else {
            $this->uri = $url[0] = '/';
        }
        return $url;
    }

    /**
     * @return bool
     */
    public function parseRoutes()
    {
        $result = false;
        $pattern = [ '/{id}/', '/\{(!(id)|[^}]+)}/' ];
        $replace = [ '[0-9]+', '(?!\d+).+' ];

        $method = $_SERVER['REQUEST_METHOD'];
        $usePattern = true;

        foreach (self::$routes as $k => $v) {
            $key = preg_replace_callback($pattern,
                function ($match) use ($pattern, $replace) {
                    return preg_replace($pattern, $replace, $match[0]);
                },
                $v['route']
            );

            if ( $key !== $v['route']) {
                self::$routes[$k]['pattern'] = $key;

                /** if we have a litteral match, let's assign controller and method already. */
            } elseif ( $v['route'] === $this->uri ) {
                $usePattern = false;
                if ( $method === $v['method'] || $v['method'] === 'ALL' ) {
                    $this->controller = $v['controller'];
                    $this->action = $v['action'];
                    unset($this->arUri[0]);
                    if (isset($this->arUri[1]) && $this->action === $this->arUri[1]) {
                        unset($this->arUri[1]);
                    }
                    $result = true;
                }
            }
        }

        /**
         * @var  $k
         * @var  $g
         * here we will match patterns and only search for routes with patterns..
         */
        if ( $usePattern ) {
            foreach (self::$routes as $k => $g) {
                if (isset($g['pattern']) && preg_match("@" . $g['pattern'] . "$@i", $this->uri)) {
                    if ($method === $g['method'] || $g['method'] === 'ALL') {
                        $this->controller = $g['controller'];
                        $this->action = $g['action'];
                        unset($this->arUri[0]);
                        if (isset($this->arUri[1]) && $this->action === $this->arUri[1]) {
                            unset($this->arUri[1]);
                        }
                    }
                    $result = true;
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * @return void
     */
    private function includeRouter()
    {
        $routeFile = STORAGE_PATH . 'routes/route';

        if ( !file_exists( $routeFile ) || ( filemtime(ROUTER_FILE) > filemtime($routeFile)) ) {

            if (!file_exists(STORAGE_PATH . 'routes')) {
                mkdir(STORAGE_PATH . 'routes');
            }

            $route = file_get_contents( ROUTER_FILE );

            Interpreter::extendInterpreter('Routes', 'self', true);
            $newFile = Interpreter::parseView($route);

            file_put_contents($routeFile, $newFile);

        }
        include $routeFile;
    }

    public static function getRoutesByAssocKey($key, $value)
    {
        $result = '';

        foreach( self::$routes as $route ) {
            if ( $route[$key] === $value) {
                $result = $route;
                break;
            }
        }
        return $result;
    }

    /**
     * @return array $routes
     */
    public static function getRoutes()
    {
        $routes = array();
        foreach( self::$routes as $route ) {
            $routes[] = [ 'Route' => $route['route'], 'Method' => $route['method'], 'Via' => $route['via'] ];
        }
        return $routes;
    }
}