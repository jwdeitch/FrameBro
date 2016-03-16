<?php
/**

 * Author: Jon Garcia
 * Date: 1/25/16
 * Time: 7:07 PM
 */

namespace App\Core\Ajax;

use App\Core\Http\Controller;
use App\Core\Http\Params;
use App\Core\JsonResponse;

/**
 * Class AjaxController
 * @package App\Core\Ajax
 */
class AjaxController extends Controller
{
    protected static $result;
    protected static $status;
    public static $redirect;

    /**
     * @output JsonResponse string
     */
    public function jsonResponder()
    {

        self::AjaxHandler();

        $redirect = ( !is_null(self::$redirect) ) ? [ 'redirect' => self::$redirect ] : null ;

        JsonResponse::Response( self::$result, self::$status, $redirect );

        return;
    }

    /**
     *
     */
    private static function AjaxHandler()
    {
        $params = new Params();
        $params = $params->all();

        if ( is_string( $params['ajax'] )) {

            $params['ajax'] = json_decode($params['ajax'], true);
        }

        $class = $params['ajax']['class'];

        $controller = new $class;

        self::flattenInput();

        if (method_exists($controller, $params['ajax']['callback'])) {
            try {
                self::$result = call_user_func_array(array($controller, $params['ajax']['callback']), array($params));
                self::$status = 200;
            } catch ( \Exception $e ) {
                self::$result = $e->getMessage();
                self::$status = 500;
            }
        }
        else {
            self::$result = 'Invalid callback method';
            self::$status = 400;
        }
        return;
    }

    /**
     *
     */
    private static function flattenInput()
    {
        if (isset($_POST['ajax'])) {
            unset($_POST['ajax']);
        } elseif (isset($_GET['ajax'])) {
            unset($_GET['ajax']);
        }

    }
}