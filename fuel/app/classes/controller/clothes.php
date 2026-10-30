<?php

class Controller_Clothes extends Controller_Base
{
    public function get_index()
    {
        $this->template->title = '衣類一覧';
        $this->template->content = '準備中です';
    }

    // 登録フォームを表示する
    public function get_create()
    {
        $this->show_form(array(), array());
    }

    // 登録フォームの送信を受け取る
    public function post_create()
    {
        $input   = \Input::post();
        $seasons = \Input::post('seasons', array());

        $val = $this->validation();
        if ( ! $val->run()) {
            $this->show_form($val->error_message(), $input);
            return;
        }

        if ( ! $this->valid_seasons($seasons)) {
            $this->show_form(array('seasons' => '季節の指定が正しくありません'), $input);
            return;
        }

        $data = $val->validated();
        // 「購入時期は不明」にチェックがあるときは、購入日を NULL にする
        if (\Input::post('purchase_unknown')) {
            $data['purchase_date'] = null;
        }

        \Model_Clothes::create($this->user_id, $data, array_unique($seasons));
        \Response::redirect('clothes');
    }

    private function show_form(array $errors, array $old)
    {
        $this->template->title = '衣類を登録する';
        $this->template->content = \View::forge('clothes/form', array(
            'errors'     => $errors,
            'old'        => $old,
            'categories' => \Config::get('closet.categories'),
            'colors'     => \Config::get('closet.colors'),
            'seasons'    => \Config::get('closet.seasons'),
        ));
    }

    private function validation()
    {
        $val = \Validation::forge();
        $val->add('name', '名称')
            ->add_rule('required')->add_rule('max_length', 100);
        $val->add('category', 'カテゴリ')
            ->add_rule('required')
            ->add_rule('match_collection', array_keys(\Config::get('closet.categories')));
        $val->add('brand', 'ブランド')
            ->add_rule('max_length', 100);
        $val->add('color', '色')
            ->add_rule('required')
            ->add_rule('match_collection', array_keys(\Config::get('closet.colors')));

        // 「購入時期は不明」にチェックがないときだけ、購入日を必須にする
        $purchase = $val->add('purchase_date', '購入日');
        if ( ! \Input::post('purchase_unknown')) {
            $purchase->add_rule('required')->add_rule('valid_date', 'Y-m-d');
        }

        $val->add('memo', 'メモ')
            ->add_rule('max_length', 500);

        return $val;
    }

    // 季節が、configにあるキーだけか確認する
    private function valid_seasons($seasons)
    {
        if ( ! is_array($seasons)) {
            return false;
        }
        $allowed = array_keys(\Config::get('closet.seasons'));

        return count(array_diff($seasons, $allowed)) === 0;
    }
}