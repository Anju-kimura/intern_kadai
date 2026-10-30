<?php

class Controller_User extends Controller_Base
{
    // ログインなしで開いてよいアクション
    protected $public_actions = array('login', 'signup');

    // 新規登録画面を表示する
    public function get_signup()
    {
        $this->show_signup(array(), array());
    }

    // 新規登録フォームの送信を受け取る
    public function post_signup()
    {
        $input = \Input::post();

        $val = $this->signup_validation();
        if ( ! $val->run()) {
            $this->show_signup($val->error_message(), $input);
            return;
        }

        $errors = array();
        if (\Model_User::exists_username($input['username'])) {
            $errors['username'] = 'このユーザー名は、すでに使われています';
        }
        if (\Model_User::exists_email($input['email'])) {
            $errors['email'] = 'このメールアドレスは、すでに登録されています';
        }
        if ($errors) {
            $this->show_signup($errors, $input);
            return;
        }

        try {
            \Auth::create_user($input['username'], $input['password'], $input['email']);
        } catch (\SimpleUserUpdateException $e) {
            $this->show_signup(array('email' => '登録できませんでした。入力内容を確認してください'), $input);
            return;
        }

        \Response::redirect('user/login');
    }

    private function show_signup(array $errors, array $old)
    {
        unset($old['password'], $old['password_confirm']);    // パスワードは、画面に戻さない
        $this->template->title = '新規登録';
        $this->template->content = \View::forge('user/signup', array(
            'errors' => $errors,
            'old'    => $old,
        ));
    }

    private function signup_validation()
    {
        $val = \Validation::forge();
        $val->add('username', 'ユーザー名')
            ->add_rule('required')->add_rule('max_length', 50);
        $val->add('email', 'メールアドレス')
            ->add_rule('required')->add_rule('valid_email')->add_rule('max_length', 255);
        $val->add('password', 'パスワード')
            ->add_rule('required')->add_rule('min_length', 8)
            ->add_rule('match_pattern', '/\A(?=.*[A-Za-z])(?=.*\d).+\z/');
        $val->add('password_confirm', 'パスワード（確認）')
            ->add_rule('required')->add_rule('match_field', 'password');
        return $val;
    }
}