<?php
class Controller_Base extends Controller_Template
{
   protected $public_actions = array();
   protected $user_id = null; 

   public function before()
   {
    parent::before();
    \CConfig::load('closet', true);

    $this->user_id = \Session::get('user_id');
    if ( ! $this->user_id and ! in_array(\Request::active()->action, $this->public_actions)) {
        \Response::redirect('user/login');
    }
   }
}