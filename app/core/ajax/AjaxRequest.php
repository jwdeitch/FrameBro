<?php
/**
* Author: Jon Garcia
* Date: 1/25/16
**/

namespace App\Core\Ajax;

class AjaxRequest
{
    private static $Queue = array();
    /**
    * @param $data array
    * ‘callback’ => ‘deleteUsers’, //the callback method to process the request. Must be declared within the callee class
    * 'selector' => '#deleteUser', //the id or class name of the element that triggers the ajax call
    * 'event' => 'click', //the type of jQuery event triggering the action {Defaults to click}
    * 'effect' => 'fadeIn', //the effect to be used when updating dom element {Defaults to show} @link https://api.jquery.com/category/effects/
    * 'wrapper' => '.element-parent', //the id or class name of the parent element where response will be added. i.e. #id, .class
    * ‘method’ => 'replaceWith' // the jquery method to use to insert the content in the dom. Defaults to {replaceWith} @link http://api.jquery.com/category/manipulation/
    * ‘customHandler’ => 'null' // if specified, upon ajax response, the response will be sent as argument to the specified javascript callback
    * 'http-method' => ‘get’ // the http request method to use. {defaults to post}
    *
    * @return string $output
    * @throws \Exception
    *
    **/
	public static function ajaxQueue(array $data)
	{
        global $url;
        $validateArray = [ 'callback', 'selector', 'wrapper'];

        foreach ( $validateArray as $k ) {
            if (!isset($data[$k])) {
                throw new \Exception("Missing key $k from array");
            }
        }
        $data['method'] = isset($data['method']) ? $data['method'] : 'replaceWith';
        $data['event'] = isset($data['event']) ? $data['event'] : 'click';
        $data['httpMethod'] = isset($data['http-method']) ? $data['http-method'] : 'POST';
        $data['effect'] = isset($data['effect']) ? $data['effect'] : 'show';

        $data['class'] = debug_backtrace()[2]['class'];
        $data['url'] = $url;
		self::$Queue[] = $data;
	}

    /**
     * @return string
     */
    public static function getAjaxObject() {

        $output = '<script>';
        $output .= '$.extend(c.settings, ';

        $arAjaxData['Ajax'] = self::$Queue;
        $output .= json_encode($arAjaxData);

        $output .= ');</script>';

        return $output;

    }
}
