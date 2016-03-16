<?php

namespace App\Controllers;

use App\Core\Http\Controller;
use App\Core\View;
use App\Models\Role;
use App\Models\User;

class homeController extends Controller
{
	public function index ( ) {

//        $roles = new Role();
//
//        $roles->with('users')->where('roles.id', 2);
//
//        !ddd($roles->get());

		View::render('home/index');
	}
}
