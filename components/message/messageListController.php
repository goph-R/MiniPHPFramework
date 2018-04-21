<?php

class MessageListController extends MessageController {
        
    public function index() {
        if (!$this->user->isLoggedIn()) {
            return $this->redirect();
        }
        $sent = mb_substr($this->router->getRoute(), -13) == 'messages/sent';
        $count = $this->messageService->findCountByUserIdAndSent($this->user->get('id'), $sent);
        $pagerRoute = $sent ? 'messages/sent' : 'messages';
        $pager = new Pager($pagerRoute, [
            'page' => $this->request->get('page', 0),
            'step' => $this->request->get('step', 10)
        ]);
        $pager->setCount($count);
        $limit = [$pager->getPage() * $pager->getStep(), $pager->getStep()];
        $messages = $this->messageService->findAllByUserIdAndSent($this->user->get('id'), $sent, $limit);
        $subtitle = $sent ? 'sent' : 'inbox';
        $this->view->set([
            'sent'           => $sent,
            'subtitle'       => $this->translation->get('message', $subtitle),
            'pager'          => $pager,
            'messages'       => $messages,
            'messageService' => $this->messageService
        ]);
        $this->confirmScript->add();
        $this->respondLayout(':core/layout', ':message/list');
    }
    
}