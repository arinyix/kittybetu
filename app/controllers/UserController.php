<?php
require_once __DIR__ . '/../models/User.php';
class UserController {
    private $dbConfig;
    public function __construct($dbConfig) {
        $this->dbConfig = $dbConfig;
    }
    public function list($params) {
        $page = max(1, (int)($params['page'] ?? 1));
        $q = trim($params['q'] ?? '');
        $users = User::paginate($this->dbConfig, $page, $q);
        require __DIR__ . '/../views/user/list.php';
    }
    public function show($id) {
        $user = User::find($this->dbConfig, $id);
        require __DIR__ . '/../views/user/show.php';
    }
    public function edit($id) {
        $user = User::find($this->dbConfig, $id);
        require __DIR__ . '/../views/user/edit.php';
    }
    public function update($id, $data) {
        $errors = validation_validateEdit($data, $this->dbConfig, $id);
        if ($errors) {
            $user = User::find($this->dbConfig, $id);
            require __DIR__ . '/../views/user/edit.php';
            return;
        }
        User::update($this->dbConfig, $id, $data);
        header('Location: /kittybetu/public/users/' . $id);
        exit;
    }
    public function changePasswordForm($id) {
        $user = User::find($this->dbConfig, $id);
        require __DIR__ . '/../views/user/change_password.php';
    }
    public function changePassword($id, $data) {
        $errors = validation_validatePassword($data['password'] ?? '');
        if ($errors) {
            $user = User::find($this->dbConfig, $id);
            require __DIR__ . '/../views/user/change_password.php';
            return;
        }
        User::changePassword($this->dbConfig, $id, $data['password']);
        header('Location: /kittybetu/public/users/' . $id);
        exit;
    }
    public function delete($id) {
        User::softDelete($this->dbConfig, $id);
        header('Location: /kittybetu/public/users');
        exit;
    }
}
