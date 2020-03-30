<?php

use blog\entities\common\Date;

return [
    [
        'user_id' => 1,
        'avatar_url' => 'google.com',
        'bio' => 'some bio text',
        'created_at' => Date::getFormatNow(),
        'updated_at' => null,
    ],
];