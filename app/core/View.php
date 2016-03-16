<?php
/**
 * Class View
 * Created By: Jon Garcia
 * Provides the methods all views will have
 */

namespace App\Core;

use App\Core\Ajax\AjaxController;
use App\Core\Ajax\AjaxRequest;
use App\Core\Http\Routes;

/**
 * Class View
 * @package App\Core
 */
class View {

    /**
     * Including extra general purpose functions
     */
    static function init()
    {
        include_once(CORE_PATH . 'view_helpers/general_functions.php');
    }

    /**
     * @param $viaName
     * @return string representing the route
     */
    public static function getRoute($viaName )
    {
        $route = Routes::getRoutesByAssocKey('via', $viaName);

        return $route['route'];
    }

    /**
     * Render the views and includes no oop functions for easy access by the template files.
     * if no view is provided, then it will echo the data sent through within the body.
     * @param $view
     * @param array $data
     * @param $responseCode
     * @throws \Exception
     */
    public static function render( $view = null, $data = array(), $responseCode = 200 ) {
        if (is_null($view) || file_exists(VIEWS_PATH . $view . '.php')) {
            http_response_code($responseCode);
            self::init();
            self::includeView( $view, $data );
        }
        else {
            throw new \Exception('Calling view, but view does not exist');
        }
    }

    /**
     * @param $view
     * @param array $data
     * @return string
     * @throws Exception
     */
    public static function renderAjax($view, $data = array()) {

        if ( self::includeView($view, $data, true) ) {
            $file = STORAGE_PATH . 'views/ajax-' . str_replace('/', '.', $view);
            ob_start();
            self::init();
            include $file;
            $result = ob_get_clean();
            return $result;
        } else {
            throw new \Exception('Could not return view');
        }
    }

    /**
     * @param $view string
     * @param $data array
     * @param $ajax bool
     * @return bool || include file
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Parses view template and partials. Makes use of the xattr extended attributes library *
     * to parse partials when they have been updated and not their parent view.              *
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     */
    private static function includeView( $view, $data = null, $ajax = false )
    {
        //human keys in array become variables, $data still available
        if (!is_null($data) && (is_array( $data ) || is_object($data) )) {
            foreach ($data as $humanName => $value) {
                $$humanName = $value;
                if ( is_object( $data ) && isset( $data->attributes )) {
                    foreach ( $data->attributes as $attr => $val ) {
                        $$attr = $val;
                    }
                }
            }
        }

        self::statCache();

        /** @var $partial | used to tell weather we're rending a view or a template */
        static $partial = false;
        /** @var $partialProperties | stores the properties of a partial when in a partial rendering. */
        static $partialProperties = array();
        /** @var $parentView | stores the name of a partial's parent view when rendering a partial */
        static $parentView;

        if (!is_null($view)) {
            /** view file is the cached processed version of the view stored in storage dir
             * if is ajax view prepend text to viewFile name
             */
            if ($ajax) {
                $viewFile = STORAGE_PATH . 'views/ajax-' . str_replace('/', '.', $view);
            } else { $viewFile = STORAGE_PATH . 'views/' . str_replace('/', '.', $view); }

            /**  templateView is the original view file without being processed. */
            $templateView = VIEWS_PATH . $view . '.php';
            $masterTemplate = VIEWS_PATH . 'layouts/master.php';

            $viewExists = file_exists($viewFile);

            /** Do we support extended attributes? */
            if ( getenv('XATTR_ENABLED') && $viewExists ) {
                /**  get array partials form extended attributes */
                $arrPartials = json_decode( xattr_get( $viewFile, 'partials'), true ); //returns false if no extended attributes
                if ( $arrPartials && key($arrPartials) === $viewFile) {

                    /** @var  $partialProperties | tricky!
                     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
                     *    if we're rendering a partial, then we want the attributes untouched. If we're not rendering  *
                     *       a partial but it's parent view, and partials are up to date, we don't want to re-ren      *
                     *         der but we want to copy the attributes over to the new parent view that we're           *
                     *                                            rendering.                                           *
                     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
                     */
                    $partialProperties = $partial ? $partialProperties : $arrPartials;
                    foreach ($arrPartials[$viewFile] as $p) {
                        if ( filemtime( VIEWS_PATH . $p['templateView'] . '.php') > filemtime( $p['viewFile'] ) ) {
                            $partial = true;
                            self::includeView( $p['templateView'] );
                        }
                    }
                }
            }

            if ( !$viewExists || ( filemtime( $templateView ) > filemtime( $viewFile ))
                || ( filemtime( $masterTemplate ) > filemtime( $viewFile ))) {
                if (!file_exists(STORAGE_PATH . 'views')) {
                    mkdir(STORAGE_PATH . 'views');
                }

                $tmpView = file_get_contents($templateView);

                if (Interpreter::hasPartials($tmpView)) {
                    foreach (Interpreter::getPartials() as $file ) {
                        $parentView = $viewFile;
                        $partial = true;
                        //recursive call to render the partial
                        self::includeView($file);
                    }
                }
                /**
                 * if is ajax view make partial true so that it only parses the view and returns true;
                 */
                if ($ajax) {
                    $partial = true;
                }

                Interpreter::extendInterpreter('View', 'self', true);
                $newFile = Interpreter::parseView($tmpView, $ajax);

                /** we'd only get here if it's a partial, so we return as this should not be included. */
                if ( $partial ) {
                    file_put_contents( $viewFile, $newFile  );
                    $partialProperties[$parentView][] = [ 'viewFile' => $viewFile, 'templateView' => $view, ];
                    $partial = false;
                    $parentView = null;
                    return true;
                }

                $master = file_get_contents($masterTemplate);
                $fileToRender = Interpreter::parseLayout($master, $newFile);

                file_put_contents($viewFile, $fileToRender);
                /** can we write extended atributes? */
                if ( getenv('XATTR_ENABLED') && !empty( $partialProperties )) {
                    $xattrValue = json_encode($partialProperties);
                    xattr_set( $viewFile, 'partials', $xattrValue );
                }
            } elseif ( $partial ) {
                /** @var let's make sure we reset $partial so that we don't mistakenly process a view template as a partial $partial */
                $partial = false;
                /** @return | to calling function */
                return true;
            }
            /**
             * don't include file if ajax is true;
             */
            if ( $ajax ) {
                return true;
            } else {
                include $viewFile;
            }
        } else {
            echo $data;
        }
    }

    /**
     * @param array $data
     */
    public static function AjaxCall(array $data)
    {
       AjaxRequest::ajaxQueue($data);
    }


    /**
     * @param $location string
     */
    public static function AjaxRedirect($location )
    {
        AjaxController::$redirect = $location;
    }

    /**
     * clears php statcache @link http://php.net/manual/en/function.clearstatcache.php
     */
    private static function statCache() {
        if ( getenv('ENV') == 'dev' ) {
            clearstatcache();
        }
    }

    /**
     * @return bool
     */
    public static function isLoggedIn()
    {
        $result = isset($_SESSION['user_logged_in']) ? TRUE : false;
        return $result;
    }

    /**
     * @param $role
     * @return bool
     */
    public static function hasRole($role)
    {
        return self::is_user_role($role);
    }

    /**
     * @param $role
     * @return bool
     */
    public static function is_user_role($role)
    {
        $result = false;

        if (is_array($role)) {
           $result = !empty(array_intersect($role, $_SESSION['roles']));
        } elseif (in_array( $role, $_SESSION['roles'])) {
            $result = true;
        }
        return $result;
    }

    /**
     * @param $key
     * @return mixed
     * @throws \Exception
     */
    public static function getUser($key)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        else throw new \Exception('Unexisting property');
    }

    /**
     * renders feedbacks
     * echo out the feedback messages (errors and success messages etc.),
     * they are in $_SESSION["feedback_positive"] and $_SESSION["feedback_negative"]
     */
    public static function renderFeedbackMessages() {
        require_once(CORE_PATH . 'view_helpers/feedback.php');

        // delete these messages (as they are not needed anymore and we want to avoid to show them twice
        Session::set('feedback_positive', null);
        Session::set('feedback_negative', null);
    }

    /**
     * helper method for negative feedback
     * @param $message
     */
    public static function error($message)
    {
        self::feedback('error', $message);
    }

    /**
     * helper method for positive feedback
     * @param $message
     */
    public static function info($message)
    {
        self::feedback('success', $message);
    }

    /**
     * @param string $type
     * @param string $message
     */
    public static function feedback($type = 'notice', $message = 'Access Denied')
    {
        if ($type === 'notice' || $type === 'warning' || $type === 'error') {
            $_SESSION['feedback_negative'][] = $message;
        }
        if ($type === 'success') {
            $_SESSION['feedback_positive'][] = $message;
        }
    }

    /** Adds the specified js file within the @path variable to the specified uri
     * @param $path
     */
    public static function add_js($path) {
        echo $script = '<script src="' . $path .  '"></script>' . "\n";
    }
    /** Adds the specified js file within the @path variable to the specified uri
     * @param $path
     */
    public static function add_css($path) {
        echo '<link rel="stylesheet" href="' .$path. '">' . "\n";
    }
}

