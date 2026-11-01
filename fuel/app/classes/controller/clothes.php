<?php

class Controller_Clothes extends Controller_Base
{
    // 衣類一覧の画面を表示する（一覧のデータは、JSがAPIから取得する）
    public function get_index()
    {
        $this->template->title = '衣類一覧';
        $this->template->content = \View::forge('clothes/index', array(
            'categories' => \Config::get('closet.categories'),
            'colors'     => \Config::get('closet.colors'),
            'seasons'    => \Config::get('closet.seasons'),
            'periods'    => $this->period_options(),
        ));
    }

    // 一覧をJSONで返す
    public function get_api_list()
    {
        $items = array();
        foreach (\Model_Clothes::search($this->user_id, $this->read_filters()) as $row) {
            $items[] = $this->present($row);
        }

        return $this->json(array('count' => count($items), 'items' => $items));
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
        $this->show_form();
    }

    // 編集フォームを表示する
    public function get_edit($id = null)
    {
        $row = \Model_Clothes::find_for_user((int) $id, $this->user_id);
        if ($row === null) {
            throw new \HttpNotFoundException;
        }

        $this->show_form($row['id'], $this->form_values($row));
    }

    // 衣類を論理削除する
    public function post_delete($id = null)
    {
        if (\Model_Clothes::soft_delete((int) $id, $this->user_id) === 0) {
            throw new \HttpNotFoundException;
        }

        \Session::set_flash('success', '削除しました');
        \Response::redirect('clothes');
    }

    // 登録と更新を、JSONで受け付ける。idがあれば更新、なければ登録
    public function post_api_save()
    {
        $id  = \Input::post('id', '');
        $row = null;
        if ($id !== '') {
            $row = \Model_Clothes::find_for_user((int) $id, $this->user_id);
            if ($row === null) {
                return $this->json(array('ok' => false, 'errors' => array('_all' => '衣類が見つかりません')), 404);
            }
        }

        list($data, $errors) = $this->read_input();
        $image_path = null;
        if (count($errors) === 0) {
            list($image_path, $image_error) = $this->save_image();
            if ($image_error !== '') {
                $errors['image'] = $image_error;
            }
        }
        if (count($errors) > 0) {
            return $this->json(array('ok' => false, 'errors' => $errors), 422);
        }

        $seasons = array_unique(\Input::post('seasons', array()));

        if ($row === null) {
            $data['image_path'] = $image_path;
            $new_id = \Model_Clothes::create($this->user_id, $data, $seasons);

            // 移動先の画面で、1回だけ表示する
            \Session::set_flash('success', '登録しました');

            return $this->json(array('ok' => true, 'id' => $new_id));
        }

        // 画像を選ばなかったときは、元の画像のままにする
        $data['image_path'] = $image_path !== null ? $image_path : $row['image_path'];
        \Model_Clothes::update($row['id'], $this->user_id, $data, $seasons);

        // 新しい画像に差し替えたときは、古いファイルを消す
        if ($image_path !== null) {
            $this->delete_image($row['image_path']);
        }

        \Session::set_flash('success', '更新しました');

        return $this->json(array('ok' => true, 'id' => $row['id']));
    }

    // DBの1行を、フォームの初期値の形に直す
    private function form_values(array $row)
    {
        return array(
            'id'               => (string) $row['id'],
            'name'             => $row['name'],
            'category'         => $row['category'],
            'brand'            => (string) $row['brand'],
            'color'            => $row['color'],
            'purchase_date'    => (string) $row['purchase_date'],
            'purchase_unknown' => $row['purchase_date'] === null,
            'seasons'          => \Model_Clothes::get_seasons($row['id']),
            'memo'             => (string) $row['memo'],
            'image_url'        => $this->present($row)['image_url'],
        );
    }

    // フォームの画面を表示する。値の入力と送信は、JS（Knockout.js）が行う
    private function show_form($id = null, array $values = array())
    {
        $is_edit  = $id !== null;
        $defaults = array(
            'id'               => '',
            'name'             => '',
            'category'         => '',
            'brand'            => '',
            'color'            => '',
            'purchase_date'    => '',
            'purchase_unknown' => false,
            'seasons'          => array(),
            'memo'             => '',
            'image_url'        => '',
        );
        $up = \Config::get('closet.upload');

        $this->template->title = $is_edit ? '衣類を編集' : '衣類を登録する';
        $this->template->content = \View::forge('clothes/form', array(
            'categories'   => \Config::get('closet.categories'),
            'colors'       => \Config::get('closet.colors'),
            'seasons'      => \Config::get('closet.seasons'),
            'initial'      => json_encode(array_merge($defaults, $values)),
            'rules'        => json_encode(array(
                'extensions' => $up['extensions'],
                'mimes'      => $up['mimes'],
                'max_size'   => $up['max_size'],
            )),
            'csrf_key'     => \Config::get('security.csrf_token_key', 'fuel_csrf_token'),
            'csrf_token'   => \Security::fetch_token(),
            'heading'      => $is_edit ? '衣類を編集' : '衣類を登録する',
            'submit_label' => $is_edit ? '更新' : '登録',
            'back_url'     => \Uri::create($is_edit ? 'clothes/view/'.$id : 'clothes'),
        ));
    }

    // 登録・編集で共通の入力チェック。戻り値: array(検証済みの値, エラーの配列)
    private function read_input()
    {
        $val = $this->validation();
        if ( ! $val->run()) {
            return array(array(), $val->error_message());
        }
        if ( ! $this->valid_seasons(\Input::post('seasons', array()))) {
            return array(array(), array('seasons' => '季節の指定が正しくありません'));
        }

        $data = $val->validated();
        // 「購入時期は不明」にチェックがあるときは、購入日を NULL にする
        if (\Input::post('purchase_unknown')) {
            $data['purchase_date'] = null;
        }

        return array($data, array());
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

    // URLのパラメータを検証して、検索条件にする。configにない値は、無視する（条件なし）
    private function read_filters()
    {
        $seasons = \Input::get('seasons', array());
        $seasons = is_array($seasons) ? array_filter($seasons, 'is_string') : array();

        return array(
            'category' => $this->allowed_key(\Input::get('category'), \Config::get('closet.categories')),
            'color'    => $this->allowed_key(\Input::get('color'), \Config::get('closet.colors')),
            // configの順に並べて、許可された季節だけを残す
            'seasons'  => array_values(array_intersect(array_keys(\Config::get('closet.seasons')), $seasons)),
            'period'   => $this->allowed_key(\Input::get('period'), $this->period_options()),
            'sort'     => \Input::get('sort') === 'asc' ? 'asc' : 'desc',
        );
    }

    // 値が、許可された配列のキーならそのまま返し、そうでなければ空文字にする
    private function allowed_key($value, array $allowed)
    {
        return (is_string($value) && array_key_exists($value, $allowed)) ? $value : '';
    }

    // 購入時期の選択肢。「すべて」は、条件なし（空文字）で表すので、allを除く
    private function period_options()
    {
        $periods = \Config::get('closet.periods');
        unset($periods['all']);

        return $periods;
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
            'view_url'       => \Uri::create('clothes/view/'.$row['id']),
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

    // 保存済みの画像ファイルを削除する。ファイル名の部分だけを使う
    private function delete_image($image_path)
    {
        if ($image_path === null || $image_path === '') {
            return;
        }

        $up   = \Config::get('closet.upload');
        $file = $up['path'].basename($image_path);
        if (is_file($file)) {
            unlink($file);
        }
    }

    // JSONのレスポンスを作る。POSTのときは、CSRFトークンの最新のものを添える
    private function json(array $body, $status = 200)
    {
        // GETでトークンを取り出すと、新しいトークンに作り直され、
        // 画面にあるフォーム(ログアウトなど)のトークンが使えなくなる
        if (\Input::method() === 'POST') {
            $body['token'] = \Security::fetch_token();
        }

        return \Response::forge(json_encode($body), $status, array('Content-Type' => 'application/json'));
    }

}