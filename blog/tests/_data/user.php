<?php

use blog\entities\common\Date;
use blog\entities\user\User;

return [
    [
        'username' => 'bayer.hudson',
        'auth_key' => 'HP187Mvq7Mmm3CTU80dLkGmni_FUH_lR',
        //password_0
        'password_hash' => '$2y$13$EjaPFBnZOQsHdGuHI.xvhuDp1fHpo8hKRSk6yshqa9c5EG8s3C3lO',
        'password_reset_token' => 'ExzkCOaYc1L8IOBs4wdTGGbgNiG3Wz1I_1402312317',
        'created_at' => Date::getFormatNow(),
        'updated_at' => null,
        'status' => User::STATUS_ACTIVE,
        'email' => 'nicole.paucek@schultz.info',
    ],
    [
        'username' => 'bob',
        'auth_key' => 'HP187Mvq7Mmm3CTU80dLkGmni_FUH_lR',
        //password_0
        'password_hash' => '$2y$13$EjaPFBnZOQsHdGuHI.xvhuDp1fHpo8hKRSk6yshqa9c5EG8s3C3lO',
        'password_reset_token' => 'ExzkCOaYc1L8IOBs4wdTGGbgNiG3Wz1I_1402312316',
        'created_at' => Date::getFormatNow(),
        'updated_at' => null,
        'status' => User::STATUS_ACTIVE,
        'email' => 'bob@schultz.info',
    ],
];
