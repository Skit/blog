<?php

use blog\entities\common\Date;
use blog\entities\common\MetaData;
use blog\entities\post\Post;
use blog\entities\post\PostBanners;

return [
    [
        'title' => 'bayer.hudson',
        'uuid' => 'f0d5b176-a8e3-40be-a78d-fc6bee14f2d6',
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
    [
        'title' => 'Highlighted post',
        'uuid' => '05adc5bc-8d6f-4257-b96f-4214aba5bc87',
        'slug' => 'highlighted',
        'preview' => 'highlighted highlighted highlighted highlighted highlighted',
        'content' => null,
        'zip_content' => gzcompress('<pre class="_lang__php"><?="Hello World";?></pre>', 1),
        'highlighted_content' => '<pre><code class="hljs php"><span class="hljs-meta">&lt;?</span>=<span class="hljs-string">"Hello World"</span>;<span class="hljs-meta">?&gt;</span></code></pre>',
        'meta_data' => new MetaData(),
        'post_banners' => new PostBanners(),
        'category_id' => 1,
        'creator_id' => 1,
        'created_at' => Date::getFormatNow(),
        'published_at' => Date::getFormatNow(),
        'status' => Post::STATUS_ACTIVE,
    ],
];