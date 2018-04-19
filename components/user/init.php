<?php

$im = InstanceManager::getInstance();
$router = $im->get('router');
$translation = $im->get('translation');
$view = $im->get('view');

$im->add('userTableFactory', 'UserTableFactory');
$im->add('userService', 'UserService');
$im->add('userFormFactory', 'UserFormFactory');

$translation->add('user', 'components/user/translations');

$view->addPath('user', 'components/user/templates');

$router->add('login', 'LoginController', 'index');
$router->add('forgot', 'ForgotController', 'index');
$router->add('forgot/sent', 'ForgotController', 'sent');
$router->add('forgot/new/:hash', 'ForgotController', 'newPassword');
$router->add('forgot/success', 'ForgotController', 'success');
$router->add('logout', 'LogoutController', 'index');
$router->add('profile/:id', 'ProfileController', 'index');
$router->add('settings', 'UserSettingsController', 'index');
$router->add('settings/activate/:id/:hash', 'UserSettingsController', 'activate');
$router->add('register', 'RegisterController', 'index');
$router->add('register/activation', 'RegisterController', 'activation');
$router->add('register/activate/:hash', 'RegisterController', 'activate');
$router->add('register/success', 'RegisterController', 'success');        
