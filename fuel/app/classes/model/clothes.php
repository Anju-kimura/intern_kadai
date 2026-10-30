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
}