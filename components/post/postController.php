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
        $this->respondLayout(':core/layout', ':post/index');
    }

    public function view() {
        $id = $this->request->get('id');
        $post = $this->postService->findActiveById($id);
        if (!$post) {
            return $this->respond404();
        }
        $this->respondLayout(':core/layout', ':post/view');
    }

}