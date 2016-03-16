<?php
WebForm::_for('\\App\\Models\\User');
WebForm::open('new-user', null, '/users/create', 'col-md-7 col-md-offset-2', 'false', 'POST', 'Crear Usuario');
    WebForm::before('div', 'form-group' . WebForm::errorClass('username'));
        WebForm::field('username', 'text', ['placeholder' => 'Username'] );
    WebForm::after();

	WebForm::before('div', 'form-group' . WebForm::errorClass('email'));
		WebForm::field('email', 'text', ['placeholder' => 'Email'] );
	WebForm::after();

	WebForm::before('div', 'form-group' . WebForm::errorClass('first-name'));
		WebForm::field('first-name', 'text', ['placeholder' => 'Name'] );
	WebForm::after();

	WebForm::before('div', 'form-group' . WebForm::errorClass('last-name'));
		WebForm::field('last-name', 'text', ['placeholder' => 'Last Name'] );
	WebForm::after();

	WebForm::before('div', 'form-group' . WebForm::errorClass('roles'));
		WebForm::select('roles', $data, [ 'placeholder' => 'Select a role'] );
	WebForm::after();

    WebForm::before('div', 'form-group' . WebForm::errorClass('password'));
        WebForm::field('password', 'password', ['placeholder' => 'Password'] );
    WebForm::after();
	WebForm::before('div', 'form-group' . WebForm::errorClass('repeat-password'));
		WebForm::field('repeat-password', 'password', ['placeholder' => 'Repeat Password'] );
	WebForm::after();
    WebForm::before();

        WebForm::submit();
    WebForm::after();
WebForm::close();
?>