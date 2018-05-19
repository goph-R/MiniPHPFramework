<?php

class PostController extends Controller {

    /**
     * @var PostService
     */
    private $postService;

    public function __construct() {
        parent::__construct();
        $im = InstanceManager::getInstance();
        $this->postService = $im->get('postService');
    }

    public function index() {
        $locale = $this->request->get('locale');
        $count = $this->postService->findActiveCountByLocale($locale);
        $pager = new Pager('posts', [
            'page' => $this->request->get('page', 0),
            'step' => $this->request->get('step', 10)
        ]);
        $pager->setCount($count);
        $limit = [$pager->getPage() * $pager->getStep(), $pager->getStep()];
        $posts = $this->postService->findActiveByLocale($locale, $limit);
        $this->view->set([
            'pager'       => $pager,
            'posts'       => $posts,
            'postService' => $this->postService
        ]);
        $this->respondLayout(':core/layout', ':post/index');
    }

    public function view() {
        $id = $this->request->get('id');
        $post = $this->postService->findActiveById($id);
        if (!$post) {
            return $this->respond404();
        }
        $this->view->set([
            'post'        => $post,
            'postService' => $this->postService
        ]);
        $this->respondLayout(':core/layout', ':post/view');
    }

}