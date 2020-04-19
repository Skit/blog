<?php

use blog\entities\post\Comment;

return  [
    [
        'creator_id' => 1,
        'content' => 'Comment one',
        'status' => Comment::STATUS_ACTIVE,
    ],
    [
        'creator_id' => 1,
        'parent_id' => 1,
        'content' => 'Comment two',
        'status' => Comment::STATUS_ACTIVE,
    ],
];