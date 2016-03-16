<?php
/**
 * Created By: Jon Garcia
 * Date: 1/16/16
 */
namespace App\Core\Http;

use App\Core\Storage\File;

/**
 * Class Params
 * @package App\Core\Http
 */
class Params
{

    private $data;
    public $files;
    public $empty = true;
    public $request;

    public function __construct()
    {
        $this->data = json_decode(file_get_contents("php://input"), true);

        $_POST = (is_null($this->data) || empty($this->data)) ? $_POST : ($this->data);
        $this->data = null;
        $this->buildRequest();
    }

    /**
     * Builds up request by analysing dada inside $_ super globals
     */
    private function buildRequest()
    {
        $request = new Request();

        if ($_FILES) {
            foreach ($_FILES as $name => $param) {

                $file = new File($param['name']);
                $file->$name = $param;
            }
            $this->files = $file;
            $this->empty = false;
            foreach($_FILES as $key => $file) {
                $_POST[$key] = $file['name'];
            }
        }

        if ($_POST) {
            foreach ($_POST as $name => $param) {
                $request->$name = $param;
            }
            $this->empty = false;
        }
        elseif ($_GET) {
            foreach ($_GET as $name => $param) {
                $request->$name = $param;
            }
            $this->empty = false;
        }

        $this->request = $request;
    }

    /**
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        if (!isset($this->$property)) {
            if (isset($this->request->$property)) {
                return $this->request->$property;
            }
        }
        return NULL;
    }

    /**
     * @param $property
     * @param $value
     */
    public function __set($property, $value)
    {
        if (!isset($this->$property)) {
            $this->request->$property = $value;
        }
    }


    /**
     * @param $filename
     * @return bool
     */
    public function has_file($filename)
    {
        $result = false;

        if (isset($this->files->$filename )) {
            $file = $this->files->$filename;
            $result = $file['error'] === 0 ? TRUE : false;
        }
        return $result;
    }

    /**
     * Gets all params into array
     * @return array
     */
    public function all()
    {
        $result = [];
        foreach( $this->request as $property => $value) {
            $result[$property] = $value;
        }
        return $result;
    }
}