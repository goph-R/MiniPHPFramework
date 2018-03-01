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
$im->add('mailer', new Mailer($im));
$im->add('app', new WebApplication($im));

// app specific

$im->add('userTable', new UserTable($im));
$im->add('userService', new UserService($im));

$config = $im->get('config');
$config->load('config.ini.php', 'dev');

$im->init();

$router = $im->get('router');
$router->add('index', 'WelcomeController', 'index');
$router->add('login', 'LoginController', 'index');
$router->add('forgot', 'ForgotController', 'index');
$router->add('forgot/sent', 'ForgotController', 'sent');
$router->add('forgot/new/:hash', 'ForgotController', 'newPassword');
$router->add('forgot/success', 'ForgotController', 'success');
$router->add('logout', 'LogoutController', 'index');
$router->add('profile/view/:id', 'ProfileController', 'index');
$router->add('profile/edit/:id', 'ProfileController', 'edit');
$router->add('register', 'RegisterController', 'index');
$router->add('register/activation', 'RegisterController', 'activation');
$router->add('register/activate/:hash', 'RegisterController', 'activate');
$router->add('register/success', 'RegisterController', 'success');

$im->get('app')->run();
$im->get('db')->close();