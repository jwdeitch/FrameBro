<?php
/**
 * Author: Jon Garcia
 */

namespace App\Controllers;

use App\Core\Html\Pagination;
use App\Core\Http\Controller;
use App\Core\Http\Params;
use App\Core\View;
use App\Models\Profile;

class profilesController extends Controller
{
    public function index($id = '')
    {
        if (!$this->isLoggedIn()) {
            View::render('errors/error', 'Permission Denied', 403);
            return;
        }

        if ( empty($id) ) {

            $params = new Params();

            $profiles = new Profile();
            $count = $profiles->count();
            $limit = 10;

            $pagination = new Pagination( $count, $limit, $params->page );
            $offset = $pagination->getOffset();
            $paginationLength = $pagination->getPaginationEnd();
            $paginationStart = $pagination->getPaginationStart();

            $profiles->find()->order('created_at DESC')->limit($limit)
                ->offset($offset)
                ->get([ 'nombres', 'apellidos', 'id', 'email' ])
                ->toArray();

            View::render( 'profiles/index',
                array(
                    "count"     => $count,
                    "profiles"  => $profiles->attributes,
                    'page'      => $pagination->page,
                    'pages'     => $pagination->total,
                    'length'    => $paginationLength,
                    'start'     => $paginationStart
                )
            );

        } else {

            $profile = new Profile();
            $profile->where('id', $id)->get();

            if ($profile->count != 1) {
                View::render('errors/error', 'Page not found', 404);
            }
            View::render('profiles/profiles_show', $profile->attributes);
        }
    }

    public function create( $ajaxParams = null )
    {
        if (!$this->isLoggedIn()) {
            View::render('errors/error', 'Permission Denied', 403);
            return;
        }

        $params = new Params();
        $arParams = $params->all();
        $this->validate($params, [
            'file'               => ['required', 'message' => 'Se requiere al menos una foto'],
            'nombres'            => ['required'],
            'apellidos'          => ['required'],
            'cedula'             => ['required', 'regex:^\d{3}-\d{7}-\d$', 'unique:profiles'],
            'email'              => ['required', 'email', 'unique:profiles'],
            'fecha_nacimiento'   => ['required', 'date'],
            'nacionalidad'       => ['any'],
            'estado-civil'       => ['any'],
            'direccion'          => ['any'],
            'ocupacion'          => ['any'],
            'telefono_casa'      => ['requiredWithout:telefono_celular:telefono_oficina', 'phone', 'unique:profiles'],
            'telefono_celular'   => ['requiredWithout:telefono_casa:telefono_oficina', 'phone', 'unique:profiles'],
            'telefono_oficina'   => ['requiredWithout:telefono_celular:telefono_casa', 'phone', 'unique:profiles'],
            'estatura'           => ['any'],
            'peso'               => ['number'],
            'color-piel'         => ['any'],
            'color-pelo'         => ['any'],
            'color-ojos'         => ['any'],
        ]);

        if ($this->validated) {
            $profiles = new Profile();

            $objDOB = new \DateTime($arParams['fecha_nacimiento']);

            $arParams['fecha_nacimiento'] = $objDOB->format('Y-m-d');

            unset($this->validatable['file']);
            foreach ($this->validatable as $key => $field ) {
                if (isset($arParams[$key])) {
                    $property = str_replace('-', '_', $key);
                    $profiles->$property = $arParams[$key];
                    unset($arParams[$key]);
                }
            }

            foreach ( $arParams as $field => $value ) {
                $data[$field] = $value;
            }

            $profiles->data = serialize($data);

            if ($profiles->save()) {
                View::info('Perfil guardado correctamente.');
                View::AjaxRedirect('/profiles/create');
            }
            return true ;
        }
        $this->displayErrors();

        View::AjaxCall(
            array(
                'callback' => 'create',
                'selector' => '.btn-primary',
                'wrapper' => '.profiles-form-wrapper',
            )
        );

        if (!is_null($ajaxParams)) {
            return View::renderAjax('profiles/profiles_new');
        }

        View::render('profiles/profiles_new');
    }

    /**
     * @throws \Exception
     */
    public function search() {

        if (!$this->isLoggedIn()) {
            View::render('errors/error', 'Permission Denied', 403);
            return;
        }

        $params = new Params();

        $query = $params->query;

        $profile = new Profile();

        $column = $this->matchInputToDBColumn($query);

        if ( $column === 'fecha_nacimiento' ) {
            $date = new \DateTime( $query );
            $value = $date->format('Y-m-d');
        } elseif ( $column === 'telefono' ) {
            $telefonos = array(
                'telefono_casa',
                'telefono_celular',
                'telefono_oficina'
            );
        }

        if ( isset($value) ) {

            $profile->where($column, $value);

        } elseif ( isset( $telefonos )) {

            $profile->where( $telefonos[0], $query )
                ->orWhere($telefonos[1], $query)
                ->orWhere($telefonos[2], $query);

        } elseif ($column === 'nombres') {

            $profile->where('nombres', "%$query%", 'LIKE' )->orWhere('apellidos', "%$query%", 'LIKE');

        } else {

            $profile->where( $column, $query );

        }

        $profile->get([ 'nombres', 'apellidos', 'id', 'email' ])->toArray();

        if ( isset( $profile->attributes['nombres']) ) {
            $data[] = $profile->attributes;
        } else {
            $data = $profile->attributes;
        }

        View::render( 'profiles/index', ['profiles' => $data ] );

    }

    /**
     * helper method
     * @param $field
     * @return string
     */
    private function matchInputToDBColumn($field) {

        if (filter_var($field, FILTER_VALIDATE_EMAIL)) {
            $column = 'email';
        } elseif ( preg_match('@^(?:1[0-2]|0?[1-9])/(?:3[01]|[12][0-9]|0?[1-9])/(?:[0-9]{2})?[0-9]{2}$@', $field )) {
            $column = 'fecha_nacimiento';
        } elseif ( preg_match('/^\d{3}-\d{7}-\d$/', $field )) {
            $column = 'cedula';
        } elseif ( preg_match('/^(?:\(\d{3}\)\s)?(?:\d{3}-)?\d{3}-\d{4}$/', $field )) {
            $column = 'telefono';
        } else {
            $column = 'nombres';
        }

        return $column;
    }
}