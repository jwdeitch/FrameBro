<?php

if ( isset( $nombres ) ) {

    $data = unserialize($data);

    $mainPic = end($data['file']);

    Markup::before('div', 'profile-header'); {

        Markup::before('div', 'col-md-3');
        {
            Markup::before('a', null, ['href' => $mainPic, 'data-lightbox' => "thumbnail"]);
            {
                Markup::before('span', 'thumbnail-container');
                Markup::element('span', ['style' => 'background-image: url(' . $mainPic . ')', 'class' => 'thumbnail main-pic']);
                Markup::after('span');
            }
            Markup::after('a');
        }

        Markup::after('div');

        Markup::before('div', 'col-md-9'); {
            Markup::element('h2', ['class' => 'panel'], '<span>' . $nombres . ' ' . $apellidos . '</span>');
        }
        Markup::after('div');

        Markup::before('div', 'col-md-6');
        {
            $dob = new DateTime($fecha_nacimiento);
            $today = new DateTime('today');
            $reg = new DateTime($created_at);

            Markup::element('p', ['class' => 'profile-inline'], 'Fecha de registro: <span>' . $reg->format('M-d-Y') . '</span>');

            Markup::element('p', ['class' => 'profile-inline'], 'Cedula: ' . '<span>' . $cedula);
            Markup::element('p', ['class' => 'profile-inline'], 'Fecha De Nacimiento: <span>' . $dob->format('M-d-Y') . '</span>');
            Markup::element('p', ['class' => 'profile-inline'], 'Edad: <span>' . $dob->diff($today)->y . ' años' . '</span>');
        }
        Markup::after('div');

        Markup::before('div', 'col-md-3');
        {
            if (!empty($telefono_casa)) {
                Markup::element('p', ['class' => 'profile-inline'], 'Casa: <span>' . $telefono_casa . '</span>');
            }
            if (!empty($telefono_celular)) {
                Markup::element('p', ['class' => 'profile-inline'], 'Celular: <span>' . $telefono_celular . '</span>');
            }
            if (!empty($telefono_oficina)) {
                Markup::element('p', ['class' => 'profile-inline'], 'Oficina: <span>' . $telefono_oficina . '</span>');
            }
            Markup::element('p', ['class' => 'profile-inline'], 'Email: <span>' . $email . '</span>');
        }
        Markup::after('div');
    }
    Markup::after('div');


    Markup::before('div', 'images-container');
    {
        Markup::before('ul', 'thumbnails');
        {
            foreach ($data['file'] as $pic) {

                Markup::before('a', null, ['href' => $pic, 'data-lightbox' => "thumbnail"]);
                {
                    Markup::before('li', 'thumbnail-container');
                    {
                        Markup::element('span', ['style' => 'background-image: url(' . $pic . ')', 'class' => 'thumbnail']);
                    }
                    Markup::after('li');
                }
                Markup::after('a');
            }
        }
        Markup::after('ul');
    }
    Markup::after('div');

    // bottom section

    Markup::before('div', 'profiles-bottom-left col-md-3');
    {

        Markup::before('div', 'well profiles-bottom');
        {
            $marital = (intval($estado_civil) === 1) ? 'Casado' : 'Soltero';

            !empty($ocupacion) ? Markup::element('div', ['class' => 'profile-inline container-left'], 'Ocupación: <span>' . $ocupacion . '</span>') : '';
            !empty($nacionalidad) ? Markup::element('div', ['class' => 'profile-inline container-left'], 'Nacionalidad: <span>' . $nacionalidad . '</span>') : '';
            !empty($estado_civil) ? Markup::element('div', ['class' => 'profile-inline container-left'], 'Estado Civil: <span>' . $marital . '</span>') : '';
            !empty($direccion) ? Markup::element('div', ['class' => 'profile-inline container-left'], 'Dirección: <span>' . $direccion . '</span>') : '';
            !empty($data['sector']) ? Markup::element('div', ['class' => 'profile-inline container-left'], 'Sector: <span>' . $data['sector'] . '</span>') : '';
            !empty($data['provincia']) ? Markup::element('div', ['class' => 'profile-inline container-left'], 'Provincia: <span>' . $data['provincia'] . '</span>') : '';
            !empty($data['countries']) ? Markup::element('div', ['class' => 'profile-inline container-left'], 'Pais: <span>' . getCountryByCode($data['countries'], 'es') . '</span>') : '';

        }
        Markup::after('div');

        Markup::before('div', 'well profiles-bottom');
        {
            $discapacidad = (isset($data['discapacidad']) && !empty($data['discapacidad-si'])) ? $data['discapacidad-si'] : '';
            $visa = ( isset($data['visa']) && intval($data['visa']) === 1 ) ? "Si" : "No";
            $agencia = ($data['agencia'] === 'si' && !empty($data['nombre-agencia'])) ? $data['nombre-agencia'] : '';

            !empty($data['genero']) ? Markup::element('div', ['class' => 'profile-inline container-right'], 'Genero: <span>' . $data['genero'] . '</span>') : '';
            !empty($discapacidad) ? Markup::element('div', ['class' => 'profile-inline container-right'], 'Discapacidad: <span>' . $discapacidad . '</span>') : '';
            !empty($data['tutor']) ? Markup::element('div', ['class' => 'profile-inline container-right'], 'Tutor: <span>' . $data['tutor'] . '</span>') : '';
            Markup::element('div', ['class' => 'profile-inline container-left'], 'Visa a USA: <span>' . $visa . '</span>');
            !empty($agencia) ? Markup::element('div', ['class' => 'profile-inline container-right'], 'Nombre de Agente: <span>' . $agencia . '</span>') : '';

        }
        Markup::after('div');

        Markup::before('div', 'well profiles-bottom');
        {
            $topWear = isset($data['camisas']) ? 'Size Camisa: <span>' . $data['camisas'] . '</span>' : (isset($data['blusa']) ? 'Size Blusa: <span>' . $data['blusa'] . '</span>' : '');

            !empty($topWear) ? Markup::element('div', ['class' => 'profile-inline container-left'], $topWear) : '';
            !empty($data['pantalon']) ? Markup::element('div', ['class' => 'profile-inline container-left'], 'Size Pantalón: <span>' . $data['pantalon'] . '</span>') : '';
            !empty($data['calzado']) ? Markup::element('div', ['class' => 'profile-inline container-left'], 'Size Calzado: <span>' . $data['calzado'] . '</span>') : '';
            !empty($data['disponibilidad']) ? Markup::element('div', ['class' => 'profile-inline container-left'], 'Disponibilidad: <span>' . ucfirst(implode(', ', $data['disponibilidad'])) . '</span>') : '';

        }
        Markup::after('div');

        Markup::before('div', 'well small profiles-bottom icon-background sports');
        {
            !empty($data['deportes']) ? Markup::element('div', ['class' => 'profile-inline container-left'], 'Deportes: <span>' . implode(', ', $data['deportes']) . '</span>') : '';
        }
        Markup::after('div');
        Markup::before('div', 'well small profiles-bottom icon-background dance');
        {
            !empty($data['bailes']) ? Markup::element('div', ['class' => 'profile-inline container-left'], 'Bailes: <span>' . implode(', ', $data['bailes']) . '</span>') : '';
        }
        Markup::after('div');
        Markup::before('div', 'well small profiles-bottom icon-background music');
        {
            !empty($data['musica']) ? Markup::element('div', ['class' => 'profile-inline container-left'], 'Instrumentos: <span>' . implode(', ', $data['musica']) . '</span>') : '';
        }
        Markup::after('div');
        Markup::before('div', 'well small profiles-bottom icon-background language');
        {
            !empty($data['idiomas']) ? Markup::element('div', ['class' => 'profile-inline container-left'], 'Idiomas: <span>' . implode(', ', $data['idiomas']) . '</span>') : '';
        }
        Markup::after('div');

    }
    Markup::after('div');

    Markup::before('div', 'profiles-bottom-right well col-md-9');
    {
        !empty($data['comerciales']) ? Markup::element('div', ['class' => 'profile-inline container-right'], "<h4>Comerciales Realizados:</h4><hr>" . $data['comerciales']) : '';
        !empty($data['espectaculo']) ? Markup::element('div', ['class' => 'profile-inline container-right'], "<h4>Espectáculos:</h4><hr>" . $data['espectaculo']) : '';
        !empty($data['programas-tv']) ? Markup::element('div', ['class' => 'profile-inline container-right'], "<h4>Programas de TV:</h4><hr>" . $data['programas-tv']) : '';
        !empty($data['teatro']) ? Markup::element('div', ['class' => 'profile-inline container-right'], "<h4>Teatro:</h4><hr>" . $data['teatro']) : '';
        !empty($data['restricciones']) ? Markup::element('div', ['class' => 'profile-inline container-right'], "<h4>Restricciones:</h4><hr>" . $data['restricciones']) : '';

    }
    Markup::after('div');

}

?>