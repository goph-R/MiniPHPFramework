<?php

include_once "autoload.php";

$im = new InstanceManager();
$im->add('config', new Config($im));
$im->add('request', new Request($im));
$im->add('response', new Response($im));
$im->add('router', new Router($im));
$im->add('db', new DB($im));
$im->add('view', new View($im));
$im->add('user', new User($im));
$im->add('app', new WebApplication($im));

$config = $im->get('config');
$config->set('db.config', 'default');
$config->set('db.default.host', 'localhost');
$config->set('db.default.name', 'a');
$config->set('db.default.user', 'root');
$config->set('db.default.password', '');
$config->set('application.path', 'C:/xampp/htdocs/MiniPHPFramework/');
$config->set('router.parameter', 'route');
$config->set('router.rewrite', false);
$config->set('router.base', 'http://localhost/MiniPHPFramework/');
$config->set('router.default.controller', 'WelcomeController');
$config->set('router.default.method', 'index');
$config->set('view.template.extension', 'phtml');
$config->set('user.salt', '!#user%$salt');

$im->init();

$router = $im->get('router');
$router->add('index', 'WelcomeController', 'index');
$router->add('login', 'LoginController', 'index');
$router->add('logout', 'LogoutController', 'index');
$router->add('profile/:id', 'ProfileController', 'index');

$im->get('app')->run();
$im->get('db')->close();
