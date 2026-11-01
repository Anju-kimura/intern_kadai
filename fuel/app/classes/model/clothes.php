<?php

class Model_Clothes
{
    // 衣類と季節を、まとめて保存する。途中で失敗したら、全部なかったことにする
    public static function create($user_id, array $data, array $seasons)
    {
        \DB::start_transaction();
        try {
            list($clothing_id) = \DB::insert('clothes')->set(array(
                'user_id'       => $user_id,
                'name'          => $data['name'],
                'category'      => $data['category'],
                'brand'         => $data['brand'],
                'color'         => $data['color'],
                'purchase_date' => $data['purchase_date'],
                'memo'          => $data['memo'],
                'image_path'    => $data['image_path'],
            ))->execute();

            foreach ($seasons as $season) {
                \DB::insert('clothing_seasons')->set(array(
                    'clothing_id' => $clothing_id,
                    'season'      => $season,
                ))->execute();
            }
            \DB::commit_transaction();
        } catch (\Exception $e) {
            \DB::rollback_transaction();
            throw $e;
        }

        return $clothing_id;
    }

    // 自分の衣類だけ、削除済みを除いて、新しい順に取得する
    public static function get_list($user_id)
    {
        return \DB::select()->from('clothes')
            ->where('user_id', $user_id)
            ->where('deleted_at', 'is', null)
            ->order_by('created_at', 'desc')
            ->execute()->as_array();
    }

    // 自分の、削除されていない衣類を、1件取得する。なければ null
    public static function find_for_user($id, $user_id)
    {
        $rows = \DB::select()->from('clothes')
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->where('deleted_at', 'is', null)
            ->execute()->as_array();

        return count($rows) === 0 ? null : $rows[0];
    }

    // その衣類の季節（キーの配列）を取得する
    public static function get_seasons($clothing_id)
    {
        $rows = \DB::select('season')->from('clothing_seasons')
            ->where('clothing_id', $clothing_id)
            ->execute()->as_array();

        return array_column($rows, 'season');
    }
}