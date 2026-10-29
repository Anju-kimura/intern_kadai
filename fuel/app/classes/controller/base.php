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
            $is_public = in_array(\Request::active()->action, $this->public_actions, true);
            if ( ! $is_public) {
                \Response::redirect('user/login');
            }
            return;    
        }

        list(, $this->user_id) = \Auth::get_user_id();
        $this->username = \Auth::get('username');
    }

    public function after($response)
    {
        $response = parent::after($response);
        $response->set_header('X-Frame-Options', 'DENY');

        return $response;
    }
}