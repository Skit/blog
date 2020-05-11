<?php

use blog\entities\common\Date;
use blog\entities\common\MetaData;
use blog\entities\post\Post;
use blog\entities\post\PostBanners;

return [
    [
        'title' => 'bayer.hudson',
        'slug' => 'HP187Mvq7Mmm3CTU80dLkGmni_FUH_lR',
        'preview' => '$2y$13$EjaPFBnZOQsHdGuHI.xvhuDp1fHpo8hKRSk6yshqa9c5EG8s3C3lO',
        'content' => 'ExzkCOaYc1L8IOBs4wdTGGbgNiG3Wz1I_1402312317',
        'meta_data' => new MetaData(),
        'post_banners' => new PostBanners(),
        'category_id' => 1,
        'creator_id' => 1,
        'created_at' => Date::getFormatNow(),
        'published_at' => Date::getFormatNow(),
        'status' => Post::STATUS_ACTIVE,
    ],
];