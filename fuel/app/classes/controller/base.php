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

    public function after($response)
    {
        $response = parent::after($response);
        $response->set_header('X-Frame-Options', 'DENY');

        return $response;
    }
}