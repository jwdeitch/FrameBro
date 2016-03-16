<?php
/**
 * Created by Jon Garcia
 */
namespace App\Core\Http;

use App\Controllers\usersController;
use App\Core\Html\Validator;

/**
 * Class Controller
 * @package App\Core\Http
 */
class Controller extends Validator {
    /**
     * try to login with cookie
     */
    function __construct()
    {
        if (!$this->isLoggedIn() && isset($_COOKIE['login_cookie'])) {
            if ($this instanceof usersController) {
                $this->tryLoginWithCookie();
            }
            else {
                $user = new usersController();
                $user->tryLoginWithCookie();
            }
        }
    }

    /**
     * @return bool
     */
    protected function isLoggedIn()
    {
        if (isset($_SESSION['user_logged_in'])) {
           return true;
        }
        return false;
    }

    /**
     * @param $location
     */
    protected function redirect($location)
    {
        if ($location === 'home') {
            $location = '/';
        }
        header("location: $location");
    }

    /**
     * @param $dir
     * @return array
     */
    protected function getDirectoryFiles($dir)
    {
        $files = null;

        $relPath = str_replace(PUBLIC_PATH, '', $dir);

        if (file_exists($dir)) {
            foreach (scandir( $dir ) as $file) {
                if ($file !== '.' && $file !== '..') {
                    $files[] = $relPath . '/' . $file;
                }
            }
        }
        return $files;
    }
}