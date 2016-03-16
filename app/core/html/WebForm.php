<?php
/**
 * Created By: Jon Garcia
 * Date: 1/16/16
 */
namespace App\Core\Html;

use App\Core\Http\Params;

/**
 * Class WebForm
 * @package App\Core\Html
 */
class WebForm extends Markup
{
    public static $values;
    public static $invalidFields = array();

    /**
     * @param $Model
     * TODO bind to a model
     */
    public static function _for($Model)
    {
//        $new = new $Model;
//        self::$values = $new->attributes;
    }

    /**
     * @param $field
     * @return string
     */
    private static function get($field)
    {
        $params = new Params();

        if ( $params->$field && $params->$field !== '') {
            return $params->$field;
        }
        elseif (isset(self::$values->$field)) {
            return self::$values->field;
        }
        return '';
    }

    /**
     * @param $field
     * @return string
     */
    public static function errorClass($field)
    {
        $result = '';
        if (in_array($field, self::$invalidFields)) {
            $result = ' has-error';
        }
        return $result;
    }

    /**
     * @param $name
     * @param null $id
     * @param null $action
     * @param null $class
     * @param bool|false $files
     * @param string $method
     */
    public static function open( $name, $id = null, $action, $class = null, $files = false, $method = 'POST' )
    {
        global $url;

        if ( is_null( $action ) && $url === 'AjaxController') {
            $params = new Params();
            $action = '/' . $params->ajax['url'];
        } else {
            $action = '/' . $url;
        }

        $class = is_null( $class ) ? 'form-block' : $class ;

        $encType = $files === TRUE ? ' enctype="multipart/form-data"' : '';
        $formStart = '';
        $formStart .= '<form name="' . $name . '" action="' . $action . '" method="' . $method . '" ' . $encType . ' accept-charset="utf-8"';
        $formStart .= 'class ="' . $class . '"';
        $formStart .= !is_null($id) ? 'id="' . $id .  '">' : '>';
        echo $formStart;
    }

    /**
     * @param $name
     * @param string $type
     * @param array $attributes
     * @param null $label
     * @param null $text
     */
    public static function field($name, $type = 'text', $attributes = array(), $label = null, $text = null)
    {
        $paramsName = rtrim( $name, '[]' );

        $fieldValue = self::get($paramsName);

        //is this a text-list js element?
        if (is_array($fieldValue) && $type !== 'checkbox' ) {
            $hiddenFields = '';
            foreach ( $fieldValue as $k => $v ) {
                $hiddenFields .= '<input type="hidden" name="' . $name.'[]' . '" value="' . $v . '">';
            }
            $fieldValue = '';
        } else { $hiddenFields = ''; }

        if (!isset($attributes['class'])) {
            $attributes['class'] = 'form-control';
        }
        $field = '';
        $field .= !is_null($label) ? '<label for="' . $name . '">' . $label . '</label>' : '';
	    $field .= '<input type="' . $type . '" name="' . $name . '" ';
        $field .= isset($attributes['value']) ? '' : 'value="' . $fieldValue . '"';
        foreach($attributes as $attribute => $value) {
            $field .= $attribute . '="' . $value . '"';
        }

        if ($type === 'radio' || $type === 'checkbox') {
            if ( isset($attributes['value']) && ( $fieldValue == $attributes['value'] ||
                    ( is_array($fieldValue) && in_array($attributes['value'], $fieldValue)))) {
                $field .= 'checked="checked"';
            }
        }
	    $field .= '>' . $text;
        $field .= $hiddenFields;
        echo $field;
    }

    /**
     * @param $name
     * @param array $attributes
     * @param $label
     */
    public static function textarea($name, $attributes = array(), $label)
    {
        if (!isset($attributes['class'])) {
            $attributes['class'] = 'ckeditor';
        }
        $textarea = '';
		$textarea .= '<label for="' . $name . '" class="">' . $label . '</label>';
		$textarea .= '<textarea name="' . $name . '"';
        foreach($attributes as $attribute => $value) {
            $textarea .= $attribute . '="' . $value . '"';
        }
		$textarea .= '>' . self::get($name) . '</textarea>';
        echo $textarea;
    }

    /**
     * @param $name
     * @param array $options
     * @param array $attributes
     * @param null $selected
     * @param null $label
     */
    public static function select($name, array $options, $attributes = array(), $selected = null, $label = NULL)
    {

        $selectedVal = isset($selected) ? $selected : self::get($name);

        if (!isset($attributes['class'])) {
            $attributes['class'] = 'form-control';
        }

        $attributes['class'] .= '';

        if (!isset($attributes['placeholder'])) {
            $attributes['placeholder'] = 'Select an option';
        }

        $selectOptions = '<div class="form-group">';
        $selectOptions .= isset($label) ? '<label for="' . $attributes['name'] . '">' . $label . '</label>' : '';
        $selectOptions .= '<select name="' . $name . '"';

        foreach($attributes as $attribute => $value) {
            $selectOptions .= $attribute . '="' . $value . '"';
        }
        $selectOptions .= '>';

        if ($selectedVal == '') {
            $selectOptions .= '<option value="" selected="selected">' . $attributes['placeholder'] . '</option>';

            foreach ($options as $key => $value) {
                $selectOptions .= '<option value=' . "$key" . '>' . $value . '</option>';
            }
        }
        else {
            $selectOptions .= '<option value="">' . $attributes['placeholder'] . '</option>';
            $selectOptions .= '<option value="' . $selectedVal . '"' . 'selected="selected">' . $options[$selectedVal] . '</option>';

            foreach ($options as $key => $value) {
                if ($value != $options[$selectedVal]) {
                    $selectOptions .= '<option value=' . "$key" . '>' . $value . '</option>';
                }
            }
        }
        $selectOptions .= '></select></div>';
        echo $selectOptions;
    }

    /**
     * @param string $value
     * @param string $classes
     */
    public static function submit($value = 'Submit', $classes = 'btn btn-primary')
    {
        echo '<button type="submit" class="' . $classes . '">' . $value . '</button>';
    }

    /**
     *
     */
    public static function close()
    {
        echo '</form>';
    }
}