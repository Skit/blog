<?php

use blog\entities\common\Date;
use blog\entities\tag\Tag;

return [
    [
        'title' => 'post',
        'slug' => 'post',
        'created_at' => Date::getFormatNow(),
        'status' => Tag::STATUS_ACTIVE,
    ],
    [
        'title' => 'some',
        'slug' => 'some',
        'created_at' => Date::getFormatNow(),
        'status' => Tag::STATUS_ACTIVE,
    ],
];