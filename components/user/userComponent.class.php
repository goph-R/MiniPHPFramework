<?php

class UserComponent {

    private $router;
    private $translation;
    private $view;
    
    public function __construct($im) {
        $im->add('userTable', new UserTable($im));
        $im->add('userService', new UserService($im));
        $this->router = $im->get('router');
        $this->translation = $im->get('translation');
        $this->view = $im->get('view');
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
        $this->translation->add('user', 'components/user/translations');
        $this->view->addPath('user', 'components/user/templates');
    }

}