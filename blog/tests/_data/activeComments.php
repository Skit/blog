<?php

use blog\entities\post\Comment;

return  [
    [
        'post_id' => 1,
        'creator_id' => 1,
        'content' => 'Comment one',
        'status' => Comment::STATUS_ACTIVE,
    ],
    [
        'post_id' => 1,
        'creator_id' => 1,
        'parent_id' => 1,
        'content' => 'Comment two',
        'status' => Comment::STATUS_ACTIVE,
    ],
];