<?php
class HomeController {
    private function render($view, $data = []) {
        if ($data) {
            extract($data);
        }
        
        $content = "../app/views/{$view}.php";
        require_once "../app/views/layouts/main.php";
    }

    public function index() {
        $this->render('home');
    }

    public function error404() {
        http_response_code(404);
        $this->render('404');
    }
}
