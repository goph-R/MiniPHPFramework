<?php

class AdminSettingsController extends Controller {

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var UserFormFactory
     */
    protected $formFactory;

    /**
     * @var ConfirmScript
     */
    protected $confirmScript;

    public function __construct() {
        parent::__construct();
        $im = InstanceManager::getInstance();
        $this->userService = $im->get('userService');
        $this->formFactory = $im->get('userFormFactory');
        $this->view->set('adminMenu', $im->get('adminMenu'));
    }

    public function index() {
        if (!$this->user->hasPermission('admin')) {
            return $this->redirect();
        }
        $record = $this->userService->findById($this->user->get('id'));
        $form = $this->formFactory->createSettingsForm($record, false);
        if ($form->processInput()) {
            $messages = $this->save($form);
            if ($messages) {
                $this->user->setFlash('settings_messages', $messages);
                return $this->redirect('admin/settings');
            }
        }
        $form->setValue('old_password', '');
        $form->setValue('password', '');
        $form->setValue('password_again', '');
        $this->view->set('form', $form);
        return $this->respondLayout(':admin/layout', ':admin/settings');
    }

    protected function save(Form $form) {
        $messages = [];
        if ($form->getValue('old_password') && $form->getValue('password')) {
            $this->userService->changePassword($this->user->get('id'), $form->getValue('password'));
            $messages[] = $this->translation->get('user', 'password_changed');
        }
        $email = $form->getValue('email');
        $id = $this->user->get('id');
        if ($email != $this->user->get('email') &&
            $this->userService->saveNewEmail($id, $email)) {
            $hash = $this->user->get('new_email_hash');
            $this->userService->activateNewEmail($id, $hash);
            $messages[] = $this->translation->get('admin', 'new_email_was_set');
        }
        return $messages;
    }

}