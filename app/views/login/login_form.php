<?php
WebForm::_for('\\App\\Models\\User');
Markup::before('div', 'well');
Markup::element('h2', null, 'Iniciar Sesion');
Markup::after();
WebForm::open('login', null, '/login', 'col-md-7 col-md-offset-2', 'false', 'POST');
    WebForm::before();
        WebForm::field('username', 'text', ['placeholder' => 'Nombre De Usuario'] );
    WebForm::after();
    WebForm::before();
        WebForm::field('password', 'password', ['placeholder' => 'Contraseña'] );
    WebForm::after();
    WebForm::before();
        WebForm::submit();
    WebForm::after();
WebForm::close();
?>