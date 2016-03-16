<?php

WebForm::_for('\\App\\Models\\Profile');
Markup::before('div', 'profiles-form-wrapper');
Markup::before('div', 'well');
Markup::element('h2', null, 'Crear Perfil');
Markup::after();
WebForm::open('profiles', null, '/profiles/create', 'form-double-stack', 'false', 'POST');

WebForm::before('div', 'photo-group btn btn-info btn-file');
WebForm::field('file', 'file', ['class' => 'file-upload'], null, 'Adjuntar Foto');
WebForm::after();
//datos personales
WebForm::before('fieldset', 'panel panel-default');
WebForm::before('div', 'panel-heading');
Markup::element('h3', ['class' => 'form-section'], 'Datos Personales');
WebForm::after();
WebForm::before('div', 'panel-body');
WebForm::before();
WebForm::field('nombres', 'text', ['placeholder' => 'Nombre'] );
WebForm::after();

WebForm::before();
WebForm::field('apellidos', 'text', ['placeholder' => 'Apellidos'] );
WebForm::after();

WebForm::before();
WebForm::field('cedula', 'text', ['placeholder' => 'Cedula'] );
WebForm::after();

WebForm::before();
WebForm::field('email', 'text', ['placeholder' => 'Email'] );
WebForm::after();

WebForm::before();
WebForm::field('fecha_nacimiento', 'text', ['class' => 'datepicker form-control', 'placeholder' => 'Fecha De Nacimiento (12/31/2015)'] );
WebForm::after();

WebForm::before();
WebForm::field('nacionalidad', 'text', ['placeholder' => 'Nacionalidad'] );
WebForm::after();

WebForm::before();
WebForm::field('ocupacion', 'text', ['placeholder' => 'Ocupación'] );
WebForm::after();

WebForm::before('div', 'form-group form-small');
Markup::element('span', ['class' => 'radio-label'], 'Estado Civil:');
WebForm::field('estado-civil', 'radio', ['id' => 'casado', 'value' => 1, 'class' => 'radio-btn'], null, 'Casado' );
WebForm::field('estado-civil', 'radio', ['id' => 'soltero', 'value' => 2, 'class' => 'radio-btn'], null, 'Soltero');
WebForm::after();

WebForm::before('div', 'form-group form-small');
WebForm::field('tutor', 'text', ['placeholder' => 'Padre/Madre/Tutor'] );
WebForm::after();

WebForm::after();
WebForm::after('fieldset');
//datos de direccion
WebForm::before('fieldset', 'panel panel-default half-screen');
WebForm::before('div', 'panel-heading');
Markup::element('h3', ['class' => 'form-section'], 'Dirección & Teléfonos');
WebForm::after();
WebForm::before('div', 'panel-body');
WebForm::before('div', 'form-group');
WebForm::field('telefono_casa', 'text', ['placeholder' => 'Telefono Casa (809-555-5555)'] );
WebForm::after();

WebForm::before('div', 'form-group');
WebForm::field('telefono_celular', 'text', ['placeholder' => 'Telefono Celular (809-555-5555)'] );
WebForm::after();

WebForm::before('div', 'form-group');
WebForm::field('telefono_oficina', 'text', ['placeholder' => 'Telefono Oficina (809-555-5555)'] );
WebForm::after();

WebForm::before();
WebForm::field('direccion', 'text', ['placeholder' => 'Dirección'] );
WebForm::after();

WebForm::before();
WebForm::field('sector', 'text', ['placeholder' => 'Sector'] );
WebForm::after();

WebForm::before();
WebForm::field('provincia', 'text', ['placeholder' => 'Provincia'] );
WebForm::after();

$countries = getCountries('es');
WebForm::select('countries', $countries, ['placeholder' => 'Countries'], 'DO' );

WebForm::after();
WebForm::after('fieldset');

//rasgos
WebForm::before('fieldset', 'panel panel-default half-screen');
WebForm::before('div', 'panel-heading');
Markup::element('h3', ['class' => 'form-section'], 'Rasgos & Características Físicas');
WebForm::after();
WebForm::before('div', 'panel-body');
WebForm::before('div', 'form-group form-small');
WebForm::field('estatura', 'text', ['placeholder' => 'Estatura'] );
WebForm::after();

WebForm::before('div', 'form-group form-small');
WebForm::field('peso', 'text', ['placeholder' => 'Peso'] );
WebForm::after();

$optionPiel = ["blanca" => 'Blanca', "amarilla" => "Amarilla", "triguena" => "Trigueña", "morena" => "Morena", "negra" => "Negra"];
WebForm::select('color-piel', $optionPiel, ['placeholder' => 'Color de Piel' ] );

$optionPelo = [ "negro" => "Negro", "castano" => "Castaño", "claro-rubio" => "Claro/Rubio", "pelirrojo" => "Pelirrojo" ];
WebForm::select('color-pelo', $optionPelo, ['placeholder' => 'Color de Pelo' ] );

$optionOjos = [ "negros" => "Negros", "miel" => "Miel", "verdes" => "Verdes", "azules" => "Azules", "grises" => "Grises" ];
WebForm::select('color-ojos', $optionOjos, [ 'placeholder' => 'Color de Ojos' ] );

WebForm::before('div', 'form-group reset-margin');
Markup::element('span', ['class' => 'radio-label full-width'], 'Alguna Discapacidad?:');
WebForm::field('discapacidad', 'radio', ['id' => 'discapacidad-no', 'value' => 'si', 'class' => 'radio-btn'], null, 'Si' );
WebForm::field('discapacidad', 'radio', ['id' => 'discapacidad-no', 'value' => 'no', 'class' => 'radio-btn'], null, 'No' );
WebForm::after();

WebForm::before('div', 'form-group');
WebForm::field('discapacidad-si', 'text', ['placeholder' => 'De que Se Trata?'] );
WebForm::after();

WebForm::select('genero', [ 'hombre' => 'Hombre', 'mujer' => 'Mujer' ], ['placeholder' => 'Genero'] );

WebForm::after();
WebForm::after('fieldset');

//vestuario

WebForm::before('fieldset', 'panel panel-default half-screen');
WebForm::before('div', 'panel-heading');
Markup::element('h3', ['class' => 'form-section'], 'Vestuarios');
WebForm::after();
WebForm::before('div', 'panel-body');

WebForm::before();
Markup::element('span', ['class' => 'radio-label'], 'Camisa:');
WebForm::field('camisas', 'radio', ['id' => 'camisas-small', 'value' => 's', 'class' => 'radio-btn'], null, 'S' );
WebForm::field('camisas', 'radio', ['id' => 'camisas-medium', 'value' => 'm', 'class' => 'radio-btn'], null, 'M' );
WebForm::field('camisas', 'radio', ['id' => 'camisas-large', 'value' => 'l', 'class' => 'radio-btn'], null, 'L' );
WebForm::after();

WebForm::before();
Markup::element('span', ['class' => 'radio-label'], 'Blusa:');
WebForm::field('blusa', 'radio', ['id' => 'blusa-small', 'value' => 's', 'class' => 'radio-btn'], null, 'S' );
WebForm::field('blusa', 'radio', ['id' => 'blusa-medium', 'value' => 'm', 'class' => 'radio-btn'], null, 'M' );
WebForm::field('blusa', 'radio', ['id' => 'blusa-large', 'value' => 'l', 'class' => 'radio-btn'], null, 'L' );
WebForm::after();

WebForm::before('div', 'form-group form-small');
WebForm::field('pantalon', 'text', ['placeholder' => 'Pantalon ( W X I )'] );
WebForm::after();

WebForm::before('div', 'form-group form-small');
WebForm::field('calzado', 'text', ['placeholder' => 'Calzado'] );
WebForm::after();

WebForm::after();
WebForm::after('fieldset');
//done

//vestuario

WebForm::before('fieldset', 'panel panel-default half-screen');
WebForm::before('div', 'panel-heading');
Markup::element('h3', ['class' => 'form-section'], 'Disponibilidad');
WebForm::after();
WebForm::before('div', 'panel-body');

WebForm::before('div', 'form-group full-width');
Markup::element('span', ['class' => 'radio-label'], 'Disponibilidad:');
WebForm::field('disponibilidad[]', 'checkbox', ['id' => 'disponibilidad-dia', 'value' => 'dia', 'class' => 'radio-btn'], null, 'Dia' );
WebForm::field('disponibilidad[]', 'checkbox', ['id' => 'disponibilidad-tarde', 'value' => 'tarde', 'class' => 'radio-btn'], null, 'Tarde' );
WebForm::field('disponibilidad[]', 'checkbox', ['id' => 'disponibilidad-noche', 'value' => 'noche', 'class' => 'radio-btn'], null, 'Noche' );
WebForm::after();

WebForm::before('div', 'form-group full-width');
Markup::element('span', ['class' => 'radio-label'], 'Visa Estados Unidos:');
WebForm::field('visa', 'radio', ['id' => 'visa-si', 'value' => '1', 'class' => 'radio-btn'], null, 'Si' );
WebForm::field('visa', 'radio', ['id' => 'visa-no', 'value' => '0', 'class' => 'radio-btn'], null, 'No' );
WebForm::after();

WebForm::after();
WebForm::after('fieldset');
//done

//talentos

WebForm::before('fieldset', 'panel panel-default');
WebForm::before('div', 'panel-heading');
Markup::element('h3', ['class' => 'form-section'], 'Talentos & habilidades');
WebForm::after();
WebForm::before('div', 'panel-body');

WebForm::before();
WebForm::field('deportes', 'text', ['class' => 'form-control text-list', 'placeholder' => 'Deportes'] );
WebForm::after();

WebForm::before();
WebForm::field('bailes', 'text', ['class' => 'form-control text-list', 'placeholder' => 'Bailes'] );
WebForm::after();

WebForm::before();
WebForm::field('musica', 'text', ['class' => 'form-control text-list', 'placeholder' => 'Instrumentos Musicales'] );
WebForm::after();

WebForm::before();
WebForm::field('idiomas', 'text', ['class' => 'form-control text-list', 'placeholder' => 'Idiomas'] );
WebForm::after();

WebForm::after();
WebForm::after('fieldset');
//done
//experience
WebForm::before('fieldset', 'panel panel-default');
WebForm::before('div', 'panel-heading');
Markup::element('h3', ['class' => 'form-section'], 'Experiencia');
WebForm::after();
WebForm::before('div', 'panel-body');

WebForm::textarea('comerciales', [ 'class' => 'ckeditor', 'id' => 'comerciales' ], 'Comerciales Realizados');
WebForm::textarea('espectaculo', [ 'class' => 'ckeditor', 'id' => 'espectaculo' ], 'Espectáculos');
WebForm::textarea('programas-tv', [ 'class' => 'ckeditor', 'id' => 'programas-tv' ], 'Programas De TV');
WebForm::textarea('teatro', [ 'class' => 'ckeditor', 'id' => 'teatro' ], 'Teatro');
WebForm::textarea('restricciones', [ 'class' => 'ckeditor', 'id' => 'restricciones' ], 'Restricciones');

WebForm::after();
WebForm::after('fieldset');
//done

//agencia
WebForm::before('fieldset', 'panel panel-default');
WebForm::before('div', 'panel-heading');
Markup::element('h3', ['class' => 'form-section'], 'Misceláneos');
WebForm::after();
WebForm::before('div', 'panel-body');

WebForm::before();
Markup::element('span', ['class' => 'radio-label'], 'Pertenece A Alguna Agencia?:');
WebForm::field('agencia', 'radio', ['id' => 'yes', 'value' =>'si', 'class' => 'radio-btn'], null, 'Si' );
WebForm::field('agencia', 'radio', ['id' => 'no', 'value' =>  'no', 'class' => 'radio-btn'], null, 'No' );
WebForm::after();

WebForm::before();
WebForm::field('nombre-agencia', 'text', ['placeholder' => 'Cual Agencia'] );
WebForm::after();

WebForm::before();
Markup::element('span', ['class' => 'radio-label'], 'Serias Extra?:');
WebForm::field('extra', 'radio', ['id' => 'extra-si', 'value' =>  'si', 'class' => 'radio-btn'], null, 'Si' );
WebForm::field('extra', 'radio', ['id' => 'extra-no', 'value' =>  'no', 'class' => 'radio-btn'], null, 'No' );
WebForm::after();

WebForm::after();
WebForm::after('fieldset');
//done

WebForm::before('div', 'submit-wrapper');
WebForm::submit();
WebForm::after();
WebForm::close();
Markup::after();

?>
