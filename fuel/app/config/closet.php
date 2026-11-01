<?php
return array(
    'categories' => array(
        'tops' => 'トップス',
        'bottoms' => 'ボトムス',
        'outer' => 'アウター',
        'shoes' => '靴',
        'other' => 'その他',
    ),

    'colors' => array(
        'white'  => array('白', '#FFFFFF'),      'black'  => array('黒', '#333333'),
        'gray'   => array('グレー', '#9CA3AF'),   'brown'  => array('ブラウン', '#7B5B3A'),
        'beige'  => array('ベージュ', '#D2C49C'), 'green'  => array('グリーン', '#7ED35F'),
        'blue'   => array('ブルー', '#0FB0E0'),   'purple' => array('パープル', '#A97FE0'),
        'yellow' => array('イエロー', '#FFC53A'), 'pink'   => array('ピンク', '#FF8EC7'),
        'red'    => array('レッド', '#FF0016'),   'orange' => array('オレンジ', '#FF7722'),
        'silver' => array('シルバー', 'linear-gradient(135deg,#eee,#aaa)'),
        'gold'   => array('ゴールド', 'linear-gradient(135deg,#e8d58a,#b89a30)'),
        'other'  => array('その他', 'conic-gradient(red,orange,yellow,green,blue,purple,red)'),
    ),

    'seasons' => array(
        'spring' => '春',
        'summer' => '夏',
        'autumn' => '秋',
        'winter' => '冬',
    ),

    'periods' => array(
        'all' => 'すべて',
        'under_6m' => '半年未満',
        '6m_to_1y' => '半年以上1年未満',
        '1y_to_3y' => '1年以上3年未満',
        'over_3y' => '3年以上',
        'unknown' => '不明',
    ),

    'upload' => array(
        'path'       => DOCROOT.'uploads/',
        'max_size'   => 2097152,   // 2MB
        'extensions' => array('jpg', 'jpeg', 'png', 'gif'),
        'mimes'      => array('image/jpeg', 'image/png', 'image/gif'),
    ),
    'password' => array('min_length' => 8),
);