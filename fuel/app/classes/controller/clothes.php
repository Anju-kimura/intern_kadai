<?php

class Controller_Clothes extends Controller_Base
{
    // 衣類一覧を表示する
    public function get_index()
    {
        $items = array();
        foreach (\Model_Clothes::get_list($this->user_id) as $row) {
            $items[] = $this->present($row);
        }

        $this->template->title = '衣類一覧';
        $this->template->content = \View::forge('clothes/index', array(
            'items'      => $items,
            'categories' => \Config::get('closet.categories'),
        ));
    }

    // 衣類の詳細を表示する
    public function get_view($id = null)
    {
        $row = \Model_Clothes::find_for_user((int) $id, $this->user_id);
        if ($row === null) {
            throw new \HttpNotFoundException;
        }

        $item = $this->present($row, \Model_Clothes::get_seasons($row['id']));

        $this->template->title = $item['name'];
        $this->template->content = \View::forge('clothes/view', array('item' => $item));
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

        list($image_path, $image_error) = $this->save_image();
        if ($image_error !== '') {
            $this->show_form(array(), $input, $image_error);
            return;
        }
        $data['image_path'] = $image_path;

        \Model_Clothes::create($this->user_id, $data, array_unique($seasons));
        \Response::redirect('clothes');
    }

    private function show_form(array $errors, array $old, $image_error = '')
    {
        $this->template->title = '衣類を登録する';
        $this->template->content = \View::forge('clothes/form', array(
            'errors'     => $errors,
            'old'        => $old,
            'categories' => \Config::get('closet.categories'),
            'colors'     => \Config::get('closet.colors'),
            'seasons'    => \Config::get('closet.seasons'),
            'image_error' => $image_error,
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

    // DBの値を、画面に出す形に整える（一覧・詳細・Step 8のJSONで使い回す）
    private function present(array $row, array $seasons = array())
    {
        $categories    = \Config::get('closet.categories');
        $colors        = \Config::get('closet.colors');
        $season_labels = \Config::get('closet.seasons');

        return array(
            'id'             => $row['id'],
            'name'           => $row['name'],
            'category_label' => $categories[$row['category']],
            'brand'          => $row['brand'] ?: '未設定',
            'color_name'     => $colors[$row['color']][0],
            'color_css'      => $colors[$row['color']][1],
            'purchase'       => $row['purchase_date'] ? date('Y年n月', strtotime($row['purchase_date'])) : '不明',
            'seasons'        => implode('・', array_map(function ($season) use ($season_labels) {
                return $season_labels[$season];
            }, $seasons)),
            'image_url'      => $row['image_path'] ? \Uri::base(false).'uploads/'.$row['image_path'] : '',
            'memo'           => (string) $row['memo'],
        );
    }

    // 画像を保存する。戻り値: array(保存したファイル名 または null, エラーメッセージ)
    private function save_image()
    {
        $file = \Input::file('image');
        if ($file === null || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return array(null, '');   // 画像は任意なので、選ばなければ何もしない
        }

        $up = \Config::get('closet.upload');
        $message = '画像は、'.implode('・', $up['extensions']).'形式の、'
            .round($up['max_size'] / 1024 / 1024).'MB以内のファイルを選んでください';

        \Upload::process(array(
            'path'           => $up['path'],
            'ext_whitelist'  => $up['extensions'],
            'mime_whitelist' => $up['mimes'],
            'max_size'       => $up['max_size'],
            'randomize'      => true,   // 保存ファイル名をランダムにする
        ));

        if (!\Upload::is_valid()) {
            return array(null, $message);
        }

        \Upload::save();
        $files = \Upload::get_files();
        if (empty($files[0]['saved_as'])) {
            return array(null, '画像を保存できませんでした');
        }

        return array($files[0]['saved_as'], '');
    }
}