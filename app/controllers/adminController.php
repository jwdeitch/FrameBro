<?php
/**
 * Created by PhpStorm.
 * Author: Jon Garcia
 * Date: 1/18/16
 */

namespace App\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Params;
use App\Core\Http\Routes;
use App\Core\Storage\FileUploads;
use App\Core\View;

class adminController extends Controller
{
    /**
     * @throws \Exception
     */
    public function index()
    {
        $params = new Params();

        if ($this->isLoggedIn()) {

            $this->validate($params, [
                'image' => ['required']
            ]);

            if ($this->validated) {
                FileUploads::upload('image', 'images/uploads');
            }

            $files = $this->getDirectoryFiles( FILES_PATH . 'images/uploads');

            View::AjaxCall(
                array(
                    'callback' => 'deleteFiles',
                    'selector' => '.delete-file',
                    'event' => 'click',
                    'wrapper' => '#file-manager',
                )
            );

            View::render('admin/file_upload', $files);
        }
        else {
            $this->redirect('/');
        }
    }

    /**
     * @param $params
     * @return string
     * @throws \Exception
     */
    public function deleteFiles( ) {
        $params = new Params();
        $params = $params->all();
        if ( unlink(PUBLIC_PATH . $params['data-collect'] )) {
            View::info('File deleted successfully');
        } else {
            View::error('File could not be deleted, make sure you have the proper permissions.');
        }
        $files = $this->getDirectoryFiles( FILES_PATH . 'images/uploads');
        return View::renderAjax('admin/file_upload', $files);
    }

    /**
     *
     */
    public function showRoutes()
    {
        if ($this->isLoggedIn()) {
            !d(Routes::getRoutes());
        }
    }

    /**
     * @throws \Exception
     */
    public function logs()
    {
        if ($this->isLoggedIn()) {
            $log = STORAGE_PATH . '/logging/app-errors.log';
            if (file_exists($log)) {
                $errorTypes = ['notice', 'warning', 'fatal', 'parse'];
                $file = file($log);
                $file = array_reverse($file);

                $result = '<h2>PHP Errors</h2><ul class="error-log-list">';
                while (list($var, $val) = each($file)) {
                    foreach ($errorTypes as $pattern) {
                        if (preg_match('@' . $pattern . '@i', $val, $matches)) {
                            $class = strtolower($matches[0]);
                            $result .= '<li class="error-log-line ' . $class . '">' . ($var+1) . '-  ' . $val . '</li>';
                        }
                    }
                }
                $result .= '</ul>';
                View::render('admin/index', $result);
            }
        }
        else {
            View::render('errors/error', 'Access denied', '443');
        }
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function info()
    {
        if ($this->isLoggedIn()) {
            return phpinfo();
        } else {
            View::render('errors/error', 'Access denied', '443');
        }
    }

    /**
     * @throws \Exception
     */
    public function statusReport() {
        if ($this->isLoggedIn()) {
            $validPHP = (version_compare(PHP_VERSION, '5.5.9') === -1) ? false : true;
            $memcache = (class_exists('\Memcached'));
            $arrThingsToCheck = [
                'xattr' => [
                    'name' => 'xattr Extension',
                    'message' => 'Loaded',
                    'value' => getenv('XATTR_SUPPORT'),
                    'info' => ''
                ],
                'storageDirAccess' => [
                    'name' => 'Storage Directory',
                    'message' => 'Writable',
                    'value' => is_writable(STORAGE_PATH),
                    'info' => '| ' .STORAGE_PATH
                ],
                'filesDirAccess' => [
                    'name' => 'Files Directory',
                    'message' => 'Writable',
                    'value' => is_writable(FILES_PATH),
                    'info' => '| ' . FILES_PATH
                    ],
                'osExtendedAttr' => [
                    'name' => 'OS Extended Attributes',
                    'message' => 'Supported or Enabled',
                    'value' => getenv('XATTR_ENABLED'),
                    'info' => ''
                ],
                'phpVersion' => [
                    'name' => 'PHP Version',
                    'message' => 'Valid',
                    'value' => $validPHP,
                    'info' => '| Version: ' . PHP_VERSION
                ],
                'Memcached' => [
                    'name' => 'Memcached Extension',
                    'message' => 'Loaded',
                    'value' => $memcache,
                    'info' => ''
                ]
            ];

            $result = '<h2>Status Report</h2><ul class="error-log-list">';

            foreach ($arrThingsToCheck as $v) {
                if ($v['value']) {
                    $class = 'status-page passed';
                    $result .= '<li class="error-log-line ' . $class . '">' . $v['name'] . ' is ' . $v['message'] . ' ' . $v['info'] . '</li>';
                } else {
                    $class = 'status-page failed';
                    $result .= '<li class="error-log-line ' . $class . '">' . $v['name'] . ' not ' . $v['message'] . ' ' . $v['info'] . '</li>';
                }
            }
            $result .= '</ul>';
            View::render('admin/index', $result);
        } else {
            View::render('errors/error', 'Access denied', '443');
        }

    }
}