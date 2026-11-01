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

    // 衣類を更新する。季節は、削除して入れ直す
    public static function update($id, $user_id, array $data, array $seasons)
    {
        \DB::start_transaction();
        try {
            \DB::update('clothes')->set(array(
                'name'          => $data['name'],
                'category'      => $data['category'],
                'brand'         => $data['brand'],
                'color'         => $data['color'],
                'purchase_date' => $data['purchase_date'],
                'memo'          => $data['memo'],
                'image_path'    => $data['image_path'],
                'updated_at'    => \DB::expr('NOW()'),
            ))
                ->where('id', $id)
                ->where('user_id', $user_id)
                ->where('deleted_at', 'is', null)
                ->execute();

            \DB::delete('clothing_seasons')->where('clothing_id', $id)->execute();
            foreach ($seasons as $season) {
                \DB::insert('clothing_seasons')->set(array(
                    'clothing_id' => $id,
                    'season'      => $season,
                ))->execute();
            }

            \DB::commit_transaction();
        } catch (\Exception $e) {
            \DB::rollback_transaction();
            throw $e;
        }
    }

    // 衣類を論理削除する。戻り値は、更新した行数（0なら、対象がない）
    public static function soft_delete($id, $user_id)
    {
        return \DB::update('clothes')
            ->set(array('deleted_at' => \DB::expr('NOW()')))
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->where('deleted_at', 'is', null)
            ->execute();
    }

    // 条件に合う、自分の衣類を取得する。条件は、すべてANDで足していく
    public static function search($user_id, array $filters)
    {
        $q = \DB::select()->from('clothes')
            ->where('user_id', $user_id)
            ->where('deleted_at', 'is', null);

        if ($filters['category'] !== '') {
            $q->where('category', $filters['category']);
        }
        if ($filters['color'] !== '') {
            $q->where('color', $filters['color']);
        }
        if (count($filters['seasons']) > 0) {
            // JOINだと「春・夏」の衣類が2行になるので、サブクエリで絞る
            $sub = \DB::select('clothing_id')->from('clothing_seasons')
                ->where('season', 'in', $filters['seasons']);
            $q->where('id', 'in', $sub);
        }
        static::where_period($q, $filters['period']);

        $direction = $filters['sort'] === 'asc' ? 'asc' : 'desc';
        $q->order_by('created_at', $direction)->order_by('id', $direction);
        
        return $q->execute()->as_array();
    }

    // 購入時期の条件を、日付の範囲として足す
    private static function where_period($q, $period)
    {
        $six_months  = \DB::expr('DATE_SUB(CURDATE(), INTERVAL 6 MONTH)');
        $one_year    = \DB::expr('DATE_SUB(CURDATE(), INTERVAL 1 YEAR)');
        $three_years = \DB::expr('DATE_SUB(CURDATE(), INTERVAL 3 YEAR)');

        switch ($period) {
            case 'under_6m':
                $q->where('purchase_date', '>', $six_months);
                break;
            case '6m_to_1y':
                $q->where('purchase_date', '<=', $six_months)
                  ->where('purchase_date', '>', $one_year);
                break;
            case '1y_to_3y':
                $q->where('purchase_date', '<=', $one_year)
                  ->where('purchase_date', '>', $three_years);
                break;
            case 'over_3y':
                $q->where('purchase_date', '<=', $three_years);
                break;
            case 'unknown':
                $q->where('purchase_date', 'is', null);
                break;
        }
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