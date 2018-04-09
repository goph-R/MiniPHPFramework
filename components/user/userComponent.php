<?php

class UserComponent implements Initiable {

    /**
     * @var Router
     */
    private $router;

    public function __construct() {
        $im = InstanceManager::getInstance();
        $this->router = $im->get('router');
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
    }

    public function init() {
        $this->router->add('login', 'LoginController', 'index');
        $this->router->add('forgot', 'ForgotController', 'index');
        $this->router->add('forgot/sent', 'ForgotController', 'sent');
        $this->router->add('forgot/new/:hash', 'ForgotController', 'newPassword');
        $this->router->add('forgot/success', 'ForgotController', 'success');
        $this->router->add('logout', 'LogoutController', 'index');
        $this->router->add('profile/view/:id', 'ProfileController', 'index');
        $this->router->add('profile/edit/:id', 'ProfileController', 'edit');
        $this->router->add('register', 'RegisterController', 'index');
        $this->router->add('register/activation', 'RegisterController', 'activation');
        $this->router->add('register/activate/:hash', 'RegisterController', 'activate');
        $this->router->add('register/success', 'RegisterController', 'success');
    }

}