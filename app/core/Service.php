<?php

class Controller {
    protected $db;
    protected $view;
    protected $request;
    
    public function __construct() {
        $this->db = $this->getDatabase();
        //$this->view = new View();
        $this->request = $this->getRequest();
    }
    
    // Get database connection
    protected function getDatabase() {
        // Return your database connection
        // This will depend on your Database-conn.php setup
    }
    
    // Get request data (GET, POST, etc.)
    protected function getRequest() {
        return [
            'get' => $_GET,
            'post' => $_POST,
            'method' => $_SERVER['REQUEST_METHOD'],
            'uri' => $_SERVER['REQUEST_URI']
        ];
    }
    
    // Render a view
    protected function view($template, $data = []) {
        return $this->view->render($template, $data);
    }
    
    // Return JSON response
    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    // Redirect to another page
    protected function redirect($url) {
        header("Location: $url");
        exit;
    }
    
    // Check if user is authenticated
    protected function isAuthenticated() {
        // Check session/token
        return isset($_SESSION['user_id']);
    }
    
    // Get current user
    protected function getCurrentUser() {
        if ($this->isAuthenticated()) {
            $userModel = new User();
            return $userModel->find($_SESSION['user_id']);
        }
        return null;
    }
    
    // Check user role
    protected function hasRole($role) {
        $user = $this->getCurrentUser();
        return $user && $user['user_role'] === $role;
    }
    
    // Require authentication
    protected function requireAuth() {
        if (!$this->isAuthenticated()) {
            $this->redirect('/login');
        }
    }
    
    // Require specific role
    protected function requireRole($role) {
        $this->requireAuth();
        if (!$this->hasRole($role)) {
            $this->redirect('/unauthorized');
        }
    }
    
    // Validate input data
    protected function validate($data, $rules) {
        $errors = [];
        foreach ($rules as $field => $rule) {
            if (isset($rule['required']) && $rule['required'] && empty($data[$field])) {
                $errors[$field] = "$field is required";
            }
            if (isset($rule['email']) && $rule['email'] && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = "$field must be a valid email";
            }
            // Add more validation rules as needed
        }
        return $errors;
    }
    
    // Sanitize input
    protected function sanitize($data) {
        return array_map('htmlspecialchars', $data);
    }
}