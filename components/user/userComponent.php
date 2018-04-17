<?php

class UserComponent {

    public function __construct() {
        $im = InstanceManager::getInstance();
        $router = $im->get('router');
        $translation = $im->get('translation');
        $translation->add('user', 'components/user/translations');
        $view = $im->get('view');
        $view->addPath('user', 'components/user/templates');
        $im->add('userTable', new UserTable());
        $im->add('permissionTable', new PermissionTable());
        $im->add('userPermissionTable', new UserPermissionTable());
        $im->add('userService', new UserService());
        $im->add('registerForm', 'RegisterForm');
        $im->add('forgotNewPasswordForm', 'ForgotNewPasswordForm');
        $im->add('userSettingsForm', 'UserSettingsForm');
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
    }

}