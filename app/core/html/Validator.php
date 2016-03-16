<?php
/**
 * User: Jon Garcia
 * Date: 1/17/16
 */

namespace App\Core\Html;

use App\Core\Http\Params;
use App\Core\Model\Generic;
use App\Core\View;

/**
 * Class Validator
 * @package App\Core\Html
 */
class Validator
{
    private $request;
    public $validated;
    public $customMessage = array();
    public $validatable = array();
    private $invalidFields = array();

    public $errors = array();

    /**
     * @param Params $params
     * @param array $data
     */
    public function validate(Params $params, array $data)
    {
        if ($params->empty) {
            return;
        }
        $this->validated = true;
        $this->request = $params->request;

        $requiredCallables = [ 'required', 'requiredWithout' ];

        foreach($data as $field => $rule) {
            if (isset($rule['message'])) {
                $this->customMessage[$field] = $rule['message'];
                unset($rule['message']);
            }
            foreach($rule as $callable) {
                if (strpos($callable, ':')) {
                    $split = explode(':', $callable);
                    $callable = $split[0];
                    unset($split[0]);
                    $this->validatable[$field] = $field;
                    $asArray = array();
                    $asArray[] = $field;
                    foreach($split as $arg) {
                        $asArray[] = $arg;
                    }
                    if (!in_array($callable, $requiredCallables) &&
                        isset($this->request->{$asArray[0]}) && $this->request->{$asArray[0]} == "") {
                        continue;
                    }
                    call_user_func('self::'.$callable, $asArray);
                }
                else {
                    $this->validatable[$field] = $field;
                    if (!in_array($callable, $requiredCallables) &&
                        isset($this->request->$field) && $this->request->$field == "") {
                        continue;
                    }
                    call_user_func('self::'.$callable, $field);
                }
            }
        }
    }

    /**
     * @param $field
     * use 'field' => ['required']
     */
    protected function required($field)
    {
        if (!isset($this->request->$field) || $this->request->$field == '') {
            $this->errors[$field] = 'is required' ;
            $this->validated = false;
            WebForm::$invalidFields[] = $field;
        }
    }

    /**
     * @param array $field
     * use 'field' => ['minimum:8']
     */
    protected function minimum($field)
    {
        $minimum = (int) $field[1];
        if (strlen($this->request->{$field[0]}) < $minimum) {
            $this->errors[$field[0]] = ' requires at least ' . $minimum . ' characters';
            $this->validated = false;
            WebForm::$invalidFields[] = $field[0];
        }
    }

    /**
     * @param array $field
     * use 'field' => ['sameAs:anotherField']
     */
    protected function sameAs(array $field) {
        if ($this->request->{$field[0]} !== $this->request->{$field[1]}) {
            $this->errors[$field[0]] = 'must be equal to ' . self::keyToName($field[1]);
            $this->validated = false;
            WebForm::$invalidFields[] = $field[0];
        }
    }

    /**
     * @param $field
     * use 'field' => ['email']
     */
    protected function email($field)
    {
        if (!filter_var($this->request->$field, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = 'is not a valid email address';
            $this->validated = false;
            WebForm::$invalidFields[] = $field;
        }
    }

    /**
     * @param array $validationData
     * use 'field' => ['unique:table:column'] //if column is not sent, the field name will be used
     * @return bool
     */
    protected function unique(array $validationData) {
        $column = (isset($validationData[2])) ? $validationData[2] : $validationData[0];
        $value = $this->request->$column;
        $query = new Generic($validationData[1]);
        $query->where($column, $value)->get([$column]);
        if ($query->count !== 0) {
            $this->errors[$validationData[0]] =  'already exists';
            $this->validated = false;
            WebForm::$invalidFields[] = $validationData[0];
        }
    }

    /**
     * @param $field
     */
    protected function regex($field)
    {
        if ( !preg_match( "@$field[1]@", $this->request->{$field[0]} )) {
            $this->errors[$field[0]] = 'is not valid.';
            $this->validated = false;
            WebForm::$invalidFields[] = $field[0];
        }
    }

    /**
     * matches american phone number formats
     * i.e. 212-123-1234, (718) 123-1234
     * @param $field
     */
    protected function phone($field){
        $phoneRegExt = '^(?:\(\d{3}\)\s)?(?:\d{3}-)?\d{3}-\d{4}$';
        if (!preg_match_all("@$phoneRegExt@", $this->request->$field)) {
            $this->errors[$field] = 'is not a valid phone number.';
            $this->validated = false;
            WebForm::$invalidFields[] = $field;
        }
    }


    /**
     * matches a date
     * i.e. 12/31/2015, 2/5/15
     * @param $field
     */
    protected function date($field){
        $dateRegEx = '^(?:1[0-2]|0?[1-9])/(?:3[01]|[12][0-9]|0?[1-9])/(?:[0-9]{2})?[0-9]{2}$';
        if (!preg_match_all("@$dateRegEx@", $this->request->$field)) {
            $this->errors[$field] = 'is not a valid date.';
            $this->validated = false;
            WebForm::$invalidFields[] = $field;
        }
    }

    /**
     * matches any number
     * i.e. 9, 999, 035
     * @param $field
     */
    protected function number($field){
        $numberRegEx = '^[0-9]+$';
        if (!preg_match_all("@$numberRegEx@", $this->request->$field)) {
            $this->errors[$field] = 'must be a number';
            $this->validated = false;
            WebForm::$invalidFields[] = $field;
        }
    }

    /**
     * @param array $validationData
     */
    protected function requiredWithout(array $validationData)
    {
        $valid = false;
        $masterField = $validationData[0];
        unset($validationData[0]);

        if ($this->request->$masterField == '') {
            foreach ($validationData as $validatedDataAgainst) {
                if (!$this->request->$validatedDataAgainst == "") {
                    $valid = true;
                }
            }
            if (!$valid) {
                $this->errors[$masterField] = 'is required when ' . self::keyToName($validationData) . 'empty';
                $this->validated = false;
                WebForm::$invalidFields[] = $masterField;
            }
        }
    }

    /**
     * @param array $field
     * only calls calls the validation method when the field is set.
     */
    protected function whenPresent(array $field)
    {
        if (isset($this->request->{$field[0]})) {
            $callable = $field[1];
            unset($field[1]);
            $field = array_values($field);
            call_user_func("self::$callable", $field);
        }
    }

    /**
     * @return bool
     * use 'field' => ['any']
     * the purpose to any is to have a field that is always validated.
     * you can technically make any field validatable, if you want a field inside
     * the validatable array but don't need to validate the field, then call any.
     * May actually be deleted and caught on the validation method but leaving here so it makes more sense.
     */
    protected function any()
    {
        return true;
    }

    /**
     * @param $field
     */
    protected function isBool($field)
    {
        if (!is_bool($this->request->$field)) {
            $this->errors[$field] = 'must be a boolean';
            $this->validated = false;
            WebForm::$invalidFields[] = $field;
        }
    }

    /**
     * Just convert form fields from form convention to a more human convention.
     * i.e first-name will become First Name
     * @param $key
     * @return mixed
     */
    private static function keyToName($key)
    {
        if (is_array($key)) {
            $verb = (count($key) > 1) ? ' are ' : ' is ';
            $result = '';
            foreach ($key as $field) {
                $clean = str_replace('_', ' ', $field);
                $clean = str_replace('-', ' ', $clean);
                if (end($key) !== $field) {
                    $result .= $clean . ' ';
                } else {
                    $result .= '& ' . $clean . $verb;
                }
            }
        } else {
            $result = str_replace('_', ' ', $key);
            $result = str_replace('-', ' ', $result);
        }
        return ucwords($result);
    }

    /**
     * @return array
     */
    public function getErrors() {
        foreach ($this->errors as $field => $error) {
            $this->errors[$field] =
                isset($this->customMessage[$field]) ?
                    $this->customMessage[$field] :
                    self::keyToName($field) . ' ' . $this->errors[$field];

        }
        return $this->errors;

    }

    /**
     * @return bool
     */
    public function displayErrors() {
        foreach ($this->errors as $field => $error) {
            $this->errors[$field] =
                isset($this->customMessage[$field]) ?
                    $this->customMessage[$field] :
                    self::keyToName($field) . ' ' . $this->errors[$field];

            View::error($this->errors[$field]);

        }
        return true;
    }

    /**
     * @return string
     */
    public function getFirstError(){
        if ( $first = key($this->errors) ) {
            $error =
                isset($this->customMessage[$first]) ?
                    $this->customMessage[$first] :
                    self::keyToName($first) . ' ' . $this->errors[$first];
            return $error;
        }
    }

    /**
     *
     */
    public function displayFirstError(){
        if ( $first = key($this->errors) ) {
            $error =
                isset($this->customMessage[$first]) ?
                    $this->customMessage[$first] :
                    self::keyToName($first) . ' ' . $this->errors[$first];

            View::error($error);

            return true;
        }
    }
}