<?php

include "components/core/config.class.php";
include "components/core/router.class.php";
include "components/core/request.class.php";
include "components/core/response.class.php";
include "components/core/user.class.php";
include "components/core/view.class.php";
include "components/core/form.class.php";
include "components/core/validator.class.php";
include "components/core/validators/notEmptyValidator.class.php";
include "components/core/input.class.php";
include "components/core/inputs/textInput.class.php";
include "components/core/inputs/passwordInput.class.php";
include "components/core/inputs/hiddenInput.class.php";
include "components/core/db.class.php";
include "components/core/dbException.class.php";
include "components/core/dbResult.class.php";
include "components/core/column.class.php";
include "components/core/columns/integerColumn.class.php";
include "components/core/columns/stringColumn.class.php";
include "components/core/columns/booleanColumn.class.php";
include "components/core/table.class.php";
include "components/core/record.class.php";
include "components/core/controller.class.php";
include "components/core/webApplication.class.php";

include "components/welcome/WelcomeController.class.php";

include "components/user/userTable.class.php";
include "components/user/userModel.class.php";
include "components/user/loginForm.class.php";
include "components/user/loginController.class.php";
include "components/user/logoutController.class.php";
include "components/user/profileController.class.php";


$config = new Config();
$request = new Request();
$response = new Response();

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
