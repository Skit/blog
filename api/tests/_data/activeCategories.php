<?php

use blog\entities\category\Category;
use blog\entities\common\MetaData;

// TODO переименовать в CategoriesActive
return  [
    [
        'title' => 'test category 1',
        'creator_id' => 1,
        'slug' => 'test_category 1',
        'content' => 'description 1',
        'meta_data' => null,
        'status' => Category::STATUS_ACTIVE,
    ],
    [
        'title' => 'test category 2',
        'creator_id' => 1,
        'slug' => 'test_category 2',
        'content' => 'description 2',
        'meta_data' => new MetaData('Сева тайтл', 'Сева описание', 'Сева ключевые'),
        'status' => Category::STATUS_ACTIVE,
    ]
];