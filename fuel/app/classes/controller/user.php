<?php

class Controller_User extends Controller_Base
{
    // ログインなしで開いてよいアクション
    protected $public_actions = array('login', 'signup');

    public function before()
    {
        parent::before();

        // ログイン中に、ログイン・新規登録の画面を開いたら、一覧へ移動する
        if (\Auth::check() and in_array(\Request::active()->action, $this->public_actions, true)) {
            \Response::redirect('clothes');
        }
    }

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

    // ログイン画面を表示する
    public function get_login()
    {
        $this->show_login('', '');
    }

    // ログインフォームの送信を受け取る
    public function post_login()
    {
        $email = \Input::post('email', '');
        $password = \Input::post('password', '');

        if ($email === '' or $password === '') {
            $this->show_login('メールアドレスとパスワードを入力してください', $email);
            return;
        }

        if ( ! \Auth::login($email, $password)) {
            $this->show_login('メールアドレスまたはパスワードが違います', $email);
            return;
        }

        \Session::instance()->rotate();    // ログイン成功のあと、セッションIDを新しくする
        \Response::redirect('clothes');
    }

    // ログアウトして、ログイン画面へ戻る（POSTだけ受け付ける）
    public function post_logout()
    {
        \Auth::logout();
        \Response::redirect('user/login');
    }

    private function show_login($error, $email)
    {
        $this->template->title = 'ログイン';
        $this->template->content = \View::forge('user/login', array(
            'error' => $error,
            'email' => $email,
        ));
    }
}