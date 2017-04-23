<?php

include_once "autoload.php";

$config = new Config();
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

$request = new Request();
$response = new Response();
$db = new DB($config);
$view = new View($config);
$user = new User($config, $request);
$router = new Router($config, $request);

$router->add('index', 'WelcomeController', 'index');
$router->add('login', 'LoginController', 'index');
$router->add('logout', 'LogoutController', 'index');
$router->add('profile/:id', 'ProfileController', 'index');

$app = new WebApplication($config, $router, $request, $response, $user, $db, $view);
$app->run();

$db->close();
