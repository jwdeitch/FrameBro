<?php
/**
 * Created By: Jon Garcia
 * Date: 1/16/16
 * Routes file.
 * Declare your routes here.
 */

Routes::get('/', 'home@index');

Routes::all('login', 'users@login', ['via' => 'login_path']);
Routes::get('users/logout', 'users@logout');
Routes::all('users/create', 'users@create');
Routes::all('users', 'users@index');
Routes::get('users/all', 'users@all');
Routes::get('users/{username}', 'users@index');
Routes::post('users/login', 'users@login');
Routes::all('users/deleteCurrentUser', 'users@deleteCurrentUser');

///profiles
Routes::get('profiles', 'profiles@index');
Routes::get('profiles/search', 'profiles@search');
Routes::get('profiles/{id}', 'profiles@index');
Routes::get('profiles/{id}/edit', 'profiles@index');
Routes::all('profiles/create', 'profiles@create');

//ajax upload
Routes::post('a-upload', 'App\Core\Storage\FileUploads@jsonUpload');

//CKEDITOR
Routes::post('ckUploads', 'App\Core\Storage\FileUploads@CKimages');


Routes::get('testing/{name}', function($name) {
   echo ("hey there $name");
});

Routes::resources('admin', 'admin',
    [
        [ 'get' =>
            ['index', 'showRoutes', 'logs', 'info', 'statusReport']
        ],
        ['post' =>
            ['index']
        ]
    ]
);

Routes::missing( function()
{
    View::render('errors/error', 'The requested page doesn\'t exist', 404);
});

/**
* This route is part of the ajax framework.
* DO NOT TOUCH
**/
Routes::post('AjaxController', 'App\\Core\\Ajax\\AjaxController@jsonResponder');