<?php

global $arUrl; //array url
global $url; //string url

// Start below adding js and css files to all pages, or specific pages
self::add_js('https://code.jquery.com/jquery-1.11.2.js');
self::add_js('//code.jquery.com/ui/1.11.4/jquery-ui.js');
self::add_js('/framework/cuna.js');
self::add_js('/framework/file_upload.js');
self::add_js('//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js');
self::add_js('/plugins/ckeditor/ckeditor.js');
self::add_js('/plugins/ckeditor/config.js');
self::add_js('/themes/js/ckeditor_retrieve_content.js');

if ($arUrl[0] === 'users') {
	self::add_js('/themes/js/user_handler.js');
}

if ($arUrl[0] === 'doc') {
	self::add_js('/themes/js/new_cat.js');
}
if ($arUrl[0] === 'profiles') {
	self::add_js('/framework/textlist.js');
}


//Add css after this line.
self::add_css('//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css');
self::add_css('/themes/css/style.css');
self::add_css('//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css');
self::add_css('/themes/lightbox/css/lightbox.css');