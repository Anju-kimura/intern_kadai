<?php

class Controller_Base extends \Controller_Template
{
    protected $public_actions = array();

    protected $user_id = null;
    protected $username = null;

    public function before()
    {
        parent::before();
        \Config::load('closet', true);

        if ( ! \Auth::check()) {
            $action = \Request::active()->action;
            $is_public = in_array($action, $this->public_actions, true);
            if ( ! $is_public) {
                // APIは、リダイレクトではなく401を返す
                if (strpos($action, 'api_') === 0) {
                    $this->respond_unauthorized();
                }
                \Response::redirect('user/login');
            }
            return;
        }

        list(, $this->user_id) = \Auth::get_user_id();
        $this->username = \Auth::get('username');
    }

    // 401とJSONを返して、処理をここで終える
    protected function respond_unauthorized()
    {
        $response = \Response::forge(
            json_encode(array('error' => 'unauthorized')),
            401,
            array(
                'Content-Type'    => 'application/json; charset=utf-8',
                'X-Frame-Options' => 'DENY',
            )
        );
        $response->send(true);
        exit;
    }

    // 文字列で受け取る欄の値を、まとめて取り出す。
    // 配列などが送られてきたら、空文字にする（そのままだと、PHPのエラーになるため）
    protected function post_strings(array $keys)
    {
        $values = array();
        foreach ($keys as $key) {
            $value = \Input::post($key, '');
            $values[$key] = is_string($value) ? $value : '';
        }

        return $values;
    }

    public function after($response)
    {
        $response = parent::after($response);
        $response->set_header('X-Frame-Options', 'DENY');
        // ログアウト後に「戻る」で、前の画面がキャッシュから表示されないようにする
        $response->set_header('Cache-Control', 'no-store');

        return $response;
    }
}