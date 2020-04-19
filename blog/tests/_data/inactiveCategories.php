<?php

use blog\entities\category\Category;
use blog\entities\common\MetaData;

// TODO переименовать в CategoriesInActive
return  [
    [
        'title' => 'test category 3',
        'creator_id' => 1,
        'slug' => 'test_category 3',
        'content' => 'description 3',
        'meta_data' => null,
        'status' => Category::STATUS_INACTIVE,
    ],
    [
        'title' => 'test category 4',
        'creator_id' => 1,
        'slug' => 'test_category 4',
        'content' => 'description 4',
        'meta_data' => (new MetaData('Сева тайтл 4', 'Сева описание 4', 'Сева ключевые 4'))->__toString(),
        'status' => Category::STATUS_INACTIVE,
    ],
];