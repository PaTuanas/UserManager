<?php

const _MODULE='home';
const _ACTION='dashboard';
const _CODE= true;

define('_WEB_HOST', 'http://'. $_SERVER['HTTP_HOST'] . '/UserManager');
define('_WEB_HOST_TEMPLATES', _WEB_HOST. '/templates');

define('_WEB_PATH', __DIR__);
define('_WEB_PATH_TEMPLATES', _WEB_PATH. '/templates');

//Connect DB
const _HOST = 'localhost';
const _DB = 'demophp';
const _USER = 'root';
const _PASS = '';
